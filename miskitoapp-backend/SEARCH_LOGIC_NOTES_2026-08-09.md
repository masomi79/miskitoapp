# FastAPI 検索ロジックメモ

作成日: 2026-08-09
対象: /home/upla/miskitoapp/miskitoapp-backend/main.py

## 1. 実装場所

- 検索 API 本体は main.py に実装されている。
- エンドポイント:
  - GET /api/word-relations
  - GET /api/word-relations-by-id

## 2. /api/word-relations の処理概要

入力:
- word (クエリ)

処理フロー:
1. miskito_words と spanish_words で入力単語を完全一致検索。
2. ミスキート語で見つかった場合:
   - miq_esp_relations と spanish_words を JOIN して訳語候補を返す。
3. スペイン語で見つかった場合:
   - miq_esp_relations と miskito_words を JOIN して訳語候補を返す。
4. どちらにも見つからなければ error を返す。

返却の主なキー:
- input_lang
- input_word
- target_lang
- relations
- error (該当なし時)

## 3. /api/word-relations-by-id の処理概要

入力:
- lang (miq または es)
- word_id

miq 分岐:
1. 起点単語を miskito_words から取得。
2. miq_esp_relations + spanish_words で訳語一覧を取得。
3. 起点単語が複合語の場合:
   - 空白分割し、語尾 i を aia に置換した候補を試行。
   - 各語の訳語リストを compose_words に格納。
4. notes テーブルから note を取得。
5. miq_ex_relations で example_id を集め、examples から例文を取得。

es 分岐:
1. 起点単語を spanish_words から取得。
2. miq_esp_relations + miskito_words で訳語一覧を取得。
3. notes/examples は空のまま。

返却の主なキー:
- source_lang
- source_word_id
- source_word
- target_lang
- relations
- notes
- examples
- compose_words

## 4. 重要な注意点

1. 検索は部分一致ではなく完全一致。
2. miq 分岐内で word_id が再代入されるため、
   その後の notes/examples 取得対象が意図した語とずれる可能性がある。
3. 現在の公開経路では /api/* は Apache から Uvicorn(FastAPI) にプロキシされるため、
   フロントの検索 API 実体は Laravel ではなくこの FastAPI 側。

## 5. 追加確認候補

- word_id 再代入の意図確認と、必要なら別変数化。
- 完全一致のみで十分か、前方一致・部分一致を導入するか。
- SQL のインデックス状況確認（miskitoWord, spanishWord, relation 外部キー）。
