<template>
    <div class="inner">
        <h2>[ {{ word }} ]</h2>

        <word-meanings
            class="word-meanings"
            v-bind:id="id"
            v-bind:lang="lang"
            v-bind:userPriviledge="userPriviledge"
            ></word-meanings>

        <word-notes
            class="word-notes"
            v-bind:id="id"></word-notes>

        <conjugations-table
            class="verbs-conjugations"
            v-if="isVerb"
            v-bind:word="word"></conjugations-table>

        <example-sentences
            class="example-sentences"
            v-bind:id="id"></example-sentences>

        <see-also 
            class="see-also"
            v-if="isRelated"
            v-bind:relatedTermsSet="relatedTermsSet"
            ></see-also>

        <p class="ref"><span>{{ lang }}-{{ id }}</span></p>

        <share-buttons
        ></share-buttons>

    </div>
</template>

<script>
import ExampleSentences from './ExampleSentences.vue';
import ConjugationsTable from './ConjugationsTable.vue';
import SeeAlso from './SeeAlso.vue';
import SearchedMeanings from '../modules/SearchedMeanings.vue';
import ShareButtons from '../modules/ShareButtons.vue';
import WordMeanings from './WordMeanings.vue';
import WordNotes from './WordNotes';
export default {
    components:{
        ConjugationsTable,
        ExampleSentences,
        SeeAlso,
        SearchedMeanings,
        ShareButtons,
        WordMeanings,
        WordNotes
    },
    props:['id','lang','userPriviledge'],
    data(){
        return {
            wordData: '',
            word: '',
            message: '',
            //動詞かどうかを判定して活用表の表示を決定するための変数
            isVerb: false,
            //Miskito語の単語であれば関連語句をチェックする
            isRelated: false,
            relatedTermsSet: ''
        }
    },
    methods:{
        async deleteRelation(deleteData){
            deleteData.tid = Number(this.id);
            // console.log(deleteData);

            await axios.post('/api/deleteRelation', deleteData)
                .then(response => this.message = response.data)
                .catch(error => console.log(error));
            
            // console.log(this.message);

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

        if(this.wordData.type == 'v'){this.isVerb=true;}

        // console.log('this is word base component. id is ' + data.id + ' and lang is ' + data.lang);
        // console.log(this.wordData);

        //see alsoを生成
        if(this.lang == 'miq'){

            let words = this.word.split(" ");
            const dataForRelatedTerms = {
                'words' : words
            }
            // 取得
            await axios.post('/data/getRelatedTerms', dataForRelatedTerms)
                .then(response => this.relatedTermsSet = response.data)
                .catch(error => console.log(error));
            // ステータスの変更
            if(Object.keys(this.relatedTermsSet).length > 0){ this.isRelated = true}
        }

    }
}
</script>
<style scoped>
    .inner{
        padding: 0 0 2rem;
        text-align: left;
    }
    .ref{
        text-align: right;
    }
    .word-meanings{
        padding-left: 40px;
    }
    h2{
        font-size: 2.6rem;
        font-weight: bold;
    }
</style>
