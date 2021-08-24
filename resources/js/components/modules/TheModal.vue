<template>
    <teleport to="body">
        <div class="modal">
            <div
                v-if="modalType == 'newWord'" 
                class="modalInner">
                <p>Añadir una nueva palabra</p>
                <div>{{ responceMessage }}</div>
                <div class="modalParts">
                    <label for="newMiskito">miskito</label>
                    <div class="inputWrap">
                    <input v-model="wordData.newMiskito" type="text" placeholder="nueva palabra en miskito">
                    </div>
                </div>
                <div class="modalParts">
                    <label for="newSpanish">español</label>
                    <div class="inputWrap">
                        <input v-model="wordData.newSpanish" type="text" placeholder="nueva palabra en español">
                    </div>
                </div>
                <div class="modalParts">
                    <div class="buttomWrap">
                        <button class="adelante" v-on:click="sendNewWords(wordData)">enviar</button>
                        <button class="negative" v-on:click="$emit('close')">cerrar</button>
                    </div>
                </div>
            </div>
            <div
                v-if="modalType == 'newExample'"
                class="modalInner">
                <p>Añadir un nuevo ejemplo para "{{ targetWord }}"</p>
                <div>{{ responceMessage }}</div>
                <div class="modalParts">
                    <label for="newExampleMiq">oración en miskito</label>
                    <div class="inputWrap">
                    <input v-model="exampleData.newExampleMiq" type="text" placeholder="nuevo ejemplo de oración en miskito">
                    </div>
                </div>
                <div class="modalParts">
                    <label for="newExampleEsp">oración en español</label>
                    <div class="inputWrap">
                        <input v-model="exampleData.newExampleEsp" type="text" placeholder="nuevo ejemplo de oración en español">
                    </div>
                </div>
                <div class="modalParts">
                    <div class="buttomWrap">
                        <button class="adelante" v-on:click="sendNewEjemplo(exampleData)">enviar</button>
                        <button class="negative" v-on:click="$emit('close')">cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>
<script>
export default {
    data(){
        return{
            responceMessage: '',
            placeholder1: '',
            wordData: {
                newMiskito: '',
                newSpanish: ''
            },
            exampleData: {
                targetWord: this.targetWord,
                newExampleMiq: '',
                newExampleEsp: ''
            }
        }
    },
    emits:['close'],
    props:['modalType', 'targetWord'],
    mounted(){
        // console.log('modal type : ' + this.modalType);
    },

    methods:{
        async sendNewWords(wordData){
            this.responceMessage = '';
            await axios.post('/data/registerNewWord', wordData)
                .then(response => this.responceMessage = response.data)
                .catch(error => this.responceMessage = response.error);
        },

        umcoFunction(word){
            console.log(word + ' is in deep shit');
        },

        async wordExistenseCheck(word){
            const checkData={
                'word' : word
            };
            var cpwd = '';
            await axios.post('/data/wordCheck', checkData)
                .then(response => cpwd = response.data)
                .catch(error => console.log(error));
            console.log(cpwd);
            return cpwd;
        },

        async sendNewEjemplo(exampleData){
            if(confirm('are you sure?')){
                // console.log('he is sure to register');
                //例文を登録してターゲットワードと関連付ける
                await axios.post('/data/registerNewExample', exampleData)
                    .then()
                    .catch(response => console.log(response.error));

                //例文を分解して単語の配列に格納する
                const exampleWordsAll = exampleData.newExampleMiq.split(' ');
                const exampleWords = exampleWordsAll.filter(element => !(element == exampleData.targetWord))

                //活用形などのバリエーションを含めて配列中の単語が辞書に存在するかチェックする

                //例文の中の単語が辞書にあればその例文を単語に関連づけるか尋ねて yes　なら登録する
                exampleWords.forEach(function(word, index){
                    var cpw = this.wordExistenseCheck(word);

                    if (cpw){
                        if(confirm('registrar este ejemplo para la palabra "' + word + '" ?')){
                            console.log('registrado.');
                        }else{
                            console.log('no registrado');
                        }
                    }else{
                    }              
                }, this);

            }else{
                // console.log('he is not sure');
            }
            this.$emit('close');
        },
    }
}
</script>
<style scoped>
    .modal{
        display: block;
        background-color: antiquewhite;
        border: solid 1px #ccc;
        padding: 2rem;
        position: fixed;
        top: 4rem;
        left: 50%;
        margin-left: -25%;
        width: 50%;
        height: 80%;
        overflow-y: auto;
        color: #333;
    }
    .modalParts{
        padding: 0.5rem 0.5rem 0 0.5rem;
        margin: 0.5rem 0 0 0;
    }
    .modalParts label{
        margin:0;
    }
    .modalParts .inputWrap{
        background: #fff;
        border: solid 1px #ccc;
    }
    .modalParts .buttomWrap{
        text-align: center;
        padding: 2rem 0 0 0;
        display: grid;
    }
    .modalParts input[type='text']{
        border: none;
        width: 100%;
        padding-left: 1rem;
    }

</style>
