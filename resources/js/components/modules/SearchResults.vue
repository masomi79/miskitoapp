<template>
    <div class="container search-results-wrap">
        <p>Resultado de la busqueda</p>
        <h2>[ 
            <router-link
                    :to="{
                        path: 'word',
                        query:{
                            id: wordId,
                            lang: langToSearch
                        }
                    }"
                > {{wordToSearch}} </router-link> 
            ]</h2>
        <searched-meanings
            v-bind:wordToSearch="wordToSearch"
            v-on:getLangToSearch="getIdFromWord"
        ></searched-meanings>
    </div>
</template>
<script>
import SearchedMeanings from './SearchedMeanings.vue'
export default {
  components: { SearchedMeanings },
    props:{
        wordToSearch: {
            type: String,
            default: 'hoge'
        }
    },
    data(){
        return {
            wordId: '',
            langToSearch: ''
        }
    },
    methods:{
        async getIdFromWord(lang){
            this.langToSearch = lang;

            const data = {
                lang : this.langToSearch,
                word : this.wordToSearch
            };

            let rs = '';

            await axios.post('/api/getIdFromWord',data)
                .then(response=>rs=response.data)
                .catch(error=>console.log(error));

            this.wordId = rs;

            console.log('id is ' + this.wordId + ' and lang is ' + this.langToSearch);

        }
    }

}
</script>
<style scoped>
    .search-results-wrap{
        text-align: left;
        padding: 1rem;
    }
</style>