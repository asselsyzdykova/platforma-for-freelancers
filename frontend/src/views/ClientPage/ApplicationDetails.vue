<template>
  <div class="application-page">
    <div class="page-header">
      <div>
        <h1>Project Application</h1>
        <p class="subtitle">Review freelancer proposal and take action.</p>
      </div>
      <button class="secondary" @click="$router.back()">Back</button>
    </div>

    <div v-if="application" class="card">
      <div class="card-head">
        <div>
          <p class="label">Freelancer</p>
          <h2>{{ application.freelancer.name }}</h2>
        </div>
        <span class="status" :class="statusClass">{{ application.status }}</span>
      </div>

      <div class="info-grid">
        <div>
          <p class="label">Project</p>
          <p class="value">{{ application.project.title }}</p>
        </div>
        <div>
          <p class="label">Proposal</p>
          <p class="value">{{ application.proposal_text }}</p>
        </div>
      </div>

      <div class="actions">
        <button class="ghost" @click="goToFreelancerProfile">View Freelancer</button>
        <button class="outline" @click="setUnderReview">Mark as Under Review</button>
        <button class="primary" @click="acceptApplication">Accept</button>
        <button class="danger" @click="rejectApplication">Reject</button>
      </div>

      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">Prev</button>
        <button
          v-for="page in totalPages"
          :key="page"
          :class="{ active: page === currentPage }"
          @click="goToPage(page)"
        >
          {{ page }}
        </button>
        <button :disabled="currentPage === totalPages" @click="goToPage(currentPage + 1)">
          Next
        </button>
      </div>
    </div>

    <div v-else class="loading">Loading application...</div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'

const route = useRoute()
const router = useRouter()
const application = ref(null)
const notifications = useNotificationStore()
const currentPage = ref(1)
const totalPages = ref(1)
const projectId = ref(null)
const pageSize = 1

const statusClass = computed(() => {
  const status = String(application.value?.status || '').toLowerCase()
  if (status.includes('accepted')) return 'accepted'
  if (status.includes('rejected')) return 'rejected'
  if (status.includes('review')) return 'review'
  if (status.includes('viewed')) return 'viewed'
  return 'pending'
})

const loadApplication = async (id = route.params.id) => {
  try {
    const res = await api.get(`/client/applications/${id}`)
    application.value = res.data
    projectId.value = res.data?.project?.id || null
    totalPages.value = res.data?.project_proposals_total || 1
    currentPage.value = res.data?.project_proposal_position || 1
  } catch (e) {
    console.error('Failed to load application', e)
  }
}

const goToPage = async (page) => {
  if (!projectId.value) return
  if (page < 1 || page > totalPages.value) return

  try {
    const res = await api.get(`/client/projects/${projectId.value}/proposals`, {
      params: { page, per_page: pageSize },
    })
    const nextItem = res.data?.data?.[0]
    if (!nextItem?.id) return
    await loadApplication(nextItem.id)
    router.replace({ name: 'ApplicationDetails', params: { id: nextItem.id } })
  } catch (e) {
    console.error('Failed to paginate applications', e)
  }
}

const goToFreelancerProfile = async () => {
  if (application.value && application.value.freelancer.id) {
    try {
      await api.post(`/client/applications/${route.params.id}/view`)
      application.value.status = 'Viewed'
    } catch (e) {
      console.error('Failed to mark as viewed', e)
    }
    router.push(`/public-profile/${application.value.freelancer.id}`)
  }
}

const setUnderReview = async () => {
  try {
    await api.post(`/client/applications/${route.params.id}/review`)
    notifications.success('Application marked as under review')
    application.value.status = 'Under Review'
  } catch (e) {
    console.error(e)
    notifications.error('Failed to mark as under review')
  }
}

const acceptApplication = async () => {
  try {
    await api.post(`/client/applications/${route.params.id}/accept`)
    notifications.success('Application accepted')
    application.value.status = 'Accepted'
  } catch (e) {
    console.error(e)
    notifications.error('Failed to accept application')
  }
}

const rejectApplication = async () => {
  try {
    await api.post(`/client/applications/${route.params.id}/reject`)
    notifications.success('Application rejected')
    application.value.status = 'Rejected'
  } catch (e) {
    console.error(e)
    notifications.error('Failed to reject application')
  }
}

onMounted(loadApplication)
</script>

<style scoped>
.application-page {
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 20px 80px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
}

.subtitle {
  color: #666;
  margin-top: 6px;
}

.card {
  background: #f7f6ff;
  border-radius: 20px;
  padding: 28px;
  box-shadow: 0 12px 30px rgba(91, 75, 138, 0.12);
}

.card-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.label {
  font-size: 12px;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.value {
  color: #3f3f46;
}

.info-grid {
  display: grid;
  gap: 16px;
  margin-bottom: 24px;
}

.status {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.status.accepted {
  background: #dcfce7;
  color: #166534;
}

.status.rejected {
  background: #fee2e2;
  color: #991b1b;
}

.status.review {
  background: #fef3c7;
  color: #92400e;
}

.status.viewed {
  background: #e0f2fe;
  color: #075985;
}

.status.pending {
  background: #e5e7eb;
  color: #374151;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 20px;
}

button {
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-weight: 600;
}

.primary {
  background: #5b4b8a;
  color: #fff;
}

.danger {
  background: #ef4444;
  color: #fff;
}

.secondary,
.ghost,
.outline {
  background: #fff;
  border: 1px solid #d6d3e5;
  color: #3f3f46;
}

.outline {
  border-color: #b7a7ea;
  color: #5b4b8a;
}

.loading {
  color: #777;
  text-align: center;
  margin-top: 60px;
}

.pagination {
  margin-top: 20px;
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.pagination button {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}

.pagination button.active {
  background: #5b3df5;
  color: #fff;
  border-color: #5b3df5;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
