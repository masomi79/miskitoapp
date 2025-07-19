<template>
    <div>
        <WordSearch />
        <h1>Bienvenido a miskito.org</h1>
        <nav>
        <ul>
            <li><router-link to="/atout">Sobre Nosotros</router-link></li>
            <li><router-link to="/login">Iniciar Sesión</router-link></li>
            <li><router-link to="/register">Registrarse</router-link></li>
        </ul>
        </nav>
    </div>
</template>

<script>
import WordSearch from '../components/WordSearch.vue'

export default {
  components: {
    WordSearch
  }
}
</script>

<style scope>
    h1{
        margin: 4em auto 1em;
    }
    nav ul{
        display: flex;
        justify-content: space-around;
    }
    nav ul li {
        margin-right: 10px;
        list-style: none;
        justify-content: center;
        align-items: center;
        display: inline-block;
    }
    nav ul li a {
        text-decoration: none;
        color: #42b883;
    }
    nav ul li a:hover {
        text-decoration: underline;
    }
</style>