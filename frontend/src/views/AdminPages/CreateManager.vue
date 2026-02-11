<template>
  <div class="create-manager-page">
    <h2>Add New Manager</h2>
    <form @submit.prevent="submitForm" class="manager-form">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input v-model="form.name" id="name" type="text" required placeholder="Enter full name" />
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input v-model="form.email" id="email" type="email" required placeholder="Enter email address" />
      </div>
      <div class="form-group">
        <label for="department">Department <span class="optional">(optional)</span></label>
        <input v-model="form.department" id="department" type="text" placeholder="e.g. Support, Projects" />
      </div>
      <div class="form-group">
        <label for="password">Temporary Password</label>
        <input v-model="form.password" id="password" type="text" required placeholder="Create temporary password" />
        <small class="hint">Share this password with the manager. They should change it after first login.</small>
      </div>
      <button class="btn primary" type="submit" :disabled="loading">
        {{ loading ? 'Creating...' : 'Create Manager' }}
      </button>
      <div v-if="error" class="error-message">{{ error }}</div>
      <div v-if="success" class="success-message">Manager created successfully!</div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/axios'

const form = ref({
  name: '',
  email: '',
  department: '',
  password: '',
})
const loading = ref(false)
const error = ref('')
const success = ref(false)

const submitForm = async () => {
  error.value = ''
  success.value = false
  loading.value = true
  try {
    await api.post('/admin/managers', form.value)
    success.value = true
    form.value = { name: '', email: '', department: '', password: '' }
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to create manager.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.create-manager-page {
  max-width: 420px;
  margin: 40px auto;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(80, 80, 120, 0.08);
  padding: 32px 28px 28px;
}
.create-manager-page h2 {
  margin-bottom: 22px;
  font-size: 1.5rem;
  font-weight: 700;
  color: #4338ca;
}
.manager-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
label {
  font-weight: 600;
  color: #444;
}
input[type="text"],
input[type="email"] {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 9px 12px;
  font-size: 1rem;
  background: #f8fafc;
  transition: border 0.2s;
}
input:focus {
  border-color: #5b3df5;
  outline: none;
}
.optional {
  color: #a1a1aa;
  font-weight: 400;
  font-size: 0.95em;
}
.hint {
  color: #6b7280;
  font-size: 0.92em;
}
.btn.primary {
  background: #5b3df5;
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 10px 0;
  font-weight: 600;
  font-size: 1.08em;
  cursor: pointer;
  margin-top: 8px;
  transition: background 0.2s;
}
.btn.primary:disabled {
  background: #a5b4fc;
  cursor: not-allowed;
}
.error-message {
  color: #dc2626;
  margin-top: 8px;
  font-size: 1em;
}
.success-message {
  color: #16a34a;
  margin-top: 8px;
  font-size: 1em;
}
</style>
