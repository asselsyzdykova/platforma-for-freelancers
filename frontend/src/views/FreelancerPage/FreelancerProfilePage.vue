<template>
  <div class="profile-layout">
    <SidebarMenu v-if="user" :userName="user.name" />

    <div class="profile-page">
      <div class="profile-card" v-if="user">
        <h1>PROFILE</h1>

        <div class="profile-content">
          <div class="left">
            <div class="avatar">
              <img
                v-if="profile.avatar_url"
                :src="profile.avatar_url"
                alt="Avatar"
                class="avatar-img"
              />
            </div>
            <h2>{{ user.name }}</h2>

            <p v-if="profile.about" class="about">
              {{ profile.about }}
            </p>

            <div
              class="skills"
              v-if="profile.skills && profile.skills.length"
            >
              <span v-for="skill in profile.skills" :key="skill">
                {{ skill }}
              </span>
            </div>
          </div>

          <div class="right">
            <button class="edit-btn" @click="editProfile">
              Edit Profile
            </button>

            <p><strong>Specialization:</strong> {{ user.role }}</p>
            <p><strong>Rating:</strong> ⭐ {{ profile.rating }} ({{ profile.reviews }})</p>
            <p><strong>Location:</strong> 📍 {{ profile.location }}</p>
            <p><strong>Completed projects:</strong> {{ profile.completed_projects }}</p>
            <p><strong>Proposals:</strong> {{ profile.proposals }}</p>
            <p>
              <strong>Member since:</strong>
              {{ new Date(profile.created_at).getFullYear() }}
            </p>

            <button class="message-btn">Send Message</button>
          </div>
        </div>

        <section class="certificates">
          <h2>CERTIFICATES</h2>

          <div class="cert-wrapper">
            <button class="arrow" @click="prevCert">◀</button>

            <div class="cert-window">
              <div
                class="cert-track"
                :style="{ transform: `translateX(-${currentCert * 240}px)` }"
              >
                <div
                  class="cert-card"
                  v-for="(cert, index) in certificates"
                  :key="index"
                >
                  {{ cert }}
                </div>
              </div>
            </div>

            <button class="arrow" @click="nextCert">▶</button>
          </div>
        </section>

        <section class="reviews">
          <h2>Reviews</h2>

          <div class="reviews-box">
            <div
              class="review-item"
              v-for="(review, index) in reviews"
              :key="index"
            >
              <div class="review-header">
                <div class="avatar small"></div>
                <p>{{ review.name }} ⭐ {{ review.rating }}</p>
              </div>

              <div class="review-content">
                <p><strong>Project name:</strong> {{ review.project }}</p>
                <p><strong>Review:</strong> {{ review.text }}</p>
                <p><strong>Execution time:</strong> {{ review.time }}</p>
              </div>
            </div>
          </div>
        </section>
      </div>

      <div v-else class="loading">
        Loading profile...
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'

const user = ref(null)
const profile = ref({})
const router = useRouter()

onMounted(async () => {
  try {
    const userRes = await api.get('/me', {
      headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
    })
    user.value = userRes.data

    const profileRes = await api.get('/freelancer/profile', {
      headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
    })
    profile.value = profileRes.data || {}
  } catch (error) {
    console.error(error)
    alert('Failed to load profile')
    router.push('/login')
  }
})

const certificates = [
  'Frontend Development Certificate',
  'Vue.js Advanced Course',
  'UI/UX Design Basics',
  'Backend Development Certificate',
  'Fullstack Web Development',
  'Mobile App Development'
]

const currentCert = ref(0)
const visibleCerts = 3
const nextCert = () => {
  if (currentCert.value < certificates.length - visibleCerts) currentCert.value++
}
const prevCert = () => {
  if (currentCert.value > 0) currentCert.value--
}

const reviews = [
  { name: 'Name Surname', rating: 5.0, project: 'Web platform for freelancers', text: 'Good job', time: '2 month' },
  { name: 'John Doe', rating: 4.5, project: 'Landing page', text: 'Very professional work', time: '3 weeks' },
  { name: 'Jane Smith', rating: 4.8, project: 'Mobile app design', text: 'Creative and timely delivery', time: '1 month' }
]

const editProfile = () => router.push('/edit-profile')
</script>

<style scoped>
  .profile-layout {
  display: flex;
  min-height: 100vh;
}

.profile-page {
  flex: 1;
  padding: 40px;
  background: #f7f6ff;
}

.avatar-img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
}

.edit-btn {
  margin-top: 16px;
  margin-bottom: 10px;
  padding: 10px 24px;
  background: white;
  color: #5b4b8a;
  border: 2px solid #5b4b8a;
  border-radius: 12px;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s;
}

.edit-btn:hover {
  background: #5b4b8a;
  color: white;
}

.profile-page {
  min-height: calc(100vh - 160px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
  background: #f7f6ff;
}

.profile-card {
  width: 100%;
  max-width: 900px;
  background: #e6e0ff;
  padding: 32px;
  border-radius: 24px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

h1 {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 24px;
  text-align: left;
}

.profile-content {
  display: flex;
  gap: 40px;
}

.left, .right {
  flex: 1;
}

.avatar {
  width: 100px;
  height: 100px;
  background: #ccc;
  border-radius: 50%;
  margin-bottom: 16px;
}

h2 {
  font-size: 22px;
  margin-bottom: 12px;
}

.about {
  background: white;
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 16px;
  font-size: 14px;
}

.skills span {
  background: white;
  padding: 6px 12px;
  border-radius: 12px;
  margin: 4px 4px 0 0;
  display: inline-block;
  font-size: 14px;
}

.right p {
  margin-bottom: 12px;
  font-size: 16px;
}

.message-btn {
  margin-top: 20px;
  padding: 12px 24px;
  background: #5b4b8a;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s;
}

.message-btn:hover {
  opacity: 0.9;
}

.loading {
  font-size: 16px;
  color: #777;
  text-align: center;
}

.cert-wrapper {
  display: flex;
  align-items: center;
  gap: 16px;
}

.cert-window {
  width: 720px;
  overflow: hidden;
}

.cert-track {
  display: flex;
  gap: 20px;
  transition: transform 0.3s ease;
}

.cert-card {
  min-width: 220px;
  height: 120px;
  background: white;
  border-radius: 20px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-size: 14px;
}

.arrow {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.reviews {
  margin-top: 60px;
}

.reviews-box {
  background: white;
  border-radius: 20px;
  padding: 20px;
  max-height: 220px;
  overflow-y: auto;
}

.review-item {
  background: #fff;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 16px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.review-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.avatar.small {
  width: 40px;
  height: 40px;
  background: #ccc;
  border-radius: 50%;
}

.review-content p {
  font-size: 14px;
  margin-bottom: 4px;
}
</style>
