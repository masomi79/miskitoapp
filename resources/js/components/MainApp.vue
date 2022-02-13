<template>
        <the-modal
            v-if="showModal"
            v-on:close="closeModal"
            v-bind:modalType="mtype"
            v-bind:targetWord="mword"
        ></the-modal>
    <div class="diccionario">
        <div>
            <input-form
                v-on:catchSentWord="sendWordToSearch"
            ></input-form>
            <div v-if="wordToSearch">
                <search-results
                    v-bind:wordToSearch="wordToSearch"
                ></search-results>
            </div>
        </div>
            <the-control
                v-bind:targetWord="mword"
                v-on:catchModalControl="openModal"
            ></the-control>
    </div>
</template>
<script>
    import TheModal from './modules/TheModal.vue';
    import TheControl from './modules/TheControl.vue';
    import InputForm from './modules/InputForm.vue';
    import SearchResults from './modules/SearchResults.vue';
    export default {
        components:{
            TheModal,
            TheControl,
            InputForm,
            SearchResults

        },
        props:[
/*            'isUser',*/
            'loginUser',
            'userPriviledge'
        ],
        inject:[
            'isUser',
        //    'loginUser'
        ],
        emits:['catchSentWord'],
        data(){
            return{
                showModal: false,
                jumpToId: '',
                jumpToLang: '',
                wordToSearch: ''
            };
        },
        methods:{
            sendWordToSearch(word){
                this.wordToSearch = word;
                // console.log('vamos a buscar ' + this.wordToSearch);
            },

            openModal(type){
                this.showModal = true;
                this.mtype = type;
                this.mword = this.word
                // console.log('opening modal type : ' + this.mtype);
            },

            closeModal(){
                this.showModal = false;
            },


            umcoWord(sentWord){
                // console.log('sent word is ' + sentWord);
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
</style>
