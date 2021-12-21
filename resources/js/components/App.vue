<template>
  <the-header></the-header>
  <router-view></router-view>
  <the-footer
    v-bind:loginUser = loginUser.userId
    v-bind:loginUserName = loginUser.userName
    v-bind:isUser = isUser
    ></the-footer>
</template>

<script>
  import TheHeader from './TheHeader.vue';
  import MainApp from './MainApp.vue';
  import TheFooter from './TheFooter.vue';
  export default{
    components: {
      TheHeader,
      MainApp,
      TheFooter
    },
    data(){
      return{
        loginUser: '',
        isUser: false
      }
    },
    async created(){
            //ログインチェック
            await axios.get('/data/loginCheck')
                .then(response => this.loginUser = response.data)
                .catch(error => console.log(error))
            if(this.loginUser.userId > 0){
                this.isUser = true;
                 // console.log(this.loginUser);
            }else{
                this.isUser = false;
            }
            // console.log(this.isUser);
    },
    provide() {
      return {
        isUser: this.isUser,
        loginUser: this.loginUser
      }
    }
  }
</script>

<style>
  header{
    background-color: #f8fafc;
    border-bottom: solid 1px lightgray;
  }
  footer{
    background-color: white;
  }
  .copyright{
    background-color: white;
  }
  button{
    border-radius: 10px;
    padding: 0.5rem 1.5rem;
    margin: 0.3rem;
    border: none;
  }
  button:hover{
    opacity: 0.7;
  }
  button.searchButton{
        padding: 0;
    line-height: 0;
  }
  button.normal{
    color: white;
    background-color: #2C974A;
    border-color: lightgray;
    box-shadow: lightgray,lightgray;
  }
  button.adelante{
    background: green;
    color: white;
  }
  button.negative{
    background: gray;
    color: #333;
  }

  h1,h2,h3,h4,h5,h6,
  p,a,
  ul,li,dl,dt,dd{
    margin: 0;
  }
  .tar{
    text-align: right;
  }
  .tal{
    text-align: left;
  }
</style>