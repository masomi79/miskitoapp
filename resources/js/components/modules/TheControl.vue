<template>
        <div class="controlPannel">
            <button class="normal" v-on:click="sendModalControl('newWord')">nueva palabra</button>
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