import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomePage.vue'
import RegisterView from '../views/RegisterPage.vue'
import FreelancersPage from '../views/FreelancersPage.vue'
import LoginPage from '@/views/LoginPage.vue'
import ProjectsPage from '@/views/ProjectsPage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: LoginPage,
    },
    {
      path: '/freelancers',
      name: 'freelancers',
      component: FreelancersPage,
    },
    {
      path: '/projects',
      name: 'projects',
      component: ProjectsPage,
    },
  ],
})

export default router
