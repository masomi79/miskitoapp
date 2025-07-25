import { createRouter, createWebHistory } from 'vue-router'
import TopPage from '../views/TopPageView.vue'
import WordDetail from '../components/WordDetail.vue'
import AboutView from '../views/AboutView.vue'
import UserRegisterView from '../views/UserRegisterView.vue'
import MyPageView from '../views/MyPageView.vue'
import LoginView from '../views/LoginView.vue'
import LogoutView from '../views/LogoutView.vue'

const routes = [
  { path: '/', component: TopPage },
  { path: '/:lang/:id', component: WordDetail, props: true },
  { path: '/about', component: AboutView, },
  { path: '/register', component: UserRegisterView },
  { path: '/mypage', component: MyPageView},
  { path: '/login', component: LoginView, name: 'Login' },
  { path: '/logout', component: LogoutView, name: 'Logout' },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router