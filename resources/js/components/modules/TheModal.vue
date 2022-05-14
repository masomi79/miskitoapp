<template>
    <transition name="fade">
    <teleport to="body">
        <div class="modal">
            <!-- new word -->
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
            <!-- new example -->
            <div
                v-if="modalType == 'newExample'"
                class="modalInner">
                <div>{{ responceMessage }}</div>
                
                <div v-if="targetWord"><p>Añadir un nuevo ejemplo para "{{ targetWord }}"</p></div>
                <div v-else>
                    <div class="modalParts">
                        <label for="targetWord">palabra en miskito</label>
                        <div class="inputWrap">
                        <input v-model="exampleData.targetWord" type="text" placeholder="palabra para relacionar">
                    </div>
                </div>
                </div>
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
            <!-- new comment -->
            <div v-if="modalType == 'newNote'" class="modalInner">
                <div>{{ responceMessage }}</div>
                <div v-if="targetWord"><p>Añadir un nueva nota para "{{ targetWord }}"</p></div>
                <div v-else>
                    <div class="modalParts">
                        <label for="targetWord">palabra en miskito</label>
                        <div class="inputWrap">
                        <input v-model="noteData.targetWord" type="text" placeholder="palabra para relacionar">
                        </div>
                    </div>
                </div>
                <div class="modalParts">
                    <label for="newNote">Contenido</label>
                    <textarea v-model="noteData.newNote" >
                    </textarea>
                </div>
                <div class="modalParts">
                    <div class="buttomWrap">
                        <button class="adelante" v-on:click="sendNewNote(noteData)">enviar</button>
                        <button class="negative" v-on:click="$emit('close')">cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
    </transition>
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
            },
            noteData: {
                targetWord: this.targetWord,
                newNote: ''
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
            if(confirm('¿Estás seguro?')){
                this.responceMessage = '';
                await axios.post('/data/registerNewWord', wordData)
                    .then(response => this.responceMessage = response.data)
                    .catch(error => this.responceMessage = response.error);
            }
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
            if(confirm('¿Estas seguro?')){
                // console.log('he is sure to register');
                //例文を登録してターゲットワードと関連付ける
                await axios.post('/data/registerNewExample', exampleData)
                    .then(response => console.log(response.data))
                    .catch(response => console.log(response.error));
            }else{
            }
            this.$emit('close');
        },

        async sendNewNote(noteData){
            if(confirm('¿Estás seguro?')){
                // 新しい記事を投稿する
                await axios.post('/data/registerNewNote', noteData)
                    .then(responce => console.log(response.data))
                    .catch(responce => console.log(response.error));
            }else{
            }
            this.$emit('close');
        }
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
    textarea{
        width: 100%;
        height: 6rem;
    }
</style>
