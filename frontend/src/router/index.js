import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/BasePages/HomePage.vue'
import RegisterView from '../views/BasePages/RegisterPage.vue'
import FreelancersPage from '../views/BasePages/FreelancersPage.vue'
import LoginPage from '@/views/BasePages/LoginPage.vue'
import ProjectsPage from '@/views/BasePages/ProjectsPage.vue'
import FreelancerProfilePage from '@/views/FreelancerPage/FreelancerProfilePage.vue'
import EditProfilePage from '@/views/FreelancerPage/EditProfilePage.vue'

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

    {
  path: '/freelancer-profile',
  name: 'FreelancerProfile',
  component: FreelancerProfilePage,
},

{
  path: '/edit-profile',
  name: 'EditProfile',
  component: EditProfilePage,
}

  ],
})

export default router
