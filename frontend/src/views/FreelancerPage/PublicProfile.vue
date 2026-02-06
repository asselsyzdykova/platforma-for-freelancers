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
        <div v-if="freelancer.certificate_urls?.length" class="certificates">
          <h3>Certificates</h3>
          <div class="cert-grid">
            <div class="cert-card" v-for="(cert, index) in freelancer.certificate_urls" :key="cert">
              <button v-if="isImage(cert)" class="cert-image-button" @click="openCertificate(cert)">
                <img :src="cert" :alt="certificateLabel(cert, index)" />
              </button>
              <a v-else :href="cert" target="_blank" rel="noopener">{{
                certificateLabel(cert, index)
              }}</a>
            </div>
          </div>
        </div>
        <div class="actions">
          <button @click="messageFreelancer">Message</button>
          <button @click="viewAllFreelancers">Back to list</button>
        </div>
      </div>
    </div>

    <div v-if="selectedCertificate" class="cert-modal" @click.self="closeCertificate">
      <button class="cert-modal-close" @click="closeCertificate">×</button>
      <img :src="selectedCertificate" alt="Certificate preview" />
    </div>

    <div v-else-if="isLoading" class="loading">Loading freelancer...</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/axios'

const route = useRoute()
const router = useRouter()
const freelancer = ref(null)
const isLoading = ref(true)
const selectedCertificate = ref(null)

const fetchFreelancer = async (id) => {
  try {
    const res = await api.get('/freelancers')
    const found = (res.data || []).find((f) => String(f.id) === String(id))
    if (found) {
      freelancer.value = found
    } else {
      router.push('/freelancers')
    }
  } catch (e) {
    console.error(e)
    router.push('/freelancers')
  } finally {
    isLoading.value = false
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

const isImage = (url) => {
  return /\.(png|jpe?g|gif|webp)$/i.test(String(url))
}

const certificateLabel = (url, index) => {
  const name = String(url).split('/').pop()
  return name ? name : `Certificate ${index + 1}`
}

const openCertificate = (url) => {
  selectedCertificate.value = url
}

const closeCertificate = () => {
  selectedCertificate.value = null
}
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
.certificates {
  margin-top: 16px;
}
.cert-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-top: 12px;
}
.cert-card {
  height: 120px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 8px;
}
.cert-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}
.cert-image-button {
  background: none;
  border: none;
  padding: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.cert-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.cert-modal img {
  max-width: 90vw;
  max-height: 90vh;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}
.cert-modal-close {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.9);
  color: #2b2b2b;
  font-size: 24px;
  cursor: pointer;
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
