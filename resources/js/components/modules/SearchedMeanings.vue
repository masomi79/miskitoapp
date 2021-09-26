<template>
    {{ wordToSearch }}
</template>
<script>
export default {
    data(){
        return {
            langToSearch: 'hoge'
        }
    },
    props:{
        wordToSearch:{
            type: String,
            default: 'hoge'
        }
    },
    async created(){
        this.getResults(this.wordToSearch);
        await console.log(this.langToSearch);
    },
    async updated(){
        this.getResults(this.wordToSearch);
        await console.log(this.langToSearch);
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
            console.log(this.langToSearch);
        }
    }
}
</script>