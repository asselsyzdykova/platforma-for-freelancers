<template>
  <div v-if="modelValue" class="report-modal" @click.self="close">
    <div class="report-box">
      <h3>Report User</h3>

      <label class="report-label">Reason</label>
      <select v-model="reportReason" class="report-select">
        <option disabled value="">Select reason</option>
        <option>Spam</option>
        <option>Offensive behavior</option>
        <option>Fraud</option>
        <option>Inappropriate content</option>
        <option>Violation of terms</option>
        <option>Other</option>
      </select>

      <label class="report-label">Description</label>
      <textarea v-model="reportDescription" placeholder="Describe the problem..." rows="3"
        class="report-textarea"></textarea>

      <div class="report-actions">
        <button class="cancel-btn" @click="close">Cancel</button>
        <button class="submit-btn" @click="submitReport">Submit</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'


const props = defineProps({
  reportedUserId: {
    type: [String, Number],
    required: true
  },
  modelValue: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'submitted'])

const reportReason = ref('')
const reportDescription = ref('')

const notifications = useNotificationStore()

const close = () => {
  emit('update:modelValue', false)
  reportReason.value = ''
  reportDescription.value = ''
}

const submitReport = async () => {
  if (!reportReason.value) {
    notifications.error('Please select a reason')
    return
  }

  try {
    await api.post('/reports', {
      reported_user_id: props.reportedUserId,
      reason: reportReason.value,
      description: reportDescription.value
    })

    notifications.success('Report submitted successfully')
    emit('submitted')
    close()
  } catch (e) {
    console.error(e)
    notifications.error('Failed to submit report')
  }
}
</script>

<style scoped>
.report-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.report-box {
  background: white;
  padding: 24px;
  border-radius: 16px;
  width: 380px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.report-label {
  font-size: 14px;
  font-weight: 600;
}

.report-select,
.report-textarea {
  width: 100%;
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #ddd;
  font-family: inherit;
}

.report-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
}

.cancel-btn {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
}

.submit-btn {
  padding: 8px 14px;
  border-radius: 8px;
  background: #ef4444;
  color: white;
  border: none;
  cursor: pointer;
}
</style>
