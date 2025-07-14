<template>
    <h1>Inicio</h1>
    <a href="/logout">logout</a>
</template>
<script>
    export default{
        async mounted() {
            console.log('thisis my page');
            const token = localStorage.getItem("access_token");
            if (!token) {
                alert("No estás autenticado. Por favor, inicia sesión.");
                this.$router.push("/login");
            }
            try {
                const res = await fetch("http://localhost:8000/mypage", {
                    method: "GET",
                    headers: { Authorization: `Bearer ${token}` }
                });
                if (!res.ok) {
                    const error = await res.json();
                    alert(error.detail || "Error al acceder a la página");
                    this.$router.push("/login");
                }
            } catch (e) {
                alert("Error de red o servidor");
                this.$router.push("/login");
            }
        }
    }
</script>
<style>
    .my-page-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    h1{
        font-size: inherit;
    }
</style>