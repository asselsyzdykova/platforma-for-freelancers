<template>
  <header class="admin-header">
    <div>
      <h1>Admin Dashboard</h1>
      <p class="subtitle">Overview of platform activity and student marketplace health.</p>
    </div>
    <div class="actions">
      <RouterLink :to="{ name: 'AdminChats' }" class="btn ghost">My Chats</RouterLink>

      <button
        class="btn ghost"
        @click="handleExport"
        :disabled="exporting"
      >
        {{ exporting ? 'Downloading...' : 'Export report' }}
      </button>

      <button
        class="btn primary"
        @click="$emit('createAnnouncement')"
      >
        Create announcement
      </button>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/axios'

defineEmits(['createAnnouncement'])
const exporting = ref(false)

const handleExport = async () => {
  exporting.value = true
  try {
    const response = await api.get('/admin/export-report', {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'admin_report.xlsx')
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    console.error('Export failed:', err)
    alert('Failed to download report')
  } finally {
    exporting.value = false
  }
}
</script>

<style scoped>
.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 28px;
}

.subtitle {
  color: #6b7280;
  margin-top: 6px;
}

.actions {
  display: flex;
  gap: 12px;
}

.btn {
  border: none;
  border-radius: 12px;
  padding: 10px 16px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
  text-decoration: none;
  font-size: 14px;
}

.btn.primary {
  background: #5b3df5;
  color: white;
}

.btn.primary:hover {
  background: #4a2ee0;
}

.btn.ghost {
  background: white;
  border: 1px solid #e5e7eb;
  color: #4338ca;
}

.btn.ghost:hover {
  background: #f9fafb;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .admin-header {
    flex-direction: column;
  }
}
</style>
