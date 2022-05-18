<template>
    <h3>notes</h3>
    <ul
        v-for="note in wordNotes" v-bind:key="note.index"
    >
        <li>umco </li>
    </ul>
</template>

<script>
export default{
    props: ['id'],
    data(){
        return{
            wordNotes: ''
        }
    },
    async mounted(){

        const dataForNote = {
            'id' : this.id
        }

        await axios.post('/api/getNotesFromId', dataForNote)
            .then(response => this.wordNotes = response.data)
            .catch(error => console.log(error));

    }

}
</script>