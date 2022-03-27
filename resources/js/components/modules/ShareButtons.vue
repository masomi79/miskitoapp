<template>
    <div class="share-buttons">  
        <a v-bind:href='link'>Compartir por WhatsApp</a>
    </div>

</template>

<script>
export default{
    data(){
        return{
            path: '',
            link: ''
        }
    },
    created(){
        this.path = window.location.href;
        this.link = 'whatsapp://send?text=' + this.path;
        console.log(this.link);
    }
}
</script>

<style scoped>
    @media screen and (min-width: 500px) {
        .share-buttons {
            display: none
        }
    }
</style>