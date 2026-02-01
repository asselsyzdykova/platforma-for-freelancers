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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import api from '@/services/axios'

const proposals = ref([])

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
    const res = await api.get('/freelancer/proposals')
    proposals.value = res.data.map((p) => ({
      ...p,
      display: getProposalDisplay(p),
    }))
  } catch (e) {
    console.error('Failed to load proposals', e)
  }
}

onMounted(loadProposals)
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
</style>
