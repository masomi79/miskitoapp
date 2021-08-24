
import { createRouter, createWebHistory } from 'vue-router';


import MainApp from './components/MainApp.vue';
import PrivacyPolicy from './pages/PrivacyPolicy.vue';
import WordDetail from './pages/WordDetail.vue';

const router = createRouter({
   // mode: history,
    history: createWebHistory(),
    routes: [
        {
            name: 'main',
            path: '/', //
            component: MainApp
        },
        {
            name: 'privacy',
            path: '/privacy', //
            component: PrivacyPolicy
        },
        {
            name: 'wordDetail',
            path: '/word',
            component: WordDetail,
            props: true
        }
    ]
});

export default router;