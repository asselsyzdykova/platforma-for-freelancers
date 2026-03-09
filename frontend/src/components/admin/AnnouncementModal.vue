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
    emit('success')
    emit('close')
  } catch (err) {
    console.error('Failed to create news', err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(40, 40, 60, 0.18);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
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
