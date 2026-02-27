<template>
  <div class="ticket-card">
    <div class="ticket-header">
      <h4>{{ ticket.subject }}</h4>
      <span :class="['status', ticket.status]">
        {{ ticket.status }}
      </span>
    </div>

    <p class="meta">
      From: <strong>{{ ticket.user?.name }}</strong>
      | {{ formatDate(ticket.created_at) }}
    </p>

    <p class="description">
      {{ ticket.description }}
    </p>
    <div v-if="!resolved" class="response-section">
      <textarea v-model="localResponse" placeholder="Write your response..."></textarea>

      <div class="actions">
        <button @click="$emit('in-progress', ticket)">
          Mark In Progress
        </button>

        <button class="resolve" @click="resolveTicket">
          Resolve & Send
        </button>
      </div>
    </div>
    <div v-else class="resolved-response">
      <h5>Response:</h5>
      <p>{{ localResponse }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  ticket: Object
})

const emit = defineEmits(['resolve', 'in-progress'])

const localResponse = ref('')
const resolved = ref(false)

onMounted(() => {
  if (props.ticket.status === 'resolved') {
    resolved.value = true
    localResponse.value = props.ticket.response || ''
  }
})

watch(
  () => props.ticket.response,
  (newVal) => {
    localResponse.value = newVal || ''
  }
)

const resolveTicket = () => {
  emit('resolve', {
    ...props.ticket,
    response: localResponse.value
  })
  resolved.value = true
}

const formatDate = (date) => {
  return new Date(date).toLocaleString()
}
</script>
<style scoped>
.ticket-card {
  background: #fff;
  padding: 20px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s;
}

.ticket-card:hover {
  transform: translateY(-2px);
}

.ticket-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.status {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
  color: #fff;
}

.status.new {
  background: #fbc02d;
}

.status.in_progress {
  background: #039be5;
}

.status.resolved {
  background: #43a047;
}

.meta {
  font-size: 13px;
  color: #666;
  margin-bottom: 10px;
}

.description {
  font-size: 14px;
  margin-bottom: 12px;
  color: #333;
}

.response-section textarea {
  width: 100%;
  min-height: 80px;
  border-radius: 8px;
  border: 1px solid #ddd;
  padding: 10px;
  resize: vertical;
  margin-bottom: 12px;
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
  transition: background 0.2s;
}

.actions button:hover {
  opacity: 0.9;
}

.actions .resolve {
  background: #4caf50;
  color: white;
}

.actions button:not(.resolve) {
  background: #039be5;
  color: white;
}
.resolved-response h5 {
  margin-bottom: 4px;
  font-size: 14px;
  font-weight: 600;
}

.resolved-response p {
  font-size: 14px;
  color: #333;
}
</style>
