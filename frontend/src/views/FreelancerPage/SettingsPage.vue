<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>Settings</h1>

      <div
        v-if="showDeleteModal"
        class="modal-backdrop"
        @click.self="closeDeleteModal"
      >
        <div class="modal-card">
          <h3>Delete account?</h3>
          <p>This action can’t be undone.</p>
          <div class="modal-actions">
            <button class="secondary" @click="closeDeleteModal">
              Cancel
            </button>
            <button class="danger" @click="confirmDelete">
              Delete
            </button>
          </div>
        </div>
      </div>

      <div class="settings-card">
        <div class="setting-item">
          <label>Full name</label>
          <input
            type="text"
            v-model="form.name"
            placeholder="Your name"
          />
        </div>

        <div class="setting-item">
          <label>Email</label>
          <input
            type="email"
            v-model="form.email"
            placeholder="your@email.com"
          />
        </div>

        <div class="setting-item">
          <label>Current password</label>
          <input
            type="password"
            v-model="form.current_password"
            placeholder="Enter current password"
          />
        </div>

        <div class="setting-item">
          <label>New password</label>
          <input
            type="password"
            v-model="form.new_password"
            placeholder="Enter new password"
          />
        </div>

        <div class="setting-item">
          <label>Confirm new password</label>
          <input
            type="password"
            v-model="form.new_password_confirmation"
            placeholder="Confirm new password"
          />
        </div>

        <div class="setting-item">
          <label>Language</label>
          <select>
            <option>English</option>
            <option>Slovak</option>
            <option>Russian</option>
          </select>
        </div>

        <button class="save-btn" @click="saveChanges">
          Save changes
        </button>

        <button class="delete-btn" @click="openDeleteModal">
          Delete account
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import api from '@/services/axios'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useNotificationStore } from '@/stores/notificationStore'

const router = useRouter()
const userStore = useUserStore()
const notifications = useNotificationStore()

const showDeleteModal = ref(false)

const form = ref({
  name: '',
  email: '',
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

onMounted(() => {
  if (userStore.user) {
    form.value.name = userStore.user.name
    form.value.email = userStore.user.email
  }
})

const saveChanges = async () => {
  try {
    await api.put('/freelancer/account', form.value)

    notifications.success('Profile updated successfully')

    userStore.user.name = form.value.name
    userStore.user.email = form.value.email

    form.value.current_password = ''
    form.value.new_password = ''
    form.value.new_password_confirmation = ''

  } catch (error) {
    if (error.response?.data?.message) {
      notifications.error(error.response.data.message)
    } else {
      notifications.error('Failed to update profile')
    }
  }
}

const openDeleteModal = () => {
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
}

const confirmDelete = async () => {
  try {
    await api.delete('/freelancer/profile')

    notifications.success('Account deleted successfully')

    userStore.logout()

    await router.replace({ name: 'home' })

  } catch {
    notifications.error('Failed to delete account')
  } finally {
    closeDeleteModal()
  }
}
</script>

<style scoped>
.delete-btn {
  background-color: red;
  color: white;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  padding: 12px 24px;
  margin-left: 250px;
}

.page-layout {
  display: flex;
  min-height: 100vh;
  background: #f7f6ff;
}

.content {
  flex: 1;
  padding: 40px;
}

h1 {
  font-size: 32px;
  margin-bottom: 24px;
}

.settings-card {
  background: #e9e3ff;
  border-radius: 30px;
  padding: 40px;
  max-width: 600px;
}

.setting-item {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

label {
  font-size: 14px;
  margin-bottom: 6px;
}

input,
select {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
}

.save-btn {
  margin-top: 20px;
  padding: 12px 24px;
  background: #5b4b8a;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-card {
  background: #fff;
  padding: 24px;
  border-radius: 16px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.modal-actions .secondary {
  background: #eee;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.modal-actions .danger {
  background: #ff4d4f;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}
</style>
