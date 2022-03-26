<template>
    <the-modal
        v-if="showModal"
        v-on:close="closeModal"
        v-bind:modalType="mtype"
        v-bind:targetWord="mword"
    ></the-modal>
    <div class="container">
    <h2>Yamni Balram {{userName}}!</h2>
    <div
    v-if="isUser">
            <the-control
                v-bind:targetWord="mword"
                v-on:catchModalControl="openModal"
            ></the-control>
    </div>
    <div
    v-else>
            <ul>
                <li><a v-bind:href='loginUrl'>iniciar sessión</a></li>
                <li><a v-bind:href='registerUrl'>inscribirse</a></li>
                </ul>
    </div>
        
    </div>
</template>
<script>
    import TheModal from '../components/modules/TheModal.vue';
    import TheControl from '../components/modules/TheControl.vue';
    export default {
        components:{
            TheModal,
            TheControl
        },
        data(){
            return{
                showModal: false,
                mword: '',
                loginUser: '',
                userName: '',
                isUser: false,
                umco: 'umco',
                loginUrl : '/login',
                registerUrl : '/register',
                logoutUrl : '/logout'
            }
        },
        methods:{

            openModal(type){
                this.showModal = true;
                this.mtype = type;
                this.mword = this.word
                // console.log('opening modal type : ' + this.mtype);
            },

            closeModal(){
                this.showModal = false;
            },
        },
        async created(){
                //ログインチェック
                await axios.get('/data/loginCheck')
                    .then(response => this.loginUser = response.data)
                    .catch(error => console.log(error))
                if(this.loginUser.userId > 0){
                    this.isUser = true;
                    this.userName = this.loginUser.userName;
                    console.log(this.loginUser);
                }else{
                    this.isUser = false;
                }
                console.log(this.isUser);
        }
    }
</script>
<style scoped>
    .container{
        margin-top: 5rem;
    }
</style>