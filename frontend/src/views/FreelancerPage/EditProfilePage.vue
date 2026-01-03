<template>
  <div class="edit-profile-page">
    <h1>Edit Profile</h1>
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
})

onMounted(async () => {
  try {
    const response = await api.get('/freelancer/profile', {
      headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
    })
    if (response.data) {
      form.value = {
        ...response.data,
        skills: response.data.skills?.join(', ') || '',
      }
    }
  } catch (error) {
    console.error(error)
  }
})

const saveProfile = async () => {
  try {
    await api.post(
      '/freelancer/profile',
      {
        ...form.value,
        skills: form.value.skills.split(',').map((s) => s.trim()),
      },
      {
        headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
      },
    )
    router.push('/freelancer-profile')
  } catch (error) {
    console.error(error)
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
</style>
