<template>
    <h3>Talvez Busca ... </h3>
    <ul>
        <li
            v-for="suggestion in suggestionsSet"
            v-bind:key="suggestion.id"
        >
            <span
                class="pointer"
            >{{ suggestion }}</span>
        </li>
    </ul>
</template>
<script>
export default {
    props:['wordToSearch'],
    data(){
        return{
            suggestionsSet: ''
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
            console.log('make suggestions for ' + word);

            // データセットの生成
            const sugData = {
                'word' : word
            }
        
        // 取得
            await axios.post('/api/getSuggestionsFromWord', sugData)
                .then(response => this.suggestionsSet = response.data)
                .catch(error => console.log(error));

            console.log('suggestions are ' + this.suggestionsSet);
        }
    }
}
</script>