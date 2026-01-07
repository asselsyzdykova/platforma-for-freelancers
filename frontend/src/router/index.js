import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/BasePages/HomePage.vue'
import RegisterView from '../views/BasePages/RegisterPage.vue'
import FreelancersPage from '../views/BasePages/FreelancersPage.vue'
import LoginPage from '@/views/BasePages/LoginPage.vue'
import ProjectsPage from '@/views/BasePages/ProjectsPage.vue'
import FreelancerProfilePage from '@/views/FreelancerPage/FreelancerProfilePage.vue'
import EditProfilePage from '@/views/FreelancerPage/EditProfilePage.vue'
import BillingAndPayments from '@/views/FreelancerPage/BillingAndPayments.vue'
import MyProposals from '@/views/FreelancerPage/MyProposals.vue'
import SettingsPage from '@/views/FreelancerPage/SettingsPage.vue'
import SupportPage from '@/views/FreelancerPage/SupportPage.vue'
import ProjectsFreelancer from '@/views/FreelancerPage/ProjectsFreelancer.vue'
import ClientPage from '@/views/ClientPage/ClientPage.vue'
import EditProfileClient from '@/views/ClientPage/EditProfileClient.vue'
import CreateProject from '@/views/ClientPage/CreateProject.vue'
import SubscriptionsPage from '@/views/BasePages/SubscriptionsPage.vue'
import ClientInbox from '@/views/ClientPage/ClientInbox.vue'
import FreelancerInbox from '@/views/FreelancerPage/FreelancerInbox.vue'
import ApplicationDetails from '@/views/ClientPage/ApplicationDetails.vue'

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
},

{
  path: '/billing-and-payments',
  name: 'BillingAndPayments',
  component: BillingAndPayments,
},

{
  path: '/my-proposals',
  name: 'MyProposals',
  component: MyProposals,
},

{
  path: '/settings',
  name: 'Settings',
  component: SettingsPage
},

{
  path: '/support',
  name: 'Support',
  component: SupportPage
},

{
  path: '/my-projects',
  name: 'MyProjects',
  component: ProjectsFreelancer,
},

{
  path: '/client-profile',
  name: 'ClientProfile',
  component: ClientPage,
},

{
  path: '/edit-client-profile',
  name: 'EditClientProfile',
  component: EditProfileClient,
},

{
  path: '/create-project',
  name: 'CreateProject',
  component: CreateProject,
},

{
  path: '/subscriptions',
  name: 'Subscriptions',
  component: SubscriptionsPage,
},

{
  path:'/client/inbox',
  name: 'ClientInbox',
  component: ClientInbox
},

{
  path: '/freelancer/inbox',
  name: 'FreelancerInbox',
  component: FreelancerInbox
},

{
  path: '/application-details/:id',
  name: 'ApplicationDetails',
  component: ApplicationDetails,
}
  ],
})

export default router
