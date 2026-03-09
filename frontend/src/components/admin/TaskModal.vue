<template>
  <div class="modal-overlay-task" @click.self="$emit('close')">
    <div class="modal-task">
      <div class="modal-header">
        <h2>Create Task for {{ manager?.name }}</h2>
        <button @click="$emit('close')" class="close-btn">✖</button>
      </div>

      <div class="form-group">
        <label>Task title</label>
        <input v-model="form.title" placeholder="Enter a task title..." />
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea v-model="form.description" rows="4"></textarea>
      </div>

      <div class="form-group">
        <label>Deadline</label>
        <input ref="deadlineInput" placeholder="Select deadline" class="date-input" />
      </div>

      <div class="modal-actions">
        <button class="btn primary" :disabled="loading" @click="submit">
          {{ loading ? 'Creating...' : 'Create Task' }}
        </button>
        <button class="btn ghost" @click="$emit('close')">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'

const props = defineProps(['manager', 'loading'])
const emit = defineEmits(['close', 'submit'])

const deadlineInput = ref(null)
const form = reactive({
  title: '',
  description: '',
  deadline: ''
})

onMounted(() => {
  flatpickr(deadlineInput.value, {
    dateFormat: "Y-m-d",
    minDate: "today",
    onChange: (_, dateStr) => form.deadline = dateStr
  })
})

const submit = () => emit('submit', { ...form, manager_id: props.manager.id })
</script>
<style scoped>
.date-input {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 10px 12px;
  font-size: 14px;
  cursor: pointer;
}
.modal-overlay-task {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.55);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  animation: fadeIn 0.2s ease;
}
.modal-task {
  width: 100%;
  max-width: 500px;
  background: #ffffff;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
  animation: slideUp 0.25s ease;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.modal-header h2 {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  opacity: 0.6;
  transition: 0.2s;
}

.close-btn:hover {
  opacity: 1;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 18px;
}

.form-group label {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 6px;
  color: #334155;
}

.form-group input,
.form-group textarea {
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  transition: 0.2s;
  resize: none;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: #6366f1;
  outline: none;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.form-group.checkbox {
  flex-direction: row;
  align-items: center;
}

.form-group.checkbox input {
  margin-right: 8px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 20px;
}

.flatpickr-calendar {
  border-radius: 16px;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.btn.primary {
  background: #5b3df5;
  color: white;
}

.btn.ghost {
  background: white;
  border: 1px solid #e5e7eb;
  color: #4338ca;
}

.btn.ghost:hover {
  background: #4338ca;
  color: white;
}
</style>
