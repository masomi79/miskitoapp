<template>
    <div class="container">
        <div class="about-us">

            <h3>¿Qué Es Eso?</h3>
            <p>Este diccionario miskito - español en linea es un proyecto personal de  <a href="https://massumifukuda.work/wp/" target="_blank">massumi fukuda</a> para compartir mutualmente el conocimiento de linguage miskito. Para saber como se funciona, ponga una palabra en miskito o en español,  clique el buttón y vea como se dice esa palabra en otra idioma.</p>


            <h3>Estadistica</h3>
            <total-words></total-words>

            <h3>Colaboración</h3>
            <p>Nos ayudan en la creación de la mayor miskito - español (y japonés) diccionario en línea. Simplemente <span><a href="/home">inicie una sesión</a></span> y añadir una nueva traducción. es un proyecto personal pero colaborativo y cada uno puede añadir (y eliminar) traducciones. Hace que nuestro diccionario miskito español real, ya que es creado por personas que viven en la región y usa el lenguaje de cada día. También puede estar seguro, que cualquier error en el diccionario se repara rápidamente, así que usted puede confiar en los datos. Si encuentra algún error o que son capaces de añadir nuevos datos: por favor, hazlo. Miles de personas se lo agradecerán para hacerlo.</p>
            <p>Nos ayudan a enriquecer el diccionario y ver como el conocimiento tuyo ayuda a los demás!</p>

            <h3>Historial</h3>
            <dl>
            <dt>2017.08.01</dt>
            <dd>publicado</dd>
            <dt>2018.11.01</dt>
            <dd>10 usuarios</dd>
            <dt>2020.08.01</dt>
            <dd>remodelado por Laravel + vue.js y cambió diseño</dd>
            </dl>
            <div>
            <latest-examples></latest-examples>
            <h3>Links</h3>
            <ul>
                <li><a href="https://www.bible.com/bible/433/MAT.1.MSKTU" target="_blank">Dawan Bîla Aiska</a></li>
                <li><a href="https://www.jw.org/miq" target="_blank">jw.org</a></li>
                <li><a href="https://wol.jw.org/miq/wol/h/r152/lp-mis" target="_blank">wol.jw.org</a></li>
            </ul>
            <h3 id="sushi">Contacto</h3>
            <p>Contactenos : <a href="https://massumifukuda.work/wp/contact" target="_blank">contact form</a></p>
            <p>Me invita un Sushi : <a href="https://www.paypal.com/paypalme2/masomi79/25usd" target="_blank">🍣🍣🍶</a></p>
            </div>
        </div>
    </div>
</template>
<script>
    import TotalWords from './modules/TotalWords';
    import LatestExamples from './modules/LatestExamples';
    export default{
        components:{
            TotalWords,
            LatestExamples
        }
    }
</script>
<style scoped>

    h3{
        color: #fff;
        color: #292929;
        margin: 1.2rem 0 0.5rem;
    }
    .container{
        margin: 4rem auto 1.2rem;
        padding-top: 1.2rem;
        border-top: solid lightgray 1px;
    }
    .about-us{
        text-align: left;
    }

    dl{
        margin-left: 1rem;
        color: #292929;
    }
    dd{
        margin-left: 1rem;
        color: #292929;
    }
</style>