<template>
    <h3>Talvez Busca ... </h3>
    <ul 
        v-if="miqSuggestionsSet"
        class="suggestions-set">
        <li
            v-for="miqSuggestion in miqSuggestionsSet"
            v-bind:key="miqSuggestion.id"
        >
            <span
                class="pointer"
            >
                <router-link 
                    :to="{
                        path: 'word',
                        query:{
                            id: miqSuggestion.id,
                            lang: miqSuggestion.lang
                        }
                    }">{{ miqSuggestion.miskitoWord }}
                </router-link>
            </span>
        </li>
    </ul>
    <ul
        v-if="espSuggestionsSet"
        class="suggestions-set">
        <li
            v-for="espSuggestion in espSuggestionsSet"
            v-bind:key="espSuggestion.id"
        >
            <span
                class="pointer"
            >
                <router-link 
                    :to="{
                        path: 'word',
                        query:{
                            id: espSuggestion.id,
                            lang: espSuggestion.lang
                        }
                    }">{{ espSuggestion.spanishWord }}
                </router-link>
            </span>
        </li>
    </ul>
</template>
<script>
export default {
    props:['wordToSearch'],
    data(){
        return{
            miqSuggestionsSet: '',
            espSuggestionsSet: ''
        }
    },
    created(){
        this.getSuggestionslist(this.wordToSearch);
    },
    watch:{
        wordToSearch:function(value){
            this.getSuggestionslist(value);
        }
    },
    methods:{
        // Suggestionsを作成する
        async getSuggestionslist(word){
            // console.log('make suggestions for ' + word);

            // データセットの生成
            const sugData = {
                'word' : word
            }
        
        // APIの値を格納する。後で言語別に分ける
        let suggestionsData = '';

        // 取得
            await axios.post('/api/getSuggestionsFromWord', sugData)
                .then(response => suggestionsData = response.data)
                .catch(error => console.log(error));

            const miqSuggestions = suggestionsData['miq'];
            const espSuggestions = suggestionsData['esp'];

            // router-linkのためにlangキーを追加する
            if(miqSuggestions.length > 0){
                miqSuggestions.forEach((e)=>{
                    e.lang = 'miq';
                });
                this.miqSuggestionsSet = miqSuggestions;
            }

            if(espSuggestions.length > 0){
                espSuggestions.forEach((m)=>{
                    m.lang = 'esp';
                });
                this.espSuggestionsSet = espSuggestions;
            }
        }
    }
}
</script>
<style scoped>
    .suggestions-set{
        margin: 0 auto 1rem;
    }
    h3{
        margin-top: 1.6rem;
    }
</style>