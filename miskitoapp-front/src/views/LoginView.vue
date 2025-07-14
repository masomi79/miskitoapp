<template>
    <h1>Login</h1>
    <p>Por favor, ingresa tus credenciales para acceder a tu cuenta.</p>
    <form @submit.prevent="handleLogin">
        <div>
            <label for="username">correo:</label>
            <input type="text" id="username" v-model="username" required />
        </div>
        <div>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" v-model="password" required />
        </div>
        <button type="submit">Iniciar Sesión</button>
    </form>
</template>
<script>
export default{
    data() {
        return {
            username: '',
            password: ''
        };
    },
    methods: {
        async handleLogin() {
            try {
                const res = await fetch("http://localhost:8000/auth/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                        username: this.username,
                        password: this.password,
                    }),
                });
                const result = await res.json();
                if (res.ok) {
                    // JWTトークンをlocalStorageに保存
                    localStorage.setItem("access_token", result.access_token);
                    alert("ログイン成功");
                    // 必要ならルート遷移
                    this.$router.push("/mypage");
                } else {
                    alert(result.detail || "ログイン失敗");
                }
            } catch (e) {
                alert("通信エラー");
            }
        }
    },
    mounted() {
        console.log('LoginView mounted');
    }
}
</script>
