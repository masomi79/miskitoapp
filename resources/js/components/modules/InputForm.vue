<template>
    <div>
        <span class="form-field">
            <input 
                class="keyword"
                v-bind:placeholder="phmessage" 
                v-model="word"
                v-on:keyup.enter="sendWord(word)"
            >
            <button class="searchButton" v-on:click="sendWord(word)"><img src="img/search.png"></button>
        </span>
    </div>
</template>
<script>
    export default{
        data(){
            return{
                phmessage: 'buscar en el diccionario',
                word: ''
            }
        },
        emits:['cacthSentWord'],
        methods:{
            sendWord(word){
                // console.log('search word :' + word);
                this.$emit('catchSentWord', word);
            }
        }
    }
</script>