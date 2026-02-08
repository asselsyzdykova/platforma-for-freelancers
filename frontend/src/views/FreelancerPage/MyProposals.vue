<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>My Proposals</h1>

      <div class="proposals-card">
        <div class="proposal" v-for="proposal in proposals" :key="proposal.id">
          <p class="project-name">{{ proposal.display.name }}</p>

          <div class="progress">
            <div class="progress-fill" :style="{ width: proposal.display.progress + '%' }"></div>
          </div>

          <div class="steps">
            <span
              :class="{ active: ['pending', 'viewed', 'under review', 'accepted', 'rejected'].includes(proposal.display.status) }"
            >
              Submitted
            </span>
            <span
              :class="{ active: ['viewed', 'under review', 'accepted', 'rejected'].includes(proposal.display.status) }"
            >
              Viewed
            </span>
            <span
              :class="{ active: ['under review', 'accepted', 'rejected'].includes(proposal.display.status) }"
            >
              Under review
            </span>
            <span class="final-status">
              <span :class="{ accepted: proposal.display.statusType === 'accepted' }">
                Accepted
              </span>
              /
              <span :class="{ rejected: proposal.display.statusType === 'rejected' }">
                Rejected
              </span>
            </span>
          </div>
        </div>
      </div>

      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
        <button
          v-for="page in totalPages"
          :key="page"
          :class="{ active: page === currentPage }"
          @click="currentPage = page"
        >
          {{ page }}
        </button>
        <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import api from '@/services/axios'

const proposals = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const pageSize = 8

const statusMap = {
  Pending: { progress: 0, step: 0 },
  Viewed: { progress: 33, step: 1 },
  'Under Review': { progress: 66, step: 2 },
  Accepted: { progress: 100, step: 3, type: 'accepted' },
  Rejected: { progress: 100, step: 3, type: 'rejected' },
}

const getProposalDisplay = (proposal) => {
  const statusInfo = statusMap[proposal.status] || statusMap['Pending']
  return {
    name: proposal.project_name,
    progress: statusInfo.progress,
    status: proposal.status.toLowerCase(),
    statusType: statusInfo.type || null,
  }
}

const loadProposals = async () => {
  try {
    const res = await api.get('/freelancer/proposals', {
      params: { page: currentPage.value, per_page: pageSize },
    })
    const data = res.data?.data || []
    proposals.value = data.map((p) => ({
      ...p,
      display: getProposalDisplay(p),
    }))
    totalPages.value = res.data?.meta?.last_page || 1
  } catch (e) {
    console.error('Failed to load proposals', e)
    proposals.value = []
    totalPages.value = 1
  }
}

onMounted(loadProposals)

watch(currentPage, () => {
  loadProposals()
})
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

h1 {
  font-size: 32px;
  margin-bottom: 24px;
}

.proposals-card {
  background: #e9e3ff;
  border-radius: 30px;
  padding: 40px;
  max-height: 600px;
  overflow-y: auto;
}

.proposal {
  background: white;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 24px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}

.project-name {
  font-weight: 500;
  margin-bottom: 12px;
}

.progress {
  height: 10px;
  background: #ddd;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 10px;
}

.progress-fill {
  height: 100%;
  background: #2cff00;
  transition: 0.3s;
}

.steps {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: #999;
}

.steps span.active {
  color: #555;
  font-weight: 500;
}

.final-status span {
  color: #999;
  font-weight: 500;
}

.final-status span.accepted {
  color: #4a3aff;
  font-weight: 600;
}

.final-status span.rejected {
  color: red;
  font-weight: 600;
}

.pagination {
  margin-top: 24px;
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
