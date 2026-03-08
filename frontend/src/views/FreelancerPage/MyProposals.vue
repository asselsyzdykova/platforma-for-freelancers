<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>My Proposals</h1>

      <div class="proposals-card">
        <div v-if="isLoading" class="state-message">Loading proposals...</div>

        <div v-else-if="!proposals.length" class="state-message">You haven't submitted any proposals yet.</div>

        <template v-else>
          <ProposalsCard v-for="proposal in proposals" :key="proposal.id" :proposal="proposal.display" />
        </template>
      </div>

      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
        <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
        <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import ProposalsCard from '@/components/FreelancerPageMenu/ProposalsCard.vue'
import api from '@/services/axios'

const proposals = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const pageSize = 8
const isLoading = ref(true)

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
    id: proposal.id,
    name: proposal.project_name,
    progress: statusInfo.progress,
    step: statusInfo.step,
    status: proposal.status.toLowerCase(),
    statusType: statusInfo.type || null,
  }
}

const loadProposals = async () => {
  try {
    isLoading.value = true
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
  } finally {
    isLoading.value = false
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

.state-message {
  text-align: center;
  color: #666;
  font-size: 16px;
  padding: 40px 0;
}

.pagination {
  margin-top: 32px;
  display: flex;
  gap: 16px;
  justify-content: center;
  align-items: center;
}

.pagination .page-info {
  font-weight: 500;
}

.pagination button {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .content {
    padding: 20px;
  }

  .proposals-card {
    padding: 20px;
  }
}

@media (max-width: 600px) {
  .page-layout {
    flex-direction: column;
  }

  .pagination {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
