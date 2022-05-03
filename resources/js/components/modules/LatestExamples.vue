<template>
    <h3>Oraciones de ejemplo añadidas recientemente</h3>
    <dl v-for="example in examples" :key="example.id">
        <dt><router-link :to="{
            name: 'wordDetail',
            query: {
                id: example.miskito_word_id,
                lang: 'miq'}
        }">{{ example.miskito_sentence }}</router-link></dt>
        <dd>{{ example.spanish_sentence }}</dd>
    </dl>
</template>
<script>
    export default{
        data(){
            return{
                examples: ''
            }
        },
        async created(){
            await axios.get('/data/getLatestExamples')
                        .then(response => this.examples = response.data)
                        .catch(error => console.log(error));   
            console.log(this.examples);    
        } 
    }
</script>
<style scoped>

    h3{
        color: #fff;
        color: #292929;
        margin: 1.2rem 0 0.5rem;
    }
    dl{
        margin-left: 1rem;
        color: #292929;
    }
    dt{
    font-size: 1.3rem;
    font-weight: 700;
    }
    dd{

    padding-left: 1rem;
    font-size: 1rem;
    }
</style>