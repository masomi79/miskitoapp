<template>
    <div class="container search-results-wrap">
        <p>Resultado de la busqueda</p>
        <h2 v-if="wordNotFound">[ {{wordToSearch}} ]</h2>
        <h2 v-else>[ 
            <router-link
                    :to="{
                        path: 'word',
                        query:{
                            id: wordId,
                            lang: langToSearch
                        }
                    }"
                > {{wordToSearch}} </router-link> 
            ]
        </h2>
        <p v-if="wordNotFound" class="one-line-message">Lo sentimos pero no se encuentra la palabra en el diccionario</p>
        <searched-meanings
            v-bind:wordToSearch="wordToSearch"
            v-on:getLangToSearch="getIdFromWord"
        ></searched-meanings>

        <word-suggestions
            v-bind:wordToSearch="wordToSearch"
        >
        </word-suggestions>

    </div>
</template>
<script>
import SearchedMeanings from './SearchedMeanings.vue';
import WordSuggestions from '../dictionary/WordSuggestions.vue';
export default {
    components: { 
        SearchedMeanings,
        WordSuggestions
    },
    props:{
        wordToSearch: {
            type: String,
            default: 'hoge'
        }
    },
    data(){
        return {
            wordId: '',
            langToSearch: '',
            wordNotFound: false
        }
    },
    methods:{
        async getIdFromWord(lang){
            this.wordNotFound = false;

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
            if( rs == 'no se encuentra'){
                this.wordNotFound = true
            }

            // console.log('id is ' + this.wordId + ' and lang is ' + this.langToSearch);

        }
    }

}
</script>
<style scoped>
    .search-results-wrap{
        text-align: left;
        padding: 1rem;
    }
    .one-line-message{
        padding-left: 40px;
    }
</style>