<template>
    <div class="share-buttons">
        <a v-bind:href='link'><span class="share-via-whatsapp"><span class="share-text">compartir por whatsApp</span></span></a>
    </div>

</template>

<script>
export default{
    data(){
        return{
            link: ''
        }
    },
    created(){

        var href = location.href;
        var param = location.search;
        var url = href.replace(param, '');

        param = encodeURIComponent(param);

        console.log(url + param);
        
        this.link = 'whatsapp://send?text=' + url + param;
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
    .share-buttons{
        text-align: center;
    }
    .share-via-whatsapp{
        background-image: url(https://miskito.org/img/whatsapp.png);
        background-position: 0px 3px;
        background-size: 30px;
        background-repeat: no-repeat;
        background-color: #27A71A;
        color: #fdfdfd;
        padding: 0.3rem 8px 0.3rem 30px;
    }
</style>