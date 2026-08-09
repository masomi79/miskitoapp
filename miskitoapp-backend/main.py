from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
import aiomysql
from dotenv import load_dotenv
import os
from routers import auth, words

# .envファイルから環境変数を読み込む
load_dotenv()

# FastAPIアプリケーションの作成
app = FastAPI()
app.include_router(auth.router)
app.include_router(words.router)

# CORS（クロスオリジンリソースシェアリング）の設定
# フロントエンド（Vue等）からAPIを呼ぶ
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # 開発中は全許可。
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
