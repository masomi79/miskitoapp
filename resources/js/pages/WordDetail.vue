<template>
    <div class="diccionario">
        <div class="container">
            <word-base
                v-bind:id="id"
                v-bind:lang="lang"
                v-bind:userPriviledge="userPriviledge"
            ></word-base>
        </div>
        <div class="container">
            <input-form
                v-on:catchSentWord="sendWordToSearch"
            ></input-form>
            <div v-if="wordToSearch">
                <search-results
                    v-bind:wordToSearch="wordToSearch"
                ></search-results>
            </div>
        </div>
    </div>
</template>

<script>
import WordBase from '../components/dictionary/WordBase.vue';
import InputForm from '../components/modules/InputForm.vue';
import SearchResults from '../components/modules/SearchResults.vue';
export default {
    data(){
        return{
            id: '',
            lang: '',
            userPriviledge: 1,
            //検索に送る
            wordToSearch: ''
        }
    },
    emits:['catchSentWord'],
    components:{
        InputForm,
        SearchResults,
        WordBase
    },
    created(){
        this.id = this.$route.query.id;
        this.lang = this.$route.query.lang;


    },
    methods:{
        sendWordToSearch(word){
            this.wordToSearch = word;
        }
    }
}
</script>

<style scoped>
    .content{
        background-color: white;
        color: #292929;
    }
</style>