<template>
  <div class="public-profile-layout">
    <div class="public-card" v-if="freelancer">
      <div class="public-left">
        <img
          v-if="freelancer.avatar_url"
          :src="freelancer.avatar_url"
          alt="Avatar"
          class="avatar-img"
        />
        <h2>{{ freelancer.name }}</h2>
        <p class="role">{{ freelancer.role }}</p>
        <p class="rating">⭐ {{ freelancer.rating }} ({{ freelancer.reviews ?? 0 }})</p>
        <p class="location">📍 {{ freelancer.location }}</p>
        <div class="skills" v-if="freelancer.skills?.length">
          <span v-for="skill in freelancer.skills" :key="skill">{{ skill }}</span>
        </div>
      </div>

      <div class="public-right">
        <h1>About</h1>
        <p v-if="freelancer.about">{{ freelancer.about }}</p>
        <div class="actions">
          <button @click="messageFreelancer">Message</button>
          <button @click="viewAllFreelancers">Back to list</button>
        </div>
      </div>
    </div>

    <div v-else class="loading">Loading freelancer...</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/axios'

const route = useRoute()
const router = useRouter()
const freelancer = ref(null)

const fetchFreelancer = async (id) => {
  try {
    // fetch list and find by id (backend provides /freelancers)
    const res = await api.get('/freelancers')
    const found = (res.data || []).find((f) => String(f.id) === String(id))
    if (found) {
      freelancer.value = found
    } else {
      // If not found, navigate back
      router.push('/freelancers')
    }
  } catch (e) {
    console.error(e)
    router.push('/freelancers')
  }
}

onMounted(() => {
  const id = route.params.id
  if (id) fetchFreelancer(id)
})

const messageFreelancer = () => {
  router.push(`/chat/${freelancer.value.id}`)
}

const viewAllFreelancers = () => router.push('/freelancers')
</script>

<style scoped>
.public-profile-layout {
  max-width: 1000px;
  margin: 40px auto;
}
.public-card {
  display: flex;
  gap: 32px;
  background: #fff;
  padding: 28px;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}
.public-left {
  width: 320px;
  text-align: center;
}
.avatar-img {
  width: 160px;
  height: 160px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 12px;
}
.skills span {
  display: inline-block;
  background: #f3efff;
  padding: 6px 10px;
  border-radius: 10px;
  margin: 4px;
}
.public-right {
  flex: 1;
}
.actions {
  margin-top: 20px;
  display: flex;
  gap: 12px;
}
.actions button {
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  background: #5b3df5;
  color: #fff;
}
</style>
