<template>
  <div class="page-layout">
    <ClientSidebar />

    <div class="content">
      <h1>My Projects & Payments</h1>

      <div v-if="loading" class="loading">Loading projects...</div>

      <div v-else class="projects-list">
        <div v-for="project in projects" :key="project.id" class="project-card">
          <div class="project-header">
            <div>
              <h3>{{ project.title }}</h3>
              <p class="freelancer">Freelancer: <strong>{{ project.freelancer?.name }}</strong></p>
            </div>
            <span class="status-badge" :class="project.status">{{ project.status }}</span>
          </div>

          <p class="description">{{ project.description }}</p>

          <div class="milestones-section">
            <h4>Payment Milestones</h4>
            <div v-for="m in project.milestones" :key="m.id" class="milestone-item">
              <div class="m-info">
                <span class="m-title">{{ m.title }}</span>
                <span class="m-price">{{ m.price }} €</span>
              </div>

              <div class="m-status">
                <button v-if="m.status === 'pending'" @click="payMilestone(m.id)" class="pay-btn"
                  :disabled="payingId === m.id">
                  {{ payingId === m.id ? 'Processing...' : 'Pay Now' }}
                </button>

                <span v-else-if="m.status === 'paid'" class="status-paid">Paid ✅</span>
                <span v-else-if="m.status === 'completed'" class="status-completed">Completed</span>
                <span v-else class="status-other">{{ m.status }}</span>
              </div>
            </div>
          </div>
        </div>
        <div v-if="projects.length === 0" class="empty-state">
          No projects found.
        </div>
      </div>
      <AppPagination v-model="currentPage" :totalPages="totalPages" mode="simple" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/services/axios'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'
import { useNotificationStore } from '@/stores/notificationStore'
import AppPagination from '@/components/UI/AppPagination.vue'

const notifications = useNotificationStore()
const projects = ref([])
const loading = ref(true)
const payingId = ref(null)
const currentPage = ref(1)
const totalPages = ref(1)

const loadProjects = async () => {
  loading.value = true
  try {
    const res = await api.get('/client/projects', {
      params: { page: currentPage.value }
    })
    projects.value = res.data.data
    totalPages.value = res.data.meta?.last_page || 1
  } catch {
    notifications.error('Failed to load projects')
  } finally {
    loading.value = false
  }
}

const payMilestone = async (milestoneId) => {
  payingId.value = milestoneId
  try {
    const { data } = await api.post(`/milestones/${milestoneId}/pay`)

    if (data.url) {
      window.location.href = data.url
    }
  } catch (err) {
    console.error(err)
    notifications.error(err.response?.data?.error || 'Payment initialization failed')
  } finally {
    payingId.value = null
  }
}

watch(currentPage, () => {
  loadProjects()
  window.scrollTo({ top: 0, behavior: 'smooth' })
})
onMounted(loadProjects)
</script>

<style scoped>
.page-layout {
  display: flex;
  min-height: 100vh;
  background: #f7f6ff;
}

.content {
  flex: 1;
  padding: 40px;
}

.project-card {
  background: white;
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.project-header {
  display: flex;
  justify-content: space-between;
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
  margin-bottom: 15px;
}

.freelancer {
  font-size: 14px;
  color: #666;
}

.milestones-section {
  background: #fcfaff;
  border-radius: 12px;
  padding: 16px;
  margin-top: 15px;
}

.milestone-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #eee;
}

.milestone-item:last-child {
  border-bottom: none;
}

.m-title {
  font-weight: 500;
  display: block;
}

.m-price {
  color: #5b3df5;
  font-weight: bold;
}

.pay-btn {
  background: #5b3df5;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s;
}

.pay-btn:hover {
  background: #4a2ed4;
}

.status-paid {
  color: #27ae60;
  font-weight: bold;
  font-size: 14px;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  height: fit-content;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}

.status-badge.active {
  background: #d4edda;
  color: #155724;
}
</style>
