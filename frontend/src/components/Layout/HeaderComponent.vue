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

      <button
        class="hamburger"
        :class="{ 'is-active': mobileMenuOpen }"
        @click="mobileMenuOpen = !mobileMenuOpen"
        aria-label="Menu"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>

      <nav :class="['nav', { 'mobile-open': mobileMenuOpen }]">
        <div class="nav-links" @click="mobileMenuOpen = false">
          <RouterLink :to="{ name: 'home' }" exact-active-class="active">Home</RouterLink>
          <RouterLink v-if="userStore.isLoggedIn" :to="{ name: 'freelancers' }" exact-active-class="active">Freelancers</RouterLink>
          <RouterLink :to="{ name: 'InternshipPage' }" exact-active-class="active">Internship</RouterLink>
          <RouterLink v-if="userStore.isLoggedIn" :to="{ name: 'projects' }" exact-active-class="active">Projects</RouterLink>
          <RouterLink :to="{ name: 'Subscriptions' }" exact-active-class="active">Subscriptions</RouterLink>

          <RouterLink v-if="!userStore.isLoggedIn" :to="{ name: 'login' }" exact-active-class="active">Log In</RouterLink>
          <RouterLink v-if="!userStore.isLoggedIn" :to="{ name: 'register' }" exact-active-class="active">Sign Up</RouterLink>

          <RouterLink v-if="userStore.isLoggedIn && userStore.user.role === 'freelancer'" :to="{ name: 'FreelancerProfile' }" exact-active-class="active">
            Profile ({{ userStore.user.name }}{{ planSuffix }})
          </RouterLink>
          <RouterLink v-else-if="userStore.isLoggedIn" :to="{ name: userStore.user.role === 'admin' ? 'AdminProfile' : userStore.user.role === 'manager' ? 'ManagerProfile' : 'ClientProfile' }" exact-active-class="active">
            Profile ({{ userStore.user.name }})
          </RouterLink>

          <button v-if="userStore.isLoggedIn" class="logout-btn" @click="logout">Logout</button>
        </div>
      </nav>
    </div>
  </header>
</template>
<style scoped>
.site-header {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 0 24px;
  height: 70px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  position: sticky;
  top: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.logo {
  height: 32px;
  object-fit: contain;
}

.nav {
  display: flex;
  align-items: center;
}

.nav-links {
  display: flex;
  gap: 8px;
  align-items: center;
}

.nav a {
  color: #1e293b;
  text-decoration: none;
  font-weight: 500;
  font-size: 14px;
  padding: 8px 16px;
  border-radius: 12px;
  transition: all 0.2s ease;
}

.nav a:hover, .nav .active {
  background: #f3efff;
  color: #5D3A9B;
}

.logout-btn {
  margin-left: 10px;
  padding: 8px 20px;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}

.hamburger {
  display: none;
  width: 40px;
  height: 40px;
  position: relative;
  background: #f8fafc;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  z-index: 1101;
}

.hamburger span {
  display: block;
  position: absolute;
  height: 2px;
  width: 22px;
  background: #5D3A9B;
  border-radius: 2px;
  left: 9px;
  transition: 0.25s ease-in-out;
}

.hamburger span:nth-child(1) { top: 14px; }
.hamburger span:nth-child(2) { top: 19px; }
.hamburger span:nth-child(3) { top: 24px; }

.hamburger.is-active span:nth-child(1) {
  top: 19px;
  transform: rotate(135deg);
}

.hamburger.is-active span:nth-child(2) {
  opacity: 0;
  left: -20px;
}

.hamburger.is-active span:nth-child(3) {
  top: 19px;
  transform: rotate(-135deg);
}

@media (max-width: 992px) {
  .hamburger {
    display: block;
  }

  .nav {
    position: fixed;
    top: 0;
    right: -100%;
    width: 280px;
    height: 100vh;
    background: white;
    box-shadow: -10px 0 30px rgba(0,0,0,0.1);
    transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 80px 24px 40px;
  }

  .nav.mobile-open {
    right: 0;
  }

  .nav-links {
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
    gap: 12px;
  }

  .nav a {
    width: 100%;
    font-size: 16px;
    padding: 14px;
    border-radius: 12px;
    background: #f8fafc;
  }

  .logout-btn {
    margin-left: 0;
    margin-top: 20px;
    width: 100%;
    padding: 16px;
  }
}
</style>

