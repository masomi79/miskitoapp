<template>

</template>

<script>
export default{
    data(){
        return{
            hasNotes: false,
            wordNote: ''
        }
    },
    async mounted(){

        const dataForNote = {
            'id' : this.id
        }

        await axios.post('/api/getNotesFromId', dataForNote)
            .then(response => this.wordNotes = response.data)
            .catch(error => console.log(error));

        console.log(this.wordNotes);

    }

}
</script>