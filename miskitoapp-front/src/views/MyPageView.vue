<template>
    <h1>Home</h1>
    <a href="/logout">logout</a>
</template>
<script>
    import { onMounted, ref } from 'vue';

    export default {
        setup() {
            const isLoggedIn = ref(false);
            const userName = ref('');

            onMounted(async () => {
                const token = localStorage.getItem('access_token');
                if (token) {
                    isLoggedIn.value = true;
                    try {
                        const res = await fetch('http://localhost:8000/mypage', {
                            headers: { Authorization: `Bearer ${token}` }
                        });
                        console.log('mypage response:', res.status);
                        if (res.ok) {
                            const data = await res.json();
                            userName.value = data.name || data.email || '';
                        } else {
                            isLoggedIn.value = false;
                            userName.value = '';
                        }
                    } catch (e) {
                        console.error(e);
                        isLoggedIn.value = false;
                        userName.value = '';
                    }
                } else {
                    isLoggedIn.value = false;
                    userName.value = '';
                }
            });

            return { isLoggedIn, userName };
        }
    }
</script>
<style scoped>
    h1 {
        margin: 2em auto;
    }
    .my-page-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    h1{
        font-size: inherit;
    }
</style>