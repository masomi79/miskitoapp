<template>
    <p>Actualmente contamos con {{ totals }}  parabras miskitas traducidas.</p>
</template>
<style scoped>
    p{
        margin: 0;
    }
</style>
<script>
export default {
    data(){
        return{
            totals: 'umco'
        }
    },
    
    async created(){
        await axios.get('/data/getTotalNumber')
                    .then(response => this.totals = response.data)
                    .catch(error => console.log(error));        
    }
}
</script>