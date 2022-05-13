<template>
    <footer>
        <div class="copyright">
            <hr>
            <p> 
                <span>Bienvenido </span>
                <span v-if="isUser"> <router-link to="home">{{loginUserName}}!</router-link> | <a v-on:click="logout()">Cerrar la sesión</a></span>
                <span v-else> Guest! | <a v-bind:href='loginUrl'>iniciar sessión</a> | <a v-bind:href='registerUrl'>inscribirse</a></span>
                
            </p>
            <p>&copy;miskito.org <span>2017 - {{ now }}</span> <span> | <router-link to="privacy">privacy</router-link> | <a href="https://massumifukuda.work/wp/contact" target="_blank">contact</a></span></p>
        </div>
    </footer>
</template>
<script>
export default {
    props:[
        'isUser',
        'loginUser',
        'loginUserName'
    ],
    data(){
        return{
            now: '2022',
            loginUrl : '/login',
            registerUrl : '/register',
            logoutUrl : '/logout'
        }
    },
    methods:{
        async logout(){
            await axios.post('/logout')
                        .then(response => console.log('loged out'))
                        .catch(error => console.log(error));
            
            window.location.href = '/dic';
        }
    }
}
</script>


<style scoped>
    footer{
        --bg-opacity: 1;
/*        background-color: #2d3748; */
/*        background-color: rgba(45,55,72,var(--bg-opacity));
        background-color: whitesmoke; */
        padding: 1.2rem 0 0;
        font-size: 0.9rem;
    }
    p{
        margin: 0 auto;
        color: #292929;
    }
    hr{
        margin: 0.3rem 0;
    }

    .copyright{
        /*
        background-color: #1a202c;
        background-color: rgba(26,32,44,1);
        color: #fff;
        */
        text-align: center;
        padding: 0.2rem 0;
        font-size: 0.8rem;
    }
</style>