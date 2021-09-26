<template>
    <div class="container">
        <p>Resultado de la busqueda [ {{wordToSearch}} ]</p>
        <searched-meanings
            v-bind:wordToSearch="wordToSearch"
        ></searched-meanings>
    </div>
</template>
<script>
import SearchedMeanings from './SearchedMeanings.vue'
export default {
  components: { SearchedMeanings },
    props:{
        wordToSearch: {
            type: String,
            default: 'hoge'
        }
    }
}
</script>