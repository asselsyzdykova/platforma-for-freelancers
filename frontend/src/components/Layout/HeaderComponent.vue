<script setup>
import { useUserStore } from '@/stores/userStore'
import { useRouter } from 'vue-router'
import { ref, onMounted, computed } from 'vue'

const userStore = useUserStore()
const router = useRouter()

const mobileMenuOpen = ref(false)

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

      <button class="hamburger" @click="mobileMenuOpen = !mobileMenuOpen">
        <span :class="{ open: mobileMenuOpen }"></span>
        <span :class="{ open: mobileMenuOpen }"></span>
        <span :class="{ open: mobileMenuOpen }"></span>
      </button>

      <nav :class="['nav', { 'mobile-open': mobileMenuOpen }]">
        <RouterLink :to="{ name: 'home' }" exact-active-class="active">Home</RouterLink>
        <RouterLink v-if="userStore.isLoggedIn" :to="{ name: 'freelancers' }" exact-active-class="active">Freelancers
        </RouterLink>
        <RouterLink :to="{ name: 'InternshipPage' }" exact-active-class="active">Internship</RouterLink>
        <RouterLink v-if="userStore.isLoggedIn" :to="{ name: 'projects' }" exact-active-class="active">Projects
        </RouterLink>
        <RouterLink :to="{ name: 'Subscriptions' }" exact-active-class="active">Subscriptions</RouterLink>

        <RouterLink v-if="!userStore.isLoggedIn" :to="{ name: 'login' }" exact-active-class="active">Log In</RouterLink>
        <RouterLink v-if="!userStore.isLoggedIn" :to="{ name: 'register' }" exact-active-class="active">Sign Up
        </RouterLink>

        <RouterLink v-if="userStore.isLoggedIn && userStore.user.role === 'freelancer'"
          :to="{ name: 'FreelancerProfile' }" exact-active-class="active">
          Profile ({{ userStore.user.name }}{{ planSuffix }})
        </RouterLink>
        <RouterLink v-if="userStore.isLoggedIn && userStore.user.role === 'user'" :to="{ name: 'ClientProfile' }"
          exact-active-class="active">
          Profile ({{ userStore.user.name }})
        </RouterLink>

        <RouterLink v-if="userStore.isLoggedIn && userStore.user.role === 'admin'" :to="{ name: 'AdminProfile' }"
          exact-active-class="active">
          Profile ({{ userStore.user.name }})
        </RouterLink>

        <RouterLink v-if="userStore.isLoggedIn && userStore.user.role === 'manager'" :to="{ name: 'ManagerProfile' }"
          exact-active-class="active">
          Profile ({{ userStore.user.name }})
        </RouterLink>

        <button v-if="userStore.isLoggedIn" @click="logout">Logout</button>
      </nav>
    </div>
  </header>
</template>

<style scoped>
.site-header {
  background: white;
  opacity: 0.9;
  backdrop-filter: blur(10px);
  padding: 16px 32px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
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
  gap: 16px;
  align-items: center;
  flex-wrap: wrap;
}

.nav a {
  color: #5D3A9B;
  text-decoration: none;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.nav a:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.nav .active {
  font-weight: bold;
  background-color: rgba(255, 255, 255, 0.3);
}

.nav button {
  padding: 8px 16px;
  background: linear-gradient(135deg, #ff6b6b, #ee5a24);
  color: #5D3A9B;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 500;
}

.hamburger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
}

.hamburger span {
  display: block;
  width: 25px;
  height: 3px;
  background: #5D3A9B;
  border-radius: 3px;
  transition: all 0.3s ease;
}

.hamburger span.open:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}

.hamburger span.open:nth-child(2) {
  opacity: 0;
}

.hamburger span.open:nth-child(3) {
  transform: rotate(-45deg) translate(5px, -5px);
}

@media (max-width: 768px) {
  .hamburger {
    display: flex;
  }

  .nav {
    position: absolute;
    top: 72px;
    right: 0;
    flex-direction: column;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 16px;
    border-radius: 12px;
    gap: 12px;
    display: none;
    min-width: 180px;
    backdrop-filter: blur(10px);
  }

  .nav.mobile-open {
    display: flex;
  }
}
</style>
