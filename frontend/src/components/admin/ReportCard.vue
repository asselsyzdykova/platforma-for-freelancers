<template>
  <div class="report-card">
    <div class="report-header">
      <div>
        <h4 class="reason">{{ report.reason }}</h4>
        <p class="users">
          Reporter:
          <strong>{{ report.reporter?.name || 'Unknown' }}</strong>
          →
          Reported:
          <strong>{{ report.reported_user?.name || 'Unknown' }}</strong>
        </p>
      </div>

      <span :class="['status', report.status]">
        {{ report.status }}
      </span>
    </div>

    <p class="meta">
      {{ formatDate(report.created_at) }}
      <span v-if="report.ticket_id">
        | Ticket #{{ report.ticket_id }}
      </span>
    </p>

    <p class="description" @click="openModal">
      {{ report.description || 'No description provided.' }}
    </p>

    <div v-if="report.status !== 'resolved'" class="actions">
      <button
        v-if="report.status === 'pending'"
        @click="markInProgress"
      >
        Mark In Progress
      </button>

      <button class="resolve-btn" @click="resolveReport">
        Resolve
      </button>
    </div>
  </div>

  <div v-if="showModal" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <h3>{{ report.reason }}</h3>

      <p>
        <strong>Reporter:</strong>
        {{ report.reporter?.name }}
      </p>

      <p>
        <strong>Reported User:</strong>
        {{ report.reported_user?.name }}
      </p>

      <p class="modal-description">
        {{ report.description }}
      </p>

      <button class="btn-close" @click="closeModal">
        Close
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  report: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['resolve', 'in-progress'])

const showModal = ref(false)

const openModal = () => {
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const markInProgress = () => {
  emit('in-progress', props.report.id)
}

const resolveReport = () => {
  emit('resolve', props.report.id)
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}
</script>

<style scoped>
.report-card {
  background: #ffffff;
  padding: 20px;
  border-radius: 14px;
  margin-bottom: 20px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
  transition: transform 0.2s ease;
}

.report-card:hover {
  transform: translateY(-3px);
}

.report-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 8px;
}

.reason {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}

.users {
  font-size: 13px;
  color: #666;
  margin-top: 4px;
}

.status {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
  color: white;
}

.status.pending {
  background: #f59e0b;
}

.status.in_progress {
  background: #3b82f6;
}

.status.resolved {
  background: #22c55e;
}

.meta {
  font-size: 12px;
  color: #777;
  margin-bottom: 10px;
}

.description {
  font-size: 14px;
  color: #333;
  margin-bottom: 14px;
  cursor: pointer;

  display: -webkit-box;
  line-clamp: 3;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.actions {
  display: flex;
  gap: 12px;
}

.actions button {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-weight: 500;
  background: #3b82f6;
  color: white;
  transition: opacity 0.2s;
}

.actions button:hover {
  opacity: 0.9;
}

.resolve-btn {
  background: #22c55e;
}

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-content {
  background: white;
  padding: 24px;
  border-radius: 12px;
  width: 500px;
  max-width: 90%;
}

.modal-description {
  margin-top: 12px;
  font-size: 14px;
  color: #333;
}

.btn-close {
  margin-top: 16px;
  padding: 6px 14px;
  border-radius: 8px;
  border: none;
  background: #ef4444;
  color: white;
  cursor: pointer;
}
</style>
