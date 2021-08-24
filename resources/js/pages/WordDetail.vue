<template>
    <div class="content">
        <div class="container">
            <word-base
                v-bind:id="id"
                v-bind:lang="lang"
                v-bind:userPriviledge="userPriviledge"
            ></word-base>
        </div>
    </div>
</template>

<script>
import WordBase from '../components/dictionary/WordBase.vue';
export default {
    data(){
        return{
            id: '',
            lang: '',
            userPriviledge: 1
        }
    },
    components:{
        WordBase
    },
    created(){
        this.id = this.$route.query.id;
        this.lang = this.$route.query.lang;


    }
}
</script>

<style scoped>
    .content{
        background-color: white;
        color: #292929;
    }
</style>