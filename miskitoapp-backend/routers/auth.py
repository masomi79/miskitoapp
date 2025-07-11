from fastapi import APIRouter, HTTPException, Depends
from schemas.user import UserCreate
from utils.password import hash_password
import aiomysql
from dotenv import load_dotenv
import os

load_dotenv(dotenv_path=os.path.join(os.path.dirname(os.path.dirname(__file__)), ".env"))

router = APIRouter()

@router.post("/auth/register")
async def register_user(user: UserCreate):
    # MariaDBに接続（接続方法は本番環境に応じて変更してください）
    conn = await aiomysql.connect(
        host=os.getenv("DB_HOST"),
        user=os.getenv("DB_USER"),
        password=os.getenv("DB_PASSWORD"),
        db=os.getenv("DB_NAME")
    )
    async with conn.cursor() as cur:
        # メールアドレス重複チェック
        await cur.execute("SELECT id FROM users WHERE email=%s", (user.email,))
        if await cur.fetchone():
            raise HTTPException(status_code=400, detail="Email already registered")
        # パスワードをハッシュ化
        hashed_pw = hash_password(user.password)
        # ユーザー登録
        await cur.execute(
            "INSERT INTO users (name, email, password) VALUES (%s, %s, %s)",
            (user.name, user.email, hashed_pw)
        )
        await conn.commit()
    conn.close()
    return {"msg": "User registered successfully"}