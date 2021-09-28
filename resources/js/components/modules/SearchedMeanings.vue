<template>
    <ul class="meanings-list">
        <li 
            v-for="meaning in meanings"
            v-bind:key="meaning.id"
            >
            <span class="meaning-wrap">
                <router-link 
                    :to="{
                        path: 'word',
                        query:{
                            id: meaning.id,
                            lang: langToJump
                        }
                    }">{{ meaning.word }}
                </router-link>
            </span>
        </li>
    </ul>
</template>
<script>
export default {
    data(){
        return {
            langToSearch: 'hoge',
            langToJump: '',
            meanings: {}
        }
    },
    props:{
        wordToSearch:{
            type: String,
            default: 'hoge'
        }
    },
    created(){
        this.getResults(this.wordToSearch);
        this.$emit('getLangToSearch', this.langToSearch);
            console.log('lang to search is ' + this.langToSearch);
    },
    watch:{
        wordToSearch: function(val){
            this.getResults(val);
            this.$emit('getLangToSearch', this.langToSearch);
            console.log('lang to search is ' + this.langToSearch);
        },
        /*
        langToSearch: function(val){
            console.log('lang to search is ' + this.langToSearch);
            this.$emit('getLangToSearch', this.langToSearch);
        }
        */
    },
    methods:{
        async getResults(word){

            // 探す言語を決定する
            const vals = {
                word: word
            };
            let rsdata = '';
            await axios.post('/data/checkWordsLang', vals)
                .then(response=>rsdata=response.data)
                .catch(error => console.log(error));
            console.log(rsdata);
            this.langToSearch = rsdata;
            if(this.langToSearch == 'español')this.langToJump='miq';
            if(this.langToSearch == 'miskito')this.langToJump='esp';
            console.log(this.langToSearch);
            console.log(this.langToJump);


            const words = word.split(" ");
            const dataForSet = {
                'word' : word,
                'words' : words,
                'lang' : this.langToSearch
            }
            console.log(dataForSet);
            let resultsSet = {};
            await axios.post('/data/getSearchResult', dataForSet)
                .then(response => resultsSet = response.data)
                .catch(error => console.log(error));

            console.log(resultsSet.match)
            this.meanings = resultsSet.meanings;

        }
    }
}
</script>

<style scoped>
    .meanings-list li{
        display: inline;
    }
    .meaning-wrap{
        padding-right: 1rem;
    }
    .meaning-wrap::after{
        content: ',';
        position: absolute;
    }
</style>