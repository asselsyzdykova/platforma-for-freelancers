<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-task">
      <div class="modal-header">
        <h2>Create Announcement</h2>
        <button @click="$emit('close')" class="close-btn">✖</button>
      </div>

      <div class="form-group">
        <label>Title</label>
        <input v-model="form.title" placeholder="Enter title..." />
      </div>

      <div class="form-group">
        <label>Content</label>
        <textarea v-model="form.content" rows="4" placeholder="Write announcement..."></textarea>
      </div>

      <div class="form-group checkbox">
        <label>
          <input type="checkbox" v-model="form.is_active" />
          Active
        </label>
      </div>

      <div class="modal-actions">
        <button class="btn primary" @click="submit" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create' }}
        </button>
        <button class="btn ghost" @click="$emit('close')">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'

const notifications = useNotificationStore()
const emit = defineEmits(['close', 'success'])
const loading = ref(false)

const form = reactive({
  title: '',
  content: '',
  is_active: true
})

const submit = async () => {
  if (!form.title.trim()) return
  loading.value = true
  try {
    await api.post('/admin/news', form)
    notifications.success('Announcement created successfully!')
    emit('success')
    emit('close')
  } catch (err) {
    console.error('Failed to create news', err)
    notifications.error('Failed to create announcement')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(4px);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-task {
  width: 95%;
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
  font-weight: 700;
  margin: 0;
  color: #1e293b;
}

.close-btn {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  opacity: 0.5;
  transition: 0.2s;
}

.close-btn:hover { opacity: 1; }

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 18px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 6px;
  color: #475569;
}

.form-group input,
.form-group textarea {
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 14px;
  transition: 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: #6366f1;
  outline: none;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-group.checkbox {
  flex-direction: row;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.form-group.checkbox input {
  width: 18px;
  height: 18px;
  accent-color: #6366f1;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}
.btn {
  border: none;
  border-radius: 10px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn.primary {
  background: linear-gradient(135deg, #6366f1, #a855f7);
  color: white;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.btn.primary:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
}

.btn.ghost {
  background: #f8fafc;
  color: #64748b;
  border: 1px solid #e2e8f0;
}

.btn.ghost:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 600px) {
  .modal-actions {
    flex-direction: column-reverse;
  }
  .btn { width: 100%; }
}
</style>
