<template>
  <div class="register-page">
    <div class="register-card">
      <h1>Create account</h1>
      <p class="subtitle">Join BezRab today</p>

      <form @submit.prevent="register">
        <div class="form-group">
          <label>Name</label>
          <input v-model="form.name" type="text" required />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" required />
        </div>

        <div v-if="form.role === 'freelancer'">
          <div class="form-group select-group">
            <select v-model="form.university" required>
               <option value="" disabled selected hidden>Select your university</option>
              <optgroup label="Bratislava">
                <option disabled class="optgroup-separator">────────</option>
                <option>Univerzita Komenského v Bratislave</option>
                <option>Slovenská technická univerzita v Bratislave</option>
                <option>Ekonomická univerzita v Bratislave</option>
                <option>Slovenská zdravotnícka univerzita v Bratislave</option>
              </optgroup>

              <optgroup label="Kosice">
                <option disabled class="optgroup-separator">────────</option>
                <option>Technická univerzita v Košiciach</option>
                <option>Univerzita Pavla Jozefa Šafárika v Košiciach</option>
                <option>Univerzita veterinárskeho lekárstva a farmácie v Košiciach</option>
              </optgroup>

              <optgroup label="Nitra">
                <option disabled class="optgroup-separator">────────</option>
                <option>Univerzita Konštantína Filozofa v Nitre</option>
                <option>Slovenská poľnohospodárska univerzita v Nitre</option>
              </optgroup>

              <optgroup label="Trnava">
                <option disabled class="optgroup-separator">────────</option>
                <option>Trnavská univerzita v Trnave</option>
                <option>Univerzita sv. Cyrila a Metoda v Trnave</option>
              </optgroup>

              <optgroup label="Banska-Bystrica">
                <option disabled class="optgroup-separator">────────</option>
                <option>Univerzita Mateja Bela v Banskej Bystrici</option>
              </optgroup>

              <optgroup label="Zilina">
                <option disabled class="optgroup-separator">────────</option>
                <option>Žilinská univerzita v Žiline</option>
              </optgroup>

              <optgroup label="Presov">
                <option disabled class="optgroup-separator">────────</option>
                <option>Prešovská univerzita v Prešove</option>
              </optgroup>

              <optgroup label="Zloven">
                <option disabled class="optgroup-separator">────────</option>
                <option>Technická univerzita vo Zvolene</option>
              </optgroup>
            </select>

            <select v-model="form.study_year" required>
              <option value="" disabled selected hidden>Select your study year</option>
              <optgroup label="Rok studia">
                <option disabled class="optgroup-separator">────────</option>
                <option>1. ročník</option>
                <option>2. ročník</option>
                <option>3. ročník</option>
                <option>4. ročník</option>
                <option>5. ročník</option>
              </optgroup>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="password" required />
        </div>

        <div class="form-group">
          <label>Confirm password</label>
          <input v-model="form.passwordConfirm" type="password" required />
        </div>

        <div class="form-group">
          <label>Register as</label>
          <div class="role-selector">
            <button
              type="button"
              :class="{ active: form.role === 'user' }"
              @click="form.role = 'user'"
            >
              👤 Client
            </button>

            <button
              type="button"
              :class="{ active: form.role === 'freelancer' }"
              @click="form.role = 'freelancer'"
            >
              💼 Freelancer(Student)
            </button>
          </div>
        </div>

        <button type="submit">Register</button>
      </form>

      <p class="login-link">
        Already have an account?
        <RouterLink :to="{ name: 'login' }">Login</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../services/axios'
import { useUserStore } from '@/stores/userStore'
import { useNotificationStore } from '@/stores/notificationStore'

const router = useRouter()
const userStore = useUserStore()
const notifications = useNotificationStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirm: '',
  role: 'user',
  university: '',
  study_year: '',
})

const register = async () => {
  if (form.password !== form.passwordConfirm) {
    notifications.warning('Passwords do not match')
    return
  }

  try {
    const response = await api.post('/register', {
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.passwordConfirm,
      role: form.role,
      university: form.university,
      study_year: form.studyYear,
    })

    userStore.setToken(response.data.access_token)
    await userStore.loadUser()
    if (!userStore.user) {
      userStore.setUser(response.data.user)
    }

    if (form.role === 'freelancer') {
      router.push('/freelancer-profile')
    } else {
      router.push('/client-profile')
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      notifications.error(Object.values(error.response.data.errors).flat().join('\n'))
    } else if (error.response?.data?.message) {
      notifications.error(error.response.data.message)
    } else {
      notifications.error('Registration failed')
    }
  }
}
</script>

<style scoped>
.register-page {
  min-height: calc(100vh - 160px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
}

.register-card {
  width: 100%;
  max-width: 420px;
  background: #f3efff;
  padding: 32px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

h1 {
  margin-bottom: 8px;
  font-size: 26px;
  text-align: center;
}

.subtitle {
  text-align: center;
  color: #777;
  margin-bottom: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 16px;
}

label {
  font-size: 14px;
  margin-bottom: 6px;
}

input {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #ddd;
  font-size: 14px;
}

input:focus {
  outline: none;
  border-color: #5b3df5;
}

select {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #ddd;
  font-size: 14px;
  background: white;
}

select:focus {
  outline: none;
  border-color: #5b3df5;
}

optgroup {
  font-weight: 600;
  color: #5b4b8a;
  background: #f7f5ff;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 6px;
  margin-bottom: 6px;
}

option {
  color: #2f2f2f;
}

option.optgroup-separator {
  color: #cbd5f5;
}

.select-group {
  gap: 12px;
}

.select-group select + select {
  margin-top: 12px;
}

button {
  width: 100%;
  padding: 12px;
  margin-top: 8px;
  background: #5b3df5;
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  cursor: pointer;
}

button:hover {
  opacity: 0.9;
}

.login-link {
  color: #5b4b8a;
  margin-top: 20px;
  text-align: center;
  font-size: 14px;
}

.role-selector {
  display: flex;
  gap: 12px;
}

.role-selector button {
  flex: 1;
  padding: 12px;
  border-radius: 12px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}

.role-selector button.active {
  background: #5b3df5;
  color: white;
  border-color: #5b3df5;
}

.role-selector button:hover {
  border-color: #5b3df5;
}
</style>
