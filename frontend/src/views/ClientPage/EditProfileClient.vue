<template>
  <div class="edit-profile-page">
    <div class="card">
      <h1>Edit Client Profile</h1>

      <form @submit.prevent="saveProfile">
        <div class="avatar-block">
          <img
            v-if="previewAvatar || form.avatar_url"
            :src="previewAvatar || form.avatar_url"
            class="avatar"
          />
          <input type="file" @change="onAvatarChange" />
        </div>

        <div class="field">
          <label>Name</label>
          <input v-model="form.name" type="text" required />
        </div>

        <div class="field">
          <label>Company</label>
          <input v-model="form.company" type="text" />
        </div>

        <div class="field">
          <label>Location</label>
          <input v-model="form.location" type="text" />
        </div>

        <div class="field">
          <label>About</label>
          <textarea v-model="form.about" rows="4"></textarea>
        </div>

        <div class="actions">
          <button type="submit" class="primary">Save changes</button>
          <button type="button" class="secondary" @click="cancel">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notificationStore'

const router = useRouter()
const notifications = useNotificationStore()

const form = ref({
  name: '',
  company: '',
  location: '',
  about: '',
  avatar_url: null,
})
const previewAvatar = ref(null)
let avatarFile = null

onMounted(async () => {
  try {
    const res = await api.get('/client/profile')
    form.value = {
      ...form.value,
      ...res.data,
    }
    previewAvatar.value = res.data.avatar_url || null
  } catch (e) {
    console.error('Failed to load client profile', e)
  }
})

const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  avatarFile = file
  previewAvatar.value = URL.createObjectURL(file)
}

const saveProfile = async () => {
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('company', form.value.company || '')
    formData.append('location', form.value.location || '')
    formData.append('about', form.value.about || '')

    if (avatarFile) formData.append('avatar', avatarFile)

    const res = await api.post('/client/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (res.data.avatar_url) previewAvatar.value = res.data.avatar_url

    notifications.success('Profile saved successfully!')
    router.push('/client-profile')
  } catch (e) {
    console.error('Failed to save client profile', e.response?.data || e)
    notifications.error('Failed to save profile. Check all fields.')
  }
}

const cancel = () => {
  router.push('/client-profile')
}
</script>

<style scoped>
.edit-profile-page {
  display: flex;
  justify-content: center;
  padding: 40px 20px;
}

.card {
  width: 100%;
  max-width: 520px;
  background: #fff;
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

h1 {
  margin-bottom: 25px;
  text-align: center;
}

.avatar-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 25px;
}

.avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
}

.field {
  margin-bottom: 16px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
}

input,
textarea {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.actions {
  display: flex;
  gap: 12px;
  margin-top: 25px;
}

.primary {
  flex: 1;
  background: #5b4b8a;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}

.secondary {
  flex: 1;
  background: #eee;
  border: none;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
}
</style>
