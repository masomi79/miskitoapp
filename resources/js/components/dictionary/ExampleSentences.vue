<template>
    <div v-if="isExamples">
        <h3>Ejemplos</h3>
        <ul>
            <dl
                v-for="example in examplesSet"
                v-bind:key = "example.index"
            >
                <dt>{{ example.miskito_sentence.toLowerCase() }}</dt>
                <dd>{{ example.spanish_sentence.toLowerCase() }}</dd>
            </dl>
        </ul>
    </div>
</template>
<script>
export default {
    props: ['id'],
    data(){
        return{
            isExamples: false,
            examplesSet: {}
        }
    },
    async mounted(){
        const dataForExamples = {
            'id' : this.id
        }
        
        await axios.post('/api/getExamples', dataForExamples)
            .then(response => this.examplesSet = response.data)
            .catch(error => console.log(error));
        // ステータスの変更
        if(Object.keys(this.examplesSet).length > 0){ this.isExamples = true}
    }
}
</script>
<style scoped>
    h3{
        margin-top: 1.6rem;
    }
    dt,dd{
        text-transform: lowercase;
    }
    dt{
        font-size: 1.3rem;
        font-weight: bold;
    }
    dd{
        padding-left: 1rem;
        font-size: 1rem;
    }
    dt:first-letter,dd:first-letter{
        text-transform: uppercase;
    }
</style>