<template>
    <div class="form-field-wrap">
        <span class="form-field">
            <input 
                class="keyword"
                v-bind:placeholder="phmessage" 
                v-model="word"
                v-on:keyup.enter="sendWord(word)"
            >
            <button class="searchButton" v-on:click="sendWord(word)"><img src="img/search.png"></button>
        </span>
        <button class="normal" v-on:click="sendWord(word)">buscar</button>
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
<style scoped>
    .form-field-wrap{
        padding: 1rem 0;
    }
    .form-field{
        display: block;
        background-color: #efefef;
        width: 85%;
        max-width: 640px;
        margin: 0 auto;
        padding: 0;
        height: 3rem;
        text-align: center;
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
</style>