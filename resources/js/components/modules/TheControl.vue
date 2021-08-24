<template>
        <div class="controlPannel">
            <p>Cotrol panel</p>
            <button class="normal" v-on:click="sendModalControl('newWord')">nueva palabra</button>
            <button class="normal" v-on:click="sendModalControl('newExample')">Nuevo Ejemplo Para Esta Palabra</button>
        </div>
</template>
<style scoped>
    .controlPannel{
        text-align: center;
        padding: 2rem 0;
    }
</style>
<script>
    export default{
        emits : ['catchModalControl'],
        methods:{
            sendModalControl(type){
                this.$emit('catchModalControl',type);
            }
        }
    }
</script>