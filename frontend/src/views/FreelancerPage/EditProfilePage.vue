<template>
  <div class="edit-profile-page">
    <h1>Edit Profile</h1>

    <div class="form-group avatar-group">
      <label>Avatar</label>
      <div class="avatar-preview" v-if="form.avatarPreview">
        <img :src="form.avatarPreview" alt="Avatar Preview" />
      </div>
      <input type="file" @change="onAvatarChange" accept="image/*" />
    </div>

    <form @submit.prevent="saveProfile">
      <div class="form-group">
        <label>About Me</label>
        <textarea v-model="form.about"></textarea>
      </div>

      <div class="form-group">
        <label>Location</label>
        <input v-model="form.location" type="text" />
      </div>

      <div class="form-group">
        <label>Skills (comma separated)</label>
        <input v-model="form.skills" type="text" />
      </div>

      <div class="form-group">
        <label>Completed Projects</label>
        <input v-model.number="form.completed_projects" type="number" />
      </div>

      <div class="form-group">
        <label>Proposals</label>
        <input v-model.number="form.proposals" type="number" />
      </div>

      <button type="submit">Save</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = ref({
  about: '',
  location: '',
  skills: '',
  completed_projects: 0,
  proposals: 0,
  avatar: null,
  avatarPreview: null,
})

onMounted(async () => {
  try {
    const response = await api.get('/freelancer/profile', {
      headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
    })
    if (response.data) {
      form.value = {
        ...form.value,
        ...response.data,
        skills: response.data.skills?.join(', ') || '',
        avatarPreview: response.data.avatar_url || null,
      }
    }
  } catch (error) {
    console.error(error)
    alert('Failed to load profile')
  }
})

const onAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.avatar = file
    form.value.avatarPreview = URL.createObjectURL(file)
  }
}

const saveProfile = async () => {
  try {
    const formData = new FormData()
    formData.append('about', form.value.about || '')
    formData.append('location', form.value.location || '')
    formData.append('completed_projects', form.value.completed_projects || 0)
    formData.append('proposals', form.value.proposals || 0)

    form.value.skills
      .split(',')
      .map((s) => s.trim())
      .filter((s) => s.length > 0)
      .forEach((skill) => formData.append('skills[]', skill))

    if (form.value.avatar) {
      formData.append('avatar', form.value.avatar)
    }

    const res = await api.post('/freelancer/profile', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
        'Content-Type': 'multipart/form-data',
      },
    })

    if (res.data.avatar_url) {
      form.value.avatarPreview = res.data.avatar_url
    }

    alert('Profile saved successfully!')
    router.push('/freelancer-profile')
  } catch (error) {
    console.error(error)
    alert('Failed to save profile. Check all fields.')
  }
}
</script>

<style scoped>
.edit-profile-page {
  max-width: 600px;
  margin: 40px auto;
  background: #f3efff;
  padding: 32px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.form-group {
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
}

input,
textarea {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
}

button {
  padding: 12px 20px;
  background: #5b3df5;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
}

.avatar-group img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
}
</style>
