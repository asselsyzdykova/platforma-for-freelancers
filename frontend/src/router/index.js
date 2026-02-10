import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import HomeView from '../views/BasePages/HomePage.vue'
import RegisterView from '../views/BasePages/RegisterPage.vue'
import FreelancersPage from '../views/BasePages/FreelancersPage.vue'
import LoginPage from '@/views/BasePages/LoginPage.vue'
import ProjectsPage from '@/views/BasePages/ProjectsPage.vue'
import FreelancerProfilePage from '@/views/FreelancerPage/FreelancerProfilePage.vue'
import EditProfilePage from '@/views/FreelancerPage/EditProfilePage.vue'
import FreelancerPublic from '@/views/FreelancerPage/PublicProfile.vue'
import BillingAndPayments from '@/views/FreelancerPage/BillingAndPayments.vue'
import MyProposals from '@/views/FreelancerPage/MyProposals.vue'
import SettingsPage from '@/views/FreelancerPage/SettingsPage.vue'
import SupportPage from '@/views/FreelancerPage/SupportPage.vue'
import ProjectsFreelancer from '@/views/FreelancerPage/ProjectsFreelancer.vue'
import ClientPage from '@/views/ClientPage/ClientPage.vue'
import EditProfileClient from '@/views/ClientPage/EditProfileClient.vue'
import CreateProject from '@/views/ClientPage/CreateProject.vue'
import SubscriptionsPage from '@/views/BasePages/SubscriptionsPage.vue'
import SubscriptionSuccessPage from '@/views/BasePages/SubscriptionSuccessPage.vue'
import ClientInbox from '@/views/ClientPage/ClientInbox.vue'
import FreelancerInbox from '@/views/FreelancerPage/FreelancerInbox.vue'
import ApplicationDetails from '@/views/ClientPage/ApplicationDetails.vue'
import PublicProfile from '@/views/FreelancerPage/PublicProfile.vue'
import ClientChats from '@/views/ClientPage/ClientChats.vue'
import ClientProjects from '@/views/ClientPage/ClientProjects.vue'
import ClientSupport from '@/views/ClientPage/ClientSupport.vue'
import ClientSettings from '@/views/ClientPage/ClientSettings.vue'
import FreelancerChats from '@/views/FreelancerPage/FreelancerChats.vue'
import AdminProfile from '@/views/AdminPages/AdminProfile.vue'
import ManagerProfile from '@/views/AdminPages/ManagerProfile.vue'
import ProjectProposals from '@/views/ClientPage/ProjectProposals.vue'

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
      meta: { requiresAuth: true },
    },
    {
      path: '/freelancers/:id',
      name: 'FreelancerPublic',
      component: FreelancerPublic,
    },
    {
      path: '/chat/:id',
      name: 'Chat',
      component: () => import('@/views/Chat/ChatPage.vue'),
    },
    {
      path: '/projects',
      name: 'projects',
      component: ProjectsPage,
      meta: { requiresAuth: true },
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
      component: SettingsPage,
    },

    {
      path: '/support',
      name: 'Support',
      component: SupportPage,
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
      meta: { requiresAuth: true },
    },

    {
      path: '/subscriptions',
      name: 'Subscriptions',
      component: SubscriptionsPage,
    },
    {
      path: '/success',
      name: 'SubscriptionSuccess',
      component: SubscriptionSuccessPage,
    },

    {
      path: '/client/inbox',
      name: 'ClientInbox',
      component: ClientInbox,
    },

    {
      path: '/client/chats',
      name: 'ClientChats',
      component: ClientChats,
    },

    {
      path: '/client/projects',
      name: 'ClientProjects',
      component: ClientProjects,
    },
    {
      path: '/client/projects/:id/proposals',
      name: 'ClientProjectProposals',
      component: ProjectProposals,
    },

    {
      path: '/client/support',
      name: 'ClientSupport',
      component: ClientSupport,
    },

    {
      path: '/client/settings',
      name: 'ClientSettings',
      component: ClientSettings,
    },

    {
      path: '/freelancer/inbox',
      name: 'FreelancerInbox',
      component: FreelancerInbox,
    },

    {
      path: '/freelancer/chats',
      name: 'FreelancerChats',
      component: FreelancerChats,
    },

    {
      path: '/application-details/:id',
      name: 'ApplicationDetails',
      component: ApplicationDetails,
    },

    {
      path: '/public-profile/:id',
      name: 'PublicProfile',
      component: PublicProfile,
    },

    {
      path: '/admin/profile',
      name: 'AdminProfile',
      component: AdminProfile,
    },

    {
      path: '/manager/profile',
      name: 'ManagerProfile',
      component: ManagerProfile,
    },
  ],
})

router.beforeEach(async (to) => {
  if (!to.meta?.requiresAuth) return true
  const store = useUserStore()
  if (!store.user && store.token) {
    await store.loadUser()
  }
  if (!store.user) {
    return { name: 'login' }
  }
  return true
})

export default router
