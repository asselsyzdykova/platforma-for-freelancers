<template>
  <div class="login-page">
    <div class="login-card">
      <h1>Login</h1>

      <form @submit.prevent="login">
        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" type="email" v-model="email" placeholder="example@email.com" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" type="password" v-model="password" placeholder="••••••••" required />
        </div>

        <button type="submit">Login</button>
      </form>

      <p class="register-link">
        No account yet?
        <RouterLink :to="{ name: 'register' }">Register</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/axios'
import { useUserStore } from '@/stores/userStore'
import { useNotificationStore } from '@/stores/notificationStore'

const email = ref('')
const password = ref('')
const router = useRouter()
const userStore = useUserStore()
const notifications = useNotificationStore()

const login = async () => {
  if (!email.value || !password.value) {
    notifications.warning('Please fill in all fields')
    return
  }

  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    userStore.setToken(response.data.access_token)
    await userStore.loadUser()
    if (!userStore.user) {
      userStore.setUser(response.data.user)
    }

    if (response.data.user.role === 'freelancer') {
      router.push('/freelancer-profile')
    } else {
      router.push('/client-profile')
    }
  } catch (error) {
    console.error(error)
    notifications.error(error.response?.data?.message || 'Login failed')
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 20px;
}

.login-card {
  width: 100%;
  max-width: 400px;
  background: #f3efff;
  padding: 2rem;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.3rem;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 0.6rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 1rem;
}

button {
  width: 100%;
  margin-top: 1rem;
  padding: 0.7rem;
  background: #4f46e5;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  cursor: pointer;
}

button:hover {
  background: #4338ca;
}

.register-link {
  margin-top: 1rem;
  text-align: center;
  font-size: 0.9rem;
  color: #5b4b8a;
}
</style>
