<script setup>
import { useUserStore } from '@/stores/userStore'
import { useRouter } from 'vue-router'
import { onMounted, computed } from 'vue'

const userStore = useUserStore()
const router = useRouter()

onMounted(() => {
  userStore.loadUser()
})

const logout = () => {
  userStore.logout()
  router.push({ name: 'login' })
}

const planSuffix = computed(() => {
  const plan = userStore.user?.plan
  if (!plan) return ''
  const label = String(plan).toLowerCase()
  const pretty = label.charAt(0).toUpperCase() + label.slice(1)
  return ` (${pretty})`
})
</script>

<template>
  <header class="site-header">
    <div class="header-inner">
      <img src="@/assets/BEZRAB.png" class="logo" />

      <nav class="nav">
        <RouterLink :to="{ name: 'home' }" exact-active-class="active">Home</RouterLink>
        <RouterLink
          v-if="userStore.isLoggedIn"
          :to="{ name: 'freelancers' }"
          exact-active-class="active"
          >Freelancers</RouterLink
        >
        <RouterLink :to="{ name: 'InternshipPage' }" exact-active-class="active">Internship</RouterLink>
        <RouterLink
          v-if="userStore.isLoggedIn"
          :to="{ name: 'projects' }"
          exact-active-class="active"
          >Projects</RouterLink
        >
        <RouterLink :to="{ name: 'Subscriptions' }" exact-active-class="active"
          >Subscriptions</RouterLink
        >

        <RouterLink v-if="!userStore.isLoggedIn" :to="{ name: 'login' }" exact-active-class="active"
          >Log In</RouterLink
        >
        <RouterLink
          v-if="!userStore.isLoggedIn"
          :to="{ name: 'register' }"
          exact-active-class="active"
          >Sign Up</RouterLink
        >

        <RouterLink
          v-if="userStore.isLoggedIn && userStore.user.role === 'freelancer'"
          :to="{ name: 'FreelancerProfile' }"
          exact-active-class="active"
        >
          Profile ({{ userStore.user.name }}{{ planSuffix }})
        </RouterLink>

        <RouterLink
          v-if="userStore.isLoggedIn && userStore.user.role === 'user'"
          :to="{ name: 'ClientProfile' }"
          exact-active-class="active"
        >
          Profile ({{ userStore.user.name }})
        </RouterLink>

        <RouterLink
          v-if="userStore.isLoggedIn && userStore.user.role === 'admin'"
          :to="{ name: 'AdminProfile' }"
          exact-active-class="active"
        >
          Profile ({{ userStore.user.name }})
        </RouterLink>

        <RouterLink
          v-if="userStore.isLoggedIn && userStore.user.role === 'manager'"
          :to="{ name: 'ManagerProfile' }"
          exact-active-class="active"
        >
          Profile ({{ userStore.user.name }})
        </RouterLink>

        <button v-if="userStore.isLoggedIn" @click="logout">Logout</button>
      </nav>
    </div>
  </header>
</template>

<style scoped>
.site-header {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);  padding: 16px 32px;
  background-color: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-inner {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  gap: 20px;
}

.logo {
  height: 40px;
  transition: transform 0.3s ease;
}
.logo:hover {
  transform: scale(1.05);
}

.nav {
  display: flex;
  justify-content: center;
  gap: 20px;
  align-items: center;
  flex-wrap: wrap;
}

.nav a {
  color: linear-gradient(135deg, #5D3A9B, #7c3aed);
  text-decoration: none;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 20px;
  transition: all 0.3s ease;
  position: relative;
}
.nav a:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
}

.nav .active {
  background-color: rgba(255, 255, 255, 0.3);
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.nav button {
  padding: 8px 16px;
  background: linear-gradient(135deg, #ff6b6b, #ee5a24);
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.nav button:hover {
  background: linear-gradient(135deg, #ee5a24, #ff6b6b);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

@media (max-width: 768px) {
  .site-header {
    padding: 12px 16px;
  }

  .header-inner {
    flex-direction: column;
    gap: 12px;
  }

  .nav {
    gap: 12px;
  }

  .nav a,
  .nav button {
    font-size: 14px;
    padding: 6px 12px;
  }
}

@media (max-width: 480px) {
  .nav {
    flex-direction: column;
    width: 100%;
  }
   .nav a,
  .nav button {
    width: 100%;
    text-align: center;
  }
}
</style>
