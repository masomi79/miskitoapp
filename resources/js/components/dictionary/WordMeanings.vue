<template>
    <div v-if="isMeanings">
        <p>
            <span 
                class="meaning" 
                v-for="meaning in wordSet" 
                :key="meaning.id"
            >
                <span class="meaningWord"> 
                    <router-link v-bind:to="{ path: 'word', query:{ 'id': meaning.id, 'lang': opstlang }}">{{ meaning.word }}</router-link>
                </span>
                <span v-if="userPriviledge > 0" class="hiddenControl">
                    <span class="pointer" v-on:click="setDeleteRelation(meaning.id, opstlang)">delete</span>
                </span>
            </span>
        </p>
    </div>
    <div v-else>
        <p>No se encuentra los significados</p>
    </div>
</template>

<script>
export default {
    emits:['sendDeleteRelation'],
    data(){
        return{
            wordSet : {},
            isMeanings: true,
            opstlang: 'esp'
        }
    },
    props:['id','lang','userPriviledge'],
    async created(){
        const data = {
            'id': this.id,
            'lang': this.lang
        }

        if(this.lang == 'miq' || this.lang == 'miskito'){
            this.opstlang = 'esp'
        }else{
            this.opstlang = 'miq' || this.lang == 'español'
        }

        await axios.post('/api/getMeanings', data)
            .then(response => this.wordSet = response.data)
            .catch(error => console.log(error));
        // console.log(this.wordSet);
        // ステータスの変更
        if(Object.keys(this.wordSet).length > 0){ this.isMeanings = true}else{ this.isMeanings = false }
    },
    methods:{
        setDeleteRelation(id, lang){
            const deleteData = {
                id: id,
                lang: lang
            };
            this.$emit('sendDeleteRelation', deleteData);
        }
    },
    watch: {
        $route(to, from){
            console.log({from});
            this.$router.go(to.fullPath);
        } 
    }
}
</script>