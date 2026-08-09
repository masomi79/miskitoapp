# 手動デプロイ手順

対象:
- フロントエンド: /home/upla/miskitoapp/miskitoapp-front
- バックエンド: /home/upla/miskitoapp/miskitoapp-backend
- 公開ディレクトリ: /var/www/miskito/public
- 公開エントリ: /var/www/miskito/public/index.html

## 前提
- Apache が /var/www/miskito/public を配信先として使っている
- /api/* は FastAPI/Uvicorn へプロキシされる
- 変更後は Apache と FastAPI サービスを再起動する

## 1. フロントエンドのビルド
ローカルで実行:

```bash
cd /home/upla/miskitoapp/miskitoapp-front
npm install
npm run build
```

これで dist 配下に配布用ファイルが生成されます。

## 2. 公開ディレクトリへコピー
ビルド結果を公開フォルダへ反映します。公開サーバー上の公開ディレクトリは `/var/www/miskito/public` です。

```bash
sudo rsync -av --delete /home/upla/miskitoapp/miskitoapp-front/dist/ /var/www/miskito/public/
```

もし実際の公開サーバーへ送る場合は、次のようにリモートホストを指定します。

```bash
rsync -av --delete /home/upla/miskitoapp/miskitoapp-front/dist/ user@your-server:/var/www/miskito/public/
```

注意:
- 既存のファイルを置き換えるため `--delete` を付けています。
- `/var/www/miskito/public/index.html` が更新対象の入口になります。

## 3. バックエンドの更新
FastAPI 側のコードを公開サーバーへ反映します。公開サーバー上では、開発中のバックエンドをそのまま配置しているディレクトリが `/home/upla/miskitoapp/miskitoapp-backend` です。

```bash
rsync -av --delete \
  --exclude '__pycache__' \
  --exclude '.venv' \
  --exclude 'venv' \
  /home/upla/miskitoapp/miskitoapp-backend/ \
  user@your-server:/home/upla/miskitoapp/miskitoapp-backend/
```

- `user` は接続ユーザー名
- `your-server` は公開サーバーのホスト名または IP
- 送信先パスはそのまま `/home/upla/miskitoapp/miskitoapp-backend` で問題ありません

## 4. 依存関係の確認
公開サーバー側で Python の依存関係が足りているか確認します。

```bash
sudo python3 -m pip install -r /home/upla/miskitoapp/miskitoapp-backend/requirements.txt
```

## 5. サービス再起動
Apache と FastAPI を再起動します。

```bash
sudo systemctl restart apache2
sudo systemctl restart <fastapi-service-name>
```

`<fastapi-service-name>` は実際のサービス名に置き換えてください。

## 6. 動作確認
以下を確認します。

```bash
curl http://localhost:8000/api/word-relations?word=ai
curl http://localhost:8000/api/word-relations-by-id?lang=miq\&word_id=1
```

または、公開サイト上で検索画面を開いて反映内容を確認します。

## 補足
- もし公開先が別ホストや別ディレクトリなら、`rsync` の送信先パスだけ差し替えれば対応できます。
- 既存の構成では、フロントの配布物は Apache 配信、API は Uvicorn/FastAPI が担当します。
