<template>
  <div class="profile-layout">
    <ClientSidebar />

    <div class="settings-page">
      <h1>Settings</h1>
      <p class="subtitle">Change your account details and password</p>

      <div class="card">
        <h3>Profile</h3>
        <form @submit.prevent="saveProfile">
          <label>
            Name
            <input v-model="form.name" required />
          </label>

          <label>
            Language
            <select v-model="form.language">
              <option value="en">English</option>
              <option value="ru">Русский</option>
              <option value="kk">Қазақша</option>
            </select>
          </label>

          <div class="actions">
            <button class="primary-btn">Save profile</button>
          </div>
        </form>
      </div>

      <div class="card">
        <h3>Change password</h3>
        <form @submit.prevent="changePassword">
          <label>
            Current password
            <input v-model="password.current" type="password" required />
          </label>

          <label>
            New password
            <input v-model="password.new" type="password" required />
          </label>

          <label>
            Confirm new password
            <input v-model="password.confirm" type="password" required />
          </label>

          <div class="actions">
            <button class="primary-btn">Change password</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'

export default {
  name: 'ClientSettings',
  components: { ClientSidebar },
  data() {
    return {
      form: {
        name: '',
        language: 'en',
      },
      password: {
        current: '',
        new: '',
        confirm: '',
      },
    }
  },
  async mounted() {
    try {
      const res = await api.get('/client/profile')
      this.form = { ...this.form, ...res.data }
    } catch (e) {
      console.warn('Could not load profile', e)
    }
  },
  methods: {
    async saveProfile() {
      try {
        const formData = new FormData()
        formData.append('name', this.form.name)
        formData.append('language', this.form.language)

        await api.post('/client/profile', formData)
        alert('Profile updated')
      } catch (e) {
        console.error('Failed to save profile', e)
        alert('Failed to save profile')
      }
    },

    async changePassword() {
      if (this.password.new !== this.password.confirm) {
        alert("New passwords don't match")
        return
      }

      try {
        await api.post('/change-password', {
          current_password: this.password.current,
          new_password: this.password.new,
        })

        alert('Password changed')
        this.password = { current: '', new: '', confirm: '' }
      } catch (e) {
        console.error('Failed to change password', e)
        alert('Failed to change password')
      }
    },
  },
}
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}
.settings-page {
  width: 60%;
    padding: 30px;
    margin: 0 auto;
}
.subtitle {
  color: #666;
  margin-bottom: 20px;
}
.card {
  background: #fff;
  padding: 18px;
  border-radius: 12px;
  margin-bottom: 18px;
}
.card h3 {
  margin-bottom: 12px;
}
label {
  display: block;
  margin-bottom: 10px;
}
input,
select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}
.actions {
  margin-top: 12px;
}
.primary-btn {
  padding: 10px 14px;
  background: #5b4b8a;
  color: white;
  border: none;
  border-radius: 8px;
}
</style>
