from fastapi import APIRouter, Query, Request
import aiomysql

router = APIRouter(prefix="/api", tags=["words"])


def build_search_candidates(query, miq_matches, esp_matches, relation_map=None, relation_map_es=None):
    if relation_map is None:
        relation_map = {}
    if relation_map_es is not None:
        relation_map = {**relation_map, **relation_map_es}

    normalized_query = query.lower()
    exact_matches = []
    partial_matches = []
    relations = []

    def append_candidate(candidate_row, matches_bucket, lang_key, word_key, is_exact):
        rels = relation_map.get((lang_key, candidate_row["id"]), [])
        candidate = {
            "lang": lang_key,
            "id": candidate_row["id"],
            "word": candidate_row[word_key],
            "relations": rels,
            "is_exact_match": is_exact,
        }
        matches_bucket.append(candidate)
        relations.extend(rels)

    for row in miq_matches:
        is_exact = str(row.get("miskitoWord", "")).lower() == normalized_query
        if is_exact:
            append_candidate(row, exact_matches, "miq", "miskitoWord", True)
        else:
            append_candidate(row, partial_matches, "miq", "miskitoWord", False)

    for row in esp_matches:
        is_exact = str(row.get("spanishWord", "")).lower() == normalized_query
        if is_exact:
            append_candidate(row, exact_matches, "es", "spanishWord", True)
        else:
            append_candidate(row, partial_matches, "es", "spanishWord", False)

    candidates = exact_matches + partial_matches

    return {
        "input_word": query,
        "exact_matches": exact_matches,
        "partial_matches": partial_matches,
        "candidates": candidates,
        "relations": relations,
    }


@router.get("/word-relations")
async def word_relations(request: Request, word: str = Query(..., description="ミスキート語またはスペイン語の単語")):
    """
    入力された単語（ミスキート語またはスペイン語）に対応する
    - 対応する全ての目標言語の単語（リレーション）
    を返すAPI
    """
    pool = request.app.state.db_pool

    results = {
        "input_lang": None,
        "input_word": word,
        "relations": [],
        "candidates": [],
    }

    async with pool.acquire() as conn:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            search_pattern = f"%{word}%"
            await cur.execute(
                "SELECT id, miskitoWord FROM miskito_words WHERE LOWER(miskitoWord) LIKE LOWER(%s)",
                (search_pattern,),
            )
            miq_rows = await cur.fetchall()
            await cur.execute(
                "SELECT id, spanishWord FROM spanish_words WHERE LOWER(spanishWord) LIKE LOWER(%s)",
                (search_pattern,),
            )
            esp_rows = await cur.fetchall()

            relation_map = {}

            if miq_rows:
                for row in miq_rows:
                    await cur.execute(
                        """
                        SELECT r.id AS rel_id, s.id AS spanish_id, s.spanishWord, r.weight
                        FROM miq_esp_relations r
                        JOIN spanish_words s ON r.spanish_word_id = s.id
                        WHERE r.miskito_word_id = %s
                        """,
                        (row["id"],),
                    )
                    rels = await cur.fetchall()
                    relation_map[("miq", row["id"])] = [
                        {
                            "id": r["spanish_id"],
                            "word": r["spanishWord"],
                            "weight": r["weight"],
                            "relation_id": r["rel_id"],
                        }
                        for r in rels
                    ]

            if esp_rows:
                for row in esp_rows:
                    await cur.execute(
                        """
                        SELECT r.id AS rel_id, m.id AS miskito_id, m.miskitoWord, r.weight
                        FROM miq_esp_relations r
                        JOIN miskito_words m ON r.miskito_word_id = m.id
                        WHERE r.spanish_word_id = %s
                        """,
                        (row["id"],),
                    )
                    rels = await cur.fetchall()
                    relation_map[("es", row["id"])] = [
                        {
                            "id": r["miskito_id"],
                            "word": r["miskitoWord"],
                            "weight": r["weight"],
                            "relation_id": r["rel_id"],
                        }
                        for r in rels
                    ]

            if miq_rows or esp_rows:
                results.update(build_search_candidates(word, miq_rows, esp_rows, relation_map))
                if miq_rows and not esp_rows:
                    results["input_lang"] = "miq"
                    results["target_lang"] = "es"
                elif esp_rows and not miq_rows:
                    results["input_lang"] = "es"
                    results["target_lang"] = "miq"
                else:
                    results["input_lang"] = "mixed"
                    results["target_lang"] = None
            else:
                results["error"] = "Lo sentimos, no se encuentra la palabra que buscas."

            return results


@router.get("/word-relations-by-id")
async def word_relations_by_id(
    request: Request,
    lang: str = Query(..., description="起点言語コード（miq または es）"),
    word_id: int = Query(..., description="起点単語のID"),
):
    """
    入力: 言語コード(lang: 'miq' または 'es')、単語ID(word_id)
    - 起点がmiqなら、対応するスペイン語訳リスト+note/example
    - 起点がesなら、対応するミスキート語訳リスト
    """
    pool = request.app.state.db_pool
    results = {
        "source_lang": lang,
        "source_word_id": word_id,
        "source_word": None,
        "target_lang": None,
        "relations": [],
        "notes": [],
        "examples": [],
        "compose_words": [],
        "compose_words_relations": [],
    }

    async with pool.acquire() as conn:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            if lang == "miq":
                await cur.execute("SELECT miskitoWord FROM miskito_words WHERE id=%s", (word_id,))
                word_row = await cur.fetchone()
                results["source_word"] = word_row["miskitoWord"] if word_row else None
                results["target_lang"] = "es"

                if " " in results["source_word"]:
                    words = results["source_word"].split(" ")
                    compose_words_info = []
                    for word in words:
                        original_word = word
                        if word.endswith("i"):
                            candidate = word[:-1] + "aia"
                            await cur.execute(
                                "SELECT id FROM miskito_words WHERE miskitoWord=%s", (candidate,)
                            )
                            row = await cur.fetchone()
                            if row:
                                original_word = candidate
                        await cur.execute(
                            "SELECT id FROM miskito_words WHERE miskitoWord=%s", (original_word,)
                        )
                        row = await cur.fetchone()
                        word_id_value = row["id"] if row else None
                        compose_words_info.append({"miskitoWord": original_word, "id": word_id_value})
                    results["compose_words"] = compose_words_info

                await cur.execute(
                    """
                    SELECT
                        r.id AS rel_id,
                        s.id AS spanish_id,
                        s.spanishWord,
                        r.weight
                    FROM miq_esp_relations r
                    JOIN spanish_words s ON r.spanish_word_id = s.id
                    WHERE r.miskito_word_id = %s
                    """,
                    (word_id,),
                )
                rels = await cur.fetchall()
                results["relations"] = [
                    {
                        "id": r["spanish_id"],
                        "word": r["spanishWord"],
                        "weight": r["weight"],
                        "relation_id": r["rel_id"],
                    }
                    for r in rels
                ]

                if results["compose_words"]:
                    new_compose_words = []
                    for wordinfo in results["compose_words"]:
                        word = wordinfo["miskitoWord"]
                        word_id_value = wordinfo["id"]
                        await cur.execute(
                            """
                            SELECT s.id, s.spanishWord
                            FROM miq_esp_relations r
                            JOIN spanish_words s ON r.spanish_word_id = s.id
                            JOIN miskito_words m ON r.miskito_word_id = m.id
                            WHERE m.miskitoWord = %s
                            """,
                            (word,),
                        )
                        translations = await cur.fetchall()
                        new_compose_words.append(
                            {
                                "id": word_id_value,
                                "miskitoWord": word,
                                "translations": [
                                    {"id": t["id"], "word": t["spanishWord"]}
                                    for t in translations
                                ],
                            }
                        )
                    results["compose_words"] = new_compose_words

                await cur.execute("SELECT note FROM notes WHERE miskito_word_id=%s", (word_id,))
                notes = await cur.fetchall()
                results["notes"] = [n["note"] for n in notes] if notes else []

                await cur.execute(
                    "SELECT example_id FROM miq_ex_relations WHERE miskito_word_id=%s", (word_id,)
                )
                ex_ids = await cur.fetchall()
                example_ids = [row["example_id"] for row in ex_ids]

                examples = []
                if example_ids:
                    format_strings = ",".join(["%s"] * len(example_ids))
                    await cur.execute(
                        f"SELECT miskito_sentence, spanish_sentence FROM examples WHERE id IN ({format_strings})",
                        tuple(example_ids),
                    )
                    exs = await cur.fetchall()
                    examples = [
                        {
                            "miskito_sentence": e["miskito_sentence"],
                            "spanish_sentence": e["spanish_sentence"],
                        }
                        for e in exs
                    ]
                results["examples"] = examples

            elif lang == "es":
                await cur.execute("SELECT spanishWord FROM spanish_words WHERE id=%s", (word_id,))
                word_row = await cur.fetchone()
                results["source_word"] = word_row["spanishWord"] if word_row else None
                results["target_lang"] = "miq"

                await cur.execute(
                    """
                    SELECT
                        r.id AS rel_id,
                        m.id AS miskito_id,
                        m.miskitoWord,
                        r.weight
                    FROM miq_esp_relations r
                    JOIN miskito_words m ON r.miskito_word_id = m.id
                    WHERE r.spanish_word_id = %s
                    """,
                    (word_id,),
                )
                rels = await cur.fetchall()
                results["relations"] = [
                    {
                        "id": r["miskito_id"],
                        "word": r["miskitoWord"],
                        "weight": r["weight"],
                        "relation_id": r["rel_id"],
                    }
                    for r in rels
                ]

            else:
                results["error"] = "langは'miq'または'es'で指定してください"

            return results
