<template>
    <div>
        <h2>{{ word }}</h2>
        
        <p><span>{{ id }}, {{ lang }}</span></p>

        <word-meanings
            class="word-meanings"
            v-bind:id="id"
            v-bind:lang="lang"
            v-bind:userPriviledge="userPriviledge"
            @sendDeleteRelation="deleteRelation"></word-meanings>
        <example-sentences
            class="example-sentences"
            v-bind:id="id"></example-sentences>

    </div>
</template>

<script>
import WordMeanings from './WordMeanings';
import ExampleSentences from './ExampleSentences.vue';
export default {
    components:{
        ExampleSentences,
        WordMeanings
    },
    props:['id','lang','userPriviledge'],
    data(){
        return {
            wordData: '',
            word: '',
            message: ''
        }
    },
    methods:{
        async deleteRelation(deleteData){
            deleteData.tid = Number(this.id);
            console.log(deleteData);

            await axios.post('/api/deleteRelation', deleteData)
                .then(response => this.message = response.data)
                .catch(error => console.log(error));
            
            console.log(this.message);

        }
    },
    async created(){

        const data = {
            id: this.id,
            lang: this.lang
        }

        await axios.post('/api/getWordFromId', data)
            .then(response => this.wordData = response.data)
            .catch(error => console.log(error));
        this.word = this.wordData.word;

        console.log('this is word base component. id is ' + data.id + ' and lang is ' + data.lang);
    }
}
</script>

