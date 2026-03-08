<template>
  <div class="proposal">
    <p class="project-name">{{ proposal.name }}</p>

    <div class="progress">
      <div class="progress-fill" :style="{ width: proposal.progress + '%' }"></div>
    </div>

    <div class="steps">
      <span :class="{ active: proposal.step >= 0 }">Submitted</span>
      <span :class="{ active: proposal.step >= 1 }">Viewed</span>
      <span :class="{ active: proposal.step >= 2 }">Under review</span>

      <span class="final-status">
        <span :class="{ accepted: proposal.statusType === 'accepted' }">Accepted</span>
        /
        <span :class="{ rejected: proposal.statusType === 'rejected' }">Rejected</span>
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  proposal: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
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

@media (max-width: 600px) {
  .steps {
    flex-direction: column;
    gap: 4px;
  }
  .project-name {
    font-size: 16px;
  }
  .progress {
    height: 8px;
  }
}
</style>
