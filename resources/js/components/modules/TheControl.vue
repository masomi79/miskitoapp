<template>
    <p class="tal"><span v-on:click="toggleControl()">Control</span></p>
    <transition name="fade">
        <div v-if="showControl">
            <div class="control-pannel">
                <button v-on:click="sendModalControl('newWord')">nueva palabra</button>
                <button v-on:click="sendModalControl('newExample')">nuevo ejemplo</button>
            </div>
        </div>
    </transition>
</template>
<script>
    export default{
        data(){
            return{
                showControl: false,
            }
        },
        emits : ['catchModalControl'],
        methods:{
            sendModalControl(type){
                this.$emit('catchModalControl',type);
            },
            toggleControl(){
                this.showControl = !this.showControl;
            },
        }
    }
</script>
<style scoped>
    .control-pannel{
        padding: 2rem;
        background-color: #f8fafc;
        text-align: left;
    }
</style>