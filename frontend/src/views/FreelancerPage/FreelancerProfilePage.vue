<template>
  <div class="profile-page">
    <div class="profile-card" v-if="user">
      <h1>Freelancer Profile</h1>

      <div class="profile-info">
        <p><strong>Name:</strong> {{ user.name }}</p>
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p><strong>Role:</strong> {{ user.role }}</p>

        <p v-if="profile.about"><strong>About Me:</strong> {{ profile.about }}</p>
        <p v-if="profile.location"><strong>Location:</strong> {{ profile.location }}</p>
        <p v-if="profile.skills && profile.skills.length">
          <strong>Skills:</strong> {{ profile.skills.join(', ') }}
        </p>
        <p v-if="profile.completed_projects !== undefined">
          <strong>Completed Projects:</strong> {{ profile.completed_projects }}
        </p>
        <p v-if="profile.proposals !== undefined">
          <strong>Proposals:</strong> {{ profile.proposals }}
        </p>
      </div>

      <div class="profile-actions">
        <button @click="editProfile">Edit Profile</button>
      </div>
    </div>

    <div v-else class="loading">
      <p>Loading profile...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'

const user = ref(null)
const profile = ref({})
const router = useRouter()

onMounted(async () => {
  try {
    const userRes = await api.get('/me', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
      },
    })
    user.value = userRes.data

    const profileRes = await api.get('/freelancer/profile', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
      },
    })
    profile.value = profileRes.data || {}
  } catch (error) {
    console.error(error)
    alert('Failed to load profile')
    router.push('/login')
  }
})

const editProfile = () => {
  router.push('/edit-profile')
}

</script>

<style scoped>
.profile-page {
  min-height: calc(100vh - 160px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
}

.profile-card {
  width: 100%;
  max-width: 500px;
  background: #f3efff;
  padding: 32px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  text-align: center;
}

h1 {
  margin-bottom: 16px;
  font-size: 28px;
}

.profile-info p {
  margin-bottom: 12px;
  font-size: 16px;
}

.profile-actions {
  margin-top: 24px;
  display: flex;
  justify-content: space-between;
}

button {
  padding: 10px 20px;
  background: #5b3df5;
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  cursor: pointer;
  transition: 0.2s;
}

button:hover {
  opacity: 0.9;
}

.loading {
  font-size: 16px;
  color: #777;
}
</style>
