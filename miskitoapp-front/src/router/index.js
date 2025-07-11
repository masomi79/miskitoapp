import { createRouter, createWebHistory } from 'vue-router'
import WordSearch from '../components/WordSearch.vue'
import WordDetail from '../components/WordDetail.vue'
import AboutView from '../views/AboutView.vue'
import UserRegisterView from '../views/UserRegisterView.vue'

const routes = [
  { path: '/', component: WordSearch },
  { path: '/:lang/:id', component: WordDetail, props: true },
  { path: '/about', name: 'About', component: AboutView, },
  { path: '/register', component: UserRegisterView }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router