from fastapi import APIRouter, HTTPException, Depends
from schemas.user import UserCreate
from utils.password import hash_password, verify_password
import aiomysql
from dotenv import load_dotenv
import os
from fastapi.security import OAuth2PasswordRequestForm
from utils.jwt import create_access_token, get_current_user
from schemas.user import UserCreate, User 

load_dotenv(dotenv_path=os.path.join(os.path.dirname(os.path.dirname(__file__)), ".env"))

router = APIRouter()

@router.post("/auth/register")
async def register_user(user: UserCreate):
    # DBに接続
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


@router.post("/auth/login")
async def login_user(form_data: OAuth2PasswordRequestForm = Depends()):
    conn = await aiomysql.connect(
        host=os.getenv("DB_HOST"),
        user=os.getenv("DB_USER"),
        password=os.getenv("DB_PASSWORD"),
        db=os.getenv("DB_NAME")
    )
    async with conn.cursor() as cur:
        await cur.execute("SELECT id, name, email, password FROM users WHERE email=%s", (form_data.username,))
        user = await cur.fetchone()
        # print(f"DBから取得したユーザー: {user}")
        if not user:
            raise HTTPException(status_code=400, detail="Invalid credentials")
        
        # パスワード検証
        if not verify_password(form_data.password, user[3]):
            print("パスワード不一致")
            raise HTTPException(status_code=400, detail="Invalid credentials")
        else:
            print("パスワード一致")

        # ユーザー情報を取得
        user_data = {
            "id": user[0],
            "name": user[1],
            "email": user[2]
        }
    # 認証できたらJWT発行し返却
    conn.close()
    access_token = create_access_token(data=user_data)
    return {"access_token": access_token, "token_type": "bearer"}
    from schemas.user import User
    from fastapi import Depends
    

@router.get("/mypage")
async def get_my_page(current_user: User = Depends(get_current_user)):
    return current_user