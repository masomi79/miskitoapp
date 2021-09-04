<template>
    <the-modal
        v-if="showModal"
        v-on:close="closeModal"
        v-bind:modalType="mtype"
        v-bind:targetWord="mword"
    ></the-modal>
    
    <div class="diccionario">
        <p>
            <span class="form-field">
            <input 
                class="keyword"
                v-bind:placeholder="phmessage" 
                v-model="word"
                v-on:keyup.enter="showWords()"
            >
            <button class="searchButton" v-on:click="showWords()"><img src="img/search.png"></button>
            </span>
        </p>
        <div v-show="loading" class="loader container">cargando...</div>
        <div v-show="!loading"></div>
        <p v-bind:html="wordslang"></p>

        <div v-if="isResults" class="results container">
            <div 
            class="meanings">
                <p><span class="title">
                
                    <router-link 
                        
                        :to="{
                            path: 'word',
                            query:{
                                id: 151,
                                lang: 'miq'
                                }
                        }">
                        
                    [ {{ word }} ]</router-link></span> 
                    <span>{{ wordslang }}</span> <span v-if="wtype" class="wtype">{{ wtype }}</span></p>
                <p>
                    <span 
                        class="meaning" 
                        v-for="meaning in meanings" 
                        :key="meaning.id"
                    >
                        <span class="meaningWord" v-on:click="research(meaning.id, meaning.word)">{{ meaning.word }}</span>
                        <span v-if="userPriviledge > 0" class="hiddenControl">
                            <span class="pointer" v-on:click="deleteRelation(meaning.id)">delete</span>
                        </span>
                    </span>
                </p>
            </div>

            <conjugations-table 
                class="verbs-conjugations"
                v-if="isVerb"
                v-bind:word="word"
            ></conjugations-table>

        </div>
        <div class="references container">
            <see-also
                class="related"
                v-if="isRelated"
                v-bind:relatedTermsSet="relatedTermsSet"
                v-on:catchResearchData="research"
            ></see-also>

            <word-suggestions
                class="suggestions"
                v-if="isSuggestions"
                v-bind:suggestionsSet="suggestionsSet"
                v-on:catchResearchData="research"
            ></word-suggestions>
            <the-control
                v-if="loginUser"
                v-bind:targetWord="mword"
                v-on:catchModalControl="openModal"
            ></the-control>
        </div>
    </div>
</template>
<script>
    import TheModal from './modules/TheModal.vue';
    import TheControl from './modules/TheControl.vue';
    import SeeAlso from './dictionary/SeeAlso.vue';
    import WordSuggestions from './dictionary/WordSuggestions.vue';
    import ConjugationsTable from './dictionary/ConjugationsTable.vue';
    export default {
        components:{
            TheModal,
            TheControl,
            SeeAlso,
            WordSuggestions,
            ConjugationsTable
        },
        props:[
/*            'isUser',*/
            'loginUser',
            'userPriviledge'
        ],
        inject:[
            'isUser',
            'loginUser'
        ],
        emits:[
            'catchResearchData',
            'catchModalControl'
        ],
        data(){
            return{
                detailLink: '/word',
                totals: 'umco',
                word: '',
                words: [],
                wordslang: '',
                wtype: '',
                opstlang: '',
                isResults: false,
                resultsSet: {},
                meanings: [],
                buttonTitle: 'search',
                phmessage: 'buscar en el disccionario',
                resultMessage1: '',         
                resultMessage2: '',
                loading: false,
                loginCheck: '',
                showModal: false,
                mtype: '',
                mword: '',
                isVerb: false,
                isRelated : false,
                relatedTermsSet: {},
                isSuggestions: false,
                suggestionsSet: {},
                isExamples: false,
                examplesSet: {}
            };
        },
        methods:{

            openModal(type){
                this.showModal = true;
                this.mtype = type;
                this.mword = this.word
                // console.log('opening modal type : ' + this.mtype);
            },

            closeModal(){
                this.showModal = false;
            },



            ///////////////////////////////////キーワードから結果を取得////////////////////////////
            async showWords(){
                this.loading = true;
                
                //初期化
                this.isResults = false;
                this.isRelated = false;
                this.isSuggestions = false;
                this.isVerb = false;
                this.isExamples = false;
                //this.wordslang = "no se encuentra";

                this.wtype = '';
                this.resultsSet = '';
                this.relatedTermsSet = '';
                const dataForLang = {'word': this.word}
                await axios.post('/data/checkWordsLang', dataForLang)
                    .then(response => this.wordslang = response.data)
                    .catch(error => console.log(error));


                this.words = this.word.split(" ");
                const dataForSet = {
                    'word' : this.word,
                    'words' : this.words,
                    'lang' : this.wordslang
                }
                await axios.post('/data/getSearchResult', dataForSet)
                    .then(response => this.resultsSet = response.data)
                    .catch(error => console.log(error));

                //結果あり
                if(Object.keys(this.resultsSet).length > 0){ this.isResults = true}

                // console.log(this.resultsSet);
                //訳語の一覧
                this.meanings = this.resultsSet.meanings


                if(this.resultsSet.type){
                    this.wtype = this.resultsSet.type;
                }
                
                //  動詞かどうかの判定、活用の一覧 
                if(this.resultsSet.type == 'v'){
                    this.isVerb = true;
                }else{
                    this.isVerb = false;
                }

                //Miskito語の場合関連語句の一覧を作成する
                if(this.wordslang == 'miskito'){
                    // 関連語句の生成
                        // データセットの生成
                        const dataForRelatedTerms = {
                            'words' : this.words
                        }
                        // 取得
                        await axios.post('/data/getRelatedTerms', dataForRelatedTerms)
                            .then(response => this.relatedTermsSet = response.data)
                            .catch(error => console.log(error));
                        // ステータスの変更
                        if(Object.keys(this.relatedTermsSet).length > 0){ this.isRelated = true}

                    // Suggestionsを作成する
                        // データセットの生成
                        const dataForSuggestions = {
                        'word' : this.word
                        }
                        // 取得
                        await axios.post('/data/getSuggestions', dataForSuggestions)
                            .then(response => this.suggestionsSet = response.data)
                            .catch(error => console.log(error));

                       
                        // ステータスの変更
                        if(Object.keys(this.suggestionsSet).length > 0){ this.isSuggestions = true}

                }

                // nothing more to load
                this.loading = false;

            },

            /////////////////////////////IDから結果を取得////////////////////////////////
            async research(id, word, langv){
                //初期化

                this.loading = true;

                this.isResults = false;
                this.isRelated = false;
                this.isSuggestions = false;
                this.isVerb = false;
                this.isExamples = false;

                this.resultsSet = '';
                this.relatedTermsSet = ''
                this.wtype = '';

                if(langv){
                    this.wordslang = langv;
                }else{

                    if(this.wordslang == 'miskito'){ 
                        this.wordslang = 'español';
                    }else{ 
                        this.wordslang = 'miskito';
                    }

                }

                

                const dataForSerachById = {
                    'lang' : this.wordslang,
                    'id' : id,
                    'word' : word
                }
                

                    await axios.post('/data/getSearchResultById', dataForSerachById)
                    .then(response => this.resultsSet = response.data)
                    .catch(error => console.log(error));

                //結果あり
                if(this.resultsSet){ 
                    this.isResults = true
                    // console.log(this.resultsSet);
                }


                this.word = this.resultsSet.cword

                //訳語の一覧
                this.meanings = this.resultsSet.meanings;

                if(this.resultsSet.type){
                    this.wtype = this.resultsSet.type;
                }

                //動詞かどうかの判定
                if(this.resultsSet.type == 'v'){
                    this.isVerb = true;

                }else{
                    this.isVerb = false;
                }


                //Miskito語の場合
                if(this.wordslang == 'miskito'){
                    // 関連語句の一覧を作成する
                        // 関連語句取得用のデータセットを生成
                        this.words = this.word.split(" ");
                        const dataForRelatedTerms = {
                                'words' : this.words
                        };
                        // 関連語句セットの生成
                        await axios.post('/data/getRelatedTerms', dataForRelatedTerms)
                        .then(response => this.relatedTermsSet = response.data)
                        .catch(error => console.log(error));
                        //ステータスの変更
                        if(Object.keys(this.relatedTermsSet).length > 0){ this.isRelated = true}

                }

                    

                
                // nothing more to load
                this.loading = false;

            },

            //動詞として登録
            async setAsVerb(){
                const setTypeData = {
                    id : this.resultsSet.match,
                    type : 'v'
                };
                // console.log(setTypeData);
                await axios.post('/data/setWordType', setTypeData)
                    .then(response => console.log(response))
                    .catch(erro => console.log(error));
            }
        },
        created() {
            console.log(this.isUser);
            console.log('id:' + this.id);
            console.log('lang:' + this.lang);
        }
    }
</script>
<style>

    a{
    color: #1d68a7;
    text-decoration: none;
    }

    .pointer:hover{
        cursor: pointer;
        text-decoration: underline;
    }
    .hiddenControl{
        display: none;
        position: absolute;
        bottom: -30px;
        right: -10px;
        background: #eee;
        padding: 0 0.2rem;
    }
    .loader{
        padding: 1rem 15px;
        text-align: left;
    }
    .verbs-conjugations table,
    .related{
        padding: 2rem 15px 0;
    }
    .diccionario{
        text-align: center;
        padding: 5rem 5% 5rem;
        background-color: #fff;
        color: #333;

    }
    .diccionario p{
        margin: 0;
    }
    .results,
    .references{
        text-align: left;
        padding-top: 2.1rem;
        /* max-width: 1140px; */
        margin: 0 auto;
    }
    .results h3{
        margin-top: 2rem;
    }
    .meaning{
       display: inline-block;
       padding-right: .8em; 
       position: relative;
    }
    .meaning::after{
        display: block;
        position: absolute;
        right: 10px;
        bottom: -1px;
        content: ","
    }
    .meaning:last-child{
        padding-right: 0;
    }
    .meaning:last-child:after{
        display: none;
    }
    .meaning:hover .meaningWord{
        cursor: pointer;
        text-decoration: underline;
    }
    .meaning:hover .hiddenControl{
        display: block;
    }
    .form-field{
        display: block;
        background-color: #efefef;
        width: 85%;
        max-width: 640px;
        margin: 0 auto;
        padding: 0;
        height: 3rem;
    }
    .form-field button{
        border: none;
    }
    .form-field button img{
        width: 32px;
        line-height: 0;
    }
    input.keyword{
        width: 85%;
        border: none;
        height: 3rem;
        font-size: 1.5rem;
        border-radius: 5px 5px 0 0;
        line-height: 3rem;
        background: none;
    }
    input.keyword:focus{
        outline: none !important;
    }
    .title{
        font-size: 1.8rem;
    }
    td{
        border: solid 1px #4a5568;
        padding: 0.4rem;

    }
    .wtype{
        border: solid 1px #333;
        border-radius: 3px;
        padding: 0 0.4rem;
        font-size: 0.8rem;
        line-height: 0;
    }
    .related-word{
        position: relative;
        padding-right: 1rem;
    }
    .related-word:after{
        position: absolute;
        content: "...";
        bottom: 0;
        right: 0;
    }
    .related-meaning{
        position: relative;
        padding-left: 0.8rem;
    }
    .related-meaning:after{
        position: absolute;
        content: ",";
        right: -4px;
        bottom: -1px;
    }
    .related-meaning:last-child:after{
        content: "";
    }
    .control-panel{
        padding: 3rem  0 0;
    }
</style>
