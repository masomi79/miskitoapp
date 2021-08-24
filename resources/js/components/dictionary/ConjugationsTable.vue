<template>    
    <h3>Conjugación</h3>
    <table>
        <tbody>
            <tr>
                <td></td>
                <td>primer persona</td>
                <td>segunda persona</td>
                <td>tercer persona</td>
            </tr>
            <tr>
                <td>Pasado</td>
                <td>{{ conj.pd1 }}</td>
                <td>{{ conj.pd2 }}</td>
                <td>{{ conj.pd3 }}</td>
            </tr>
            <tr>
                <td>presente</td>
                <td>{{ conj.pr1 }}</td>
                <td>{{ conj.pr2 }}</td>
                <td>{{ conj.pr3 }}</td>
            </tr>
            <tr>
                <td>Futuro</td>
                <td>{{ conj.ft1 }}</td>
                <td>{{ conj.ft2 }}</td>
                <td>{{ conj.ft3 }}</td>
            </tr>
        </tbody>
    </table>
</template>
<script>
export default {
    inheritAttrs: false,
    props:['word'],
    data(){
        return{
            conj: {
                'pd1': '',
                'pd2': '',
                'pd3': '',
                'pr1': '',
                'pr2': '',
                'pr3': '',
                'ft1': '',
                'ft2': '',
                'ft3': ''
            }
        }
    },
    created(){
        var route = this.word.slice(0,-3);
        this.conj.pd1 = route + 'ri';
        this.conj.pd2 = route + 'ram';
        this.conj.pd3 = route + 'an';
        this.conj.pr1 = route + 'isna';
        this.conj.pr2 = route + 'isma';
        this.conj.pr3 = route + 'isa';
        this.conj.ft1 = route + 'amna';
        this.conj.ft2 = route + 'ma';
        this.conj.ft3 = route + 'bia';
    }
}
</script>