from fastapi import FastAPI, Query
from fastapi.middleware.cors import CORSMiddleware
import aiomysql
from dotenv import load_dotenv
import os

# .envファイルから環境変数を読み込む
load_dotenv()

# FastAPIアプリケーションの作成
app = FastAPI()

# CORS（クロスオリジンリソースシェアリング）の設定
# フロントエンド（Vue等）からAPIを呼ぶために必要。本番ではallow_originsを制限推奨。
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # 開発中は全許可。本番環境では限定推奨。
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


def require_env(name):
    value = os.getenv(name)
    if value is None:
        raise RuntimeError(f"環境変数 {name} が設定されていません")
    return value


# MariaDB用の接続情報
DB_CONFIG = {
    "host": os.getenv("DB_HOST"),
    "port": int(os.getenv("DB_PORT")),
    "user": os.getenv("DB_USER"),
    "password": os.getenv("DB_PASSWORD"),
    "db": os.getenv("DB_NAME"),
    "autocommit": True,
}

# アプリ起動時にDBコネクションプールを作成
@app.on_event("startup")
async def startup():
    # グローバルにDBプールを保存
    app.state.db_pool = await aiomysql.create_pool(**DB_CONFIG)

# アプリ終了時にDBプールを閉じる
@app.on_event("shutdown")
async def shutdown():
    app.state.db_pool.close()
    await app.state.db_pool.wait_closed()

# 単語検索APIエンドポイント
@app.get("/api/word-relations")
async def word_relations(
    word: str = Query(..., description="ミスキート語またはスペイン語の単語")
):
    """
    入力された単語（ミスキート語またはスペイン語）に対応する
    - 対応する全ての目標言語の単語（リレーション）
    を返すAPI
    """
    # DBコネクションプールを取得
    pool = app.state.db_pool

    # 結果格納用の辞書を初期化
    results = {
        "input_lang": None,   # "miq" or "es"
        "input_word": word,   # 入力単語
        "relations": [],      # 対応単語リスト
    }

    # DBからデータ取得
    async with pool.acquire() as conn:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            # 1. 入力単語がミスキート語かスペイン語か判定
            # ミスキート語テーブルで検索
            await cur.execute("SELECT id FROM miskito_words WHERE miskitoWord=%s", (word,))
            miq_row = await cur.fetchone()
            # スペイン語テーブルで検索
            await cur.execute("SELECT id FROM spanish_words WHERE spanishWord=%s", (word,))
            esp_row = await cur.fetchone()

            # 2. ミスキート語でヒットした場合
            if miq_row:
                results["input_lang"] = "miq"
                results["target_lang"] = "es"
                miq_id = miq_row["id"]

                # 2-1. リレーション（対応するスペイン語単語）を取得
                await cur.execute("""
                    SELECT r.id AS rel_id, s.id AS spanish_id, s.spanishWord, r.weight
                    FROM miq_esp_relations r
                    JOIN spanish_words s ON r.spanish_word_id = s.id
                    WHERE r.miskito_word_id = %s
                """, (miq_id,))
                rels = await cur.fetchall()
                results["relations"] = [
                    {
                        "id": r["spanish_id"],
                        "word": r["spanishWord"],
                        "weight": r["weight"],
                        "relation_id": r["rel_id"]
                    }
                    for r in rels
                ]

            # 3. スペイン語でヒットした場合
            elif esp_row:
                results["input_lang"] = "es"
                results["target_lang"] = "miq"
                esp_id = esp_row["id"]

                # 3-1. リレーション（対応するミスキート語単語）を取得
                await cur.execute("""
                    SELECT r.id AS rel_id, m.id AS miskito_id, m.miskitoWord, r.weight
                    FROM miq_esp_relations r
                    JOIN miskito_words m ON r.miskito_word_id = m.id
                    WHERE r.spanish_word_id = %s
                """, (esp_id,))
                rels = await cur.fetchall()
                results["relations"] = [
                    {
                        "id": r["miskito_id"],
                        "word": r["miskitoWord"],
                        "weight": r["weight"],
                        "relation_id": r["rel_id"]
                    }
                    for r in rels
                ]

            # 4. どちらにも該当しない場合
            else:
                results["error"] = "Lo sentimos, no se encuentra la palabra que buscas."

            # 5. 結果を返す（自動でJSON形式になる）
            return results



@app.get("/api/word-relations-by-id")
async def word_relations_by_id(
    lang: str = Query(..., description="起点言語コード（miq または es）"),
    word_id: int = Query(..., description="起点単語のID"),
):
    """
    入力: 言語コード(lang: 'miq' または 'es')、単語ID(word_id)
    - 起点がmiqなら、対応するスペイン語訳リスト+note/example
    - 起点がesなら、対応するミスキート語訳リスト
    """
    pool = app.state.db_pool
    results = {
        "source_lang": lang,
        "source_word_id": word_id,
        "source_word" : None,
        "target_lang": None,
        "relations": [],
        "notes": [],
        "examples": [],
        "compose_words": [],  # 2語以上の単語がある場合に使用
        "compose_words_relations": []  # compose_wordsの訳語リスト
    }

    async with pool.acquire() as conn:
        async with conn.cursor(aiomysql.DictCursor) as cur:

            if lang == "miq":

                # miskito_wordsから起点単語を取得
                await cur.execute("SELECT miskitoWord FROM miskito_words WHERE id=%s", (word_id,))
                word_row = await cur.fetchone()
                results["source_word"] = word_row["miskitoWord"] if word_row else None
                results["target_lang"] = "es"

                # 返ってきたmiskitoWordが２語からなる場合は、スペースで分割して配列"compose_words"に格納

                if " " in results["source_word"]:
                    words = results["source_word"].split(" ")
                    compose_words_info = []
                    for i, word in enumerate(words):
                        original_word = word
                        # 活用形チェック
                        if word.endswith("i"):
                            candidate = word[:-1] + "aia"
                            await cur.execute(
                                "SELECT id FROM miskito_words WHERE miskitoWord=%s", (candidate,)
                            )
                            row = await cur.fetchone()
                            if row:
                                original_word = candidate
                        # id取得
                        await cur.execute(
                            "SELECT id FROM miskito_words WHERE miskitoWord=%s", (original_word,)
                        )
                        row = await cur.fetchone()
                        word_id = row["id"] if row else None
                        compose_words_info.append({"miskitoWord": original_word, "id": word_id})
                    results["compose_words"] = compose_words_info


                # miq_esp_relationsから対応するスペイン語訳を取得
                await cur.execute("""
                    SELECT
                        r.id AS rel_id,
                        s.id AS spanish_id,
                        s.spanishWord,
                        r.weight
                    FROM miq_esp_relations r
                    JOIN spanish_words s ON r.spanish_word_id = s.id
                    WHERE r.miskito_word_id = %s
                """, (word_id,))
                rels = await cur.fetchall()
                results["relations"] = [
                    {
                        "id": r["spanish_id"],
                        "word": r["spanishWord"],
                        "weight": r["weight"],
                        "relation_id": r["rel_id"]
                    } for r in rels
                ]

                # compose_wordsがある場合には,それぞれの単語の訳語のリストを作成する
                if "compose_words" in results and results["compose_words"]:
                    new_compose_words = []
                    for wordinfo in results["compose_words"]:
                        word = wordinfo["miskitoWord"]
                        word_id = wordinfo["id"]
                        # その単語の訳語リストを取得
                        await cur.execute("""
                            SELECT s.id, s.spanishWord
                            FROM miq_esp_relations r
                            JOIN spanish_words s ON r.spanish_word_id = s.id
                            JOIN miskito_words m ON r.miskito_word_id = m.id
                            WHERE m.miskitoWord = %s
                        """, (word,))
                        translations = await cur.fetchall()
                        new_compose_words.append({
                            "id": word_id,
                            "miskitoWord": word,
                            "translations": [
                                {"id": t["id"], "word": t["spanishWord"]}
                                for t in translations
                            ]
                        })
                    results["compose_words"] = new_compose_words

                # notes取得
                await cur.execute("SELECT note FROM notes WHERE miskito_word_id=%s", (word_id,))
                notes = await cur.fetchall()
                results["notes"] = [n["note"] for n in notes] if notes else []

                # examples取得（miq_ex_relations経由でexample_idを取得し、examplesテーブルから文を取得）
                await cur.execute(
                    "SELECT example_id FROM miq_ex_relations WHERE miskito_word_id=%s", (word_id,)
                )
                ex_ids = await cur.fetchall()
                example_ids = [row["example_id"] for row in ex_ids]

                examples = []
                if example_ids:
                    # IN句でまとめて取得
                    format_strings = ','.join(['%s'] * len(example_ids))
                    await cur.execute(
                        f"SELECT miskito_sentence, spanish_sentence FROM examples WHERE id IN ({format_strings})",
                        tuple(example_ids)
                    )
                    exs = await cur.fetchall()
                    examples = [
                        {
                            "miskito_sentence": e["miskito_sentence"],
                            "spanish_sentence": e["spanish_sentence"]
                        }
                        for e in exs
                    ]
                results["examples"] = examples


            elif lang == "es":

                # spanish_wordsから起点単語を取得
                await cur.execute("SELECT spanishWord FROM spanish_words WHERE id=%s", (word_id,))
                word_row = await cur.fetchone()
                results["source_word"] = word_row["spanishWord"] if word_row else None
                results["target_lang"] = "miq"

                # miq_esp_relationsから対応するミスキート語訳を取得
                await cur.execute("""
                    SELECT
                        r.id AS rel_id,
                        m.id AS miskito_id,
                        m.miskitoWord,
                        r.weight
                    FROM miq_esp_relations r
                    JOIN miskito_words m ON r.miskito_word_id = m.id
                    WHERE r.spanish_word_id = %s
                """, (word_id,))
                rels = await cur.fetchall()
                results["relations"] = [
                    {
                        "id": r["miskito_id"],
                        "word": r["miskitoWord"],
                        "weight": r["weight"],
                        "relation_id": r["rel_id"]
                    } for r in rels
                ]
                # スペイン語起点ではnote,exampleは空

            else:
                results["error"] = "langは'miq'または'es'で指定してください"

            return results