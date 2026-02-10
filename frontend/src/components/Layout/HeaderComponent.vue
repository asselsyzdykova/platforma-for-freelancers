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
          >Login</RouterLink
        >
        <RouterLink
          v-if="!userStore.isLoggedIn"
          :to="{ name: 'register' }"
          exact-active-class="active"
          >Register</RouterLink
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
  padding: 16px 32px;
  background-color: #e6e0ff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.header-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  height: 30px;
}

.nav {
  display: flex;
  gap: 16px;
  align-items: center;
}

@media (max-width: 640px) {
  .site-header {
    padding: 12px 16px;
  }

  .header-inner {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .nav {
    flex-wrap: wrap;
    gap: 10px 12px;
  }

  .nav a,
  .nav button {
    font-size: 14px;
  }
}

.nav .active {
  font-weight: bold;
  color: linear-gradient(135deg, #5D3A9B, #7c3aed);
}

button {
  padding: 6px 12px;
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s;
}

button:hover {
  opacity: 0.9;
}
</style>
