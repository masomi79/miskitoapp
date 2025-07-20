<template>
    <div>
        <h1>Logout</h1>
        <p>Has cerrado sesión exitosamente.</p>
        <button @click="redirectToLogin">Volver a la página de inicio de sesión</button>
    </div>
</template>

<script>
    export default {
        name: 'LogoutView',
        methods: {
            redirectToLogin() {
                // Redirige al usuario a la página de inicio de sesión
                this.$router.push({ name: 'Login' });
            }
        },
        mounted() {
            // Aquí podrías agregar lógica para limpiar el estado del usuario, si es necesario
            localStorage.removeItem('access_token'); // Elimina el token de acceso
            window.dispatchEvent(new Event('login-success')); // ←追加
        }
    };
</script>