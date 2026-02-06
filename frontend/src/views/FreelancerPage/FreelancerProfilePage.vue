<template>
  <div class="profile-layout">
    <SidebarMenu v-if="user" :userName="user.name" />

    <div class="profile-page">
      <div class="profile-card" v-if="user">
        <h1>PROFILE</h1>

        <div class="profile-content">
          <!-- LEFT -->
          <div class="left">
            <div class="avatar">
              <img
                v-if="profile.avatar_url"
                :src="profile.avatar_url"
                alt="Avatar"
                class="avatar-img"
              />
            </div>

            <div class="name-row">
              <h2>{{ user.name }}</h2>
              <span class="plan-badge">{{ planLabel }} Plan</span>
            </div>

            <p v-if="profile.about" class="about">
              {{ profile.about }}
            </p>

            <div class="skills" v-if="profile.skills?.length">
              <span v-for="skill in profile.skills" :key="skill">
                {{ skill }}
              </span>
            </div>
          </div>

          <!-- RIGHT -->
          <div class="right">
            <button class="edit-btn" @click="editProfile">Edit Profile</button>
            <button class="inbox-btn" @click="goToInbox">
              Inbox
              <span v-if="hasUnread" class="inbox-dot"></span>
            </button>

            <p><strong>Specialization:</strong> {{ user.role }}</p>
            <p><strong>Rating:</strong> ⭐ {{ profile.rating }} ({{ profile.reviews }})</p>
            <p><strong>Location:</strong> 📍 {{ profile.location }}</p>
            <p v-if="user.university"><strong>University:</strong> 🎓 {{ user.university }}</p>
            <p v-if="user.study_year"><strong>Study year:</strong> {{ user.study_year }}</p>
            <p><strong>Completed projects:</strong> {{ profile.completed_projects }}</p>
            <p><strong>Proposals:</strong> {{ profile.proposals }}</p>
            <p>
              <strong>Member since:</strong>
              {{ new Date(profile.created_at).getFullYear() }}
            </p>
          </div>
        </div>

        <!-- CERTIFICATES -->
        <section class="certificates" v-if="certificateUrls.length">
          <h2>CERTIFICATES</h2>

          <div class="cert-wrapper">
            <button v-if="showCertArrows" class="arrow" @click="prevCert">◀</button>

            <div class="cert-window">
              <div class="cert-track" :style="{ transform: `translateX(-${currentCert * 240}px)` }">
                <div class="cert-card" v-for="(cert, index) in certificateUrls" :key="cert">
                  <button
                    v-if="isImage(cert)"
                    class="cert-image-button"
                    @click="openCertificate(cert)"
                  >
                    <img :src="cert" :alt="certificateLabel(cert, index)" />
                  </button>
                  <a v-else :href="cert" target="_blank" rel="noopener">{{
                    certificateLabel(cert, index)
                  }}</a>
                </div>
              </div>
            </div>

            <button v-if="showCertArrows" class="arrow" @click="nextCert">▶</button>
          </div>
        </section>

        <div v-if="selectedCertificate" class="cert-modal" @click.self="closeCertificate">
          <button class="cert-modal-close" @click="closeCertificate">×</button>
          <img :src="selectedCertificate" alt="Certificate preview" />
        </div>

        <!-- REVIEWS -->
        <section class="reviews">
          <h2>Reviews</h2>

          <div class="reviews-box">
            <div class="review-item" v-for="(review, index) in reviews" :key="index">
              <div class="review-header">
                <div class="avatar small"></div>
                <p>{{ review.name }} ⭐ {{ review.rating }}</p>
              </div>

              <div class="review-content">
                <p><strong>Project:</strong> {{ review.project }}</p>
                <p><strong>Review:</strong> {{ review.text }}</p>
                <p><strong>Time:</strong> {{ review.time }}</p>
              </div>
            </div>
          </div>
        </section>
      </div>

      <div v-else class="loading">Loading profile...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, onActivated, onBeforeUnmount } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import { useNotificationStore } from '@/stores/notificationStore'

const user = ref(null)
const profile = ref({})
const router = useRouter()
const toast = useNotificationStore()
const notifications = ref([])
const hasUnread = ref(false)
const goToInbox = () => router.push('/freelancer/inbox')

const planLabel = computed(() => {
  const plan = user.value?.plan
  if (!plan) return 'Free'
  const label = String(plan).toLowerCase()
  return label.charAt(0).toUpperCase() + label.slice(1)
})

const loadProfile = async () => {
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
    toast.error('Failed to load profile')
    router.push('/login')
    return
  }

  try {
    const notifRes = await api.get('/freelancer/notifications', {
      headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
    })
    notifications.value = notifRes.data
    hasUnread.value = notifications.value.some((n) => !n.is_read)
  } catch (error) {
    console.error(error)
  }
}

const handlePageShow = (event) => {
  if (event?.persisted) {
    window.location.reload()
    return
  }
  loadProfile()
}

const handleVisibilityChange = () => {
  if (document.visibilityState === 'visible') {
    loadProfile()
  }
}

onMounted(() => {
  loadProfile()
  window.addEventListener('pageshow', handlePageShow)
  document.addEventListener('visibilitychange', handleVisibilityChange)
})

onActivated(() => {
  loadProfile()
})

onBeforeUnmount(() => {
  window.removeEventListener('pageshow', handlePageShow)
  document.removeEventListener('visibilitychange', handleVisibilityChange)
})

const certificateUrls = computed(() => profile.value?.certificate_urls || [])

const certificateLabel = (url, index) => {
  const name = String(url).split('/').pop()
  return name ? name : `Certificate ${index + 1}`
}

const isImage = (url) => {
  return /\.(png|jpe?g|gif|webp)$/i.test(String(url))
}

const selectedCertificate = ref(null)

const openCertificate = (url) => {
  selectedCertificate.value = url
}

const closeCertificate = () => {
  selectedCertificate.value = null
}

const currentCert = ref(0)
const visibleCerts = 3
const showCertArrows = computed(() => certificateUrls.value.length > visibleCerts)
const nextCert = () => {
  if (currentCert.value < certificateUrls.value.length - visibleCerts) currentCert.value++
}
const prevCert = () => {
  if (currentCert.value > 0) currentCert.value--
}

const reviews = [
  {
    name: 'Name Surname',
    rating: 5.0,
    project: 'Web platform for freelancers',
    text: 'Good job',
    time: '2 month',
  },
  {
    name: 'John Doe',
    rating: 4.5,
    project: 'Landing page',
    text: 'Very professional work',
    time: '3 weeks',
  },
  {
    name: 'Jane Smith',
    rating: 4.8,
    project: 'Mobile app design',
    text: 'Creative and timely delivery',
    time: '1 month',
  },
]

const editProfile = () => router.push('/edit-profile')
</script>

<style scoped>
.inbox-btn {
  padding: 10px 24px;
  background: white;
  color: #5b4b8a;
  border: 2px solid #5b4b8a;
  border-radius: 12px;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s;
  margin-left: 12px;
}

.inbox-btn:hover {
  background: #5b4b8a;
  color: white;
}

.inbox-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  background: #ef4444;
  border-radius: 50%;
  margin-left: 8px;
  vertical-align: middle;
  pointer-events: none;
}
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

.left,
.right {
  flex: 1;
}

.avatar {
  width: 100px;
  height: 100px;
  background: #ccc;
  border-radius: 50%;
  margin-bottom: 16px;
}

.name-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

h2 {
  font-size: 22px;
  margin-bottom: 0;
}

.plan-badge {
  background: #5b4b8a;
  color: #fff;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 999px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
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

.loading {
  font-size: 16px;
  color: #777;
  text-align: center;
}

.cert-wrapper {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 12px 0;
}

.cert-window {
  width: min(760px, 72vw);
  overflow: hidden;
}

.cert-track {
  display: flex;
  gap: 18px;
  transition: transform 0.35s ease;
}

.cert-card {
  min-width: 220px;
  height: 140px;
  background: linear-gradient(135deg, #ffffff, #f6f3ff);
  border-radius: 22px;
  box-shadow: 0 10px 24px rgba(91, 61, 245, 0.12);
  border: 1px solid rgba(91, 61, 245, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-size: 14px;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.cert-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 16px;
}

.cert-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 28px rgba(91, 61, 245, 0.18);
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

.arrow {
  background: #ffffff;
  border: 1px solid rgba(91, 61, 245, 0.2);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  font-size: 18px;
  cursor: pointer;
  box-shadow: 0 8px 16px rgba(91, 61, 245, 0.15);
  transition:
    transform 0.15s ease,
    box-shadow 0.15s ease;
}

.arrow:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 20px rgba(91, 61, 245, 0.2);
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
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
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
