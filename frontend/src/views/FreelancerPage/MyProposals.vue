<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>My Proposals</h1>

      <div class="proposals-card">
        <div
          class="proposal"
          v-for="(proposal, index) in proposals"
          :key="index"
        >
          <p class="project-name">{{ proposal.name }}</p>

          <div class="progress">
            <div
              class="progress-fill"
              :style="{ width: proposal.progress + '%' }"
            ></div>
          </div>

          <div class="steps">
            <span>Submitted</span>
            <span>Viewed</span>
            <span>Under review</span>
            <span
              :class="{
                accepted: proposal.status === 'accepted',
                rejected: proposal.status === 'rejected'
              }"
            >
              Accepted / Rejected
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'

const proposals = [
  {
    name: 'Project name',
    progress: 20,
    status: 'submitted'
  },
  {
    name: 'Project name',
    progress: 45,
    status: 'viewed'
  },
  {
    name: 'Project name',
    progress: 70,
    status: 'review'
  },
  {
    name: 'Project name',
    progress: 100,
    status: 'accepted'
  }
]
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
  color: #555;
}

.accepted {
  color: #4a3aff;
  font-weight: 600;
}

.rejected {
  color: red;
  font-weight: 600;
}
</style>
