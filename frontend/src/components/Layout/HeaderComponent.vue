<script setup>
import { useUserStore } from '@/stores/userStore'
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const userStore = useUserStore()
const router = useRouter()

onMounted(() => {
  userStore.loadUser()
})

const logout = () => {
  userStore.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <header class="site-header">
    <div class="header-inner">
      <img src="@/assets/BEZRAB.png" class="logo" />

      <nav class="nav">
        <RouterLink :to="{ name: 'home' }" exact-active-class="active">Home</RouterLink>
        <RouterLink :to="{ name: 'freelancers' }" exact-active-class="active"
          >Freelancers</RouterLink
        >
<RouterLink
  v-if="userStore.isLoggedIn"
  :to="{ name: 'projects' }"
  exact-active-class="active"
>Projects</RouterLink>
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
          Profile ({{ userStore.user.name }})
        </RouterLink>

        <RouterLink
          v-if="userStore.isLoggedIn && userStore.user.role === 'user'"
          :to="{ name: 'ClientProfile' }"
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

.nav .active {
  font-weight: bold;
  color: #5b3df5;
}

button {
  padding: 6px 12px;
  background: #5b3df5;
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
