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
<script setup>
import { ref } from 'vue';

const username = ref('');
const password = ref('');

const handleLogin = async () => {
    try {
        const res = await fetch("http://localhost:8000/auth/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                username: username.value,
                password: password.value,
            }),
        });
        const result = await res.json();
        if (res.ok) {
            localStorage.setItem('access_token', result.access_token)
            window.dispatchEvent(new Event('login-success'))
            this.$router.push('/mypage')
        } else {
            alert(result.detail || "ログイン失敗");
        }
    } catch (e) {
        alert("通信エラー");
    }
}

</script>
