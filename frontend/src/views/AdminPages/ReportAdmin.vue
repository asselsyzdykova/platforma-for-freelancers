<template>
  <div class="reports-page">
    <h2>Reports Management</h2>

    <div class="filters">
      <select v-model="statusFilter" @change="loadReports(1)">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="resolved">Resolved</option>
      </select>
    </div>

    <div v-if="reports.length" class="report-grid">
      <ReportCard
        v-for="report in reports"
        :key="report.id"
        :report="report"
        @resolve="resolveReport"
        @in-progress="markInProgress"
      />
    </div>

    <p v-else class="empty">No reports found.</p>

    <div
      class="pagination"
      v-if="pagination.last_page > 1"
    >
      <button
        :disabled="pagination.current_page === 1"
        @click="changePage(pagination.current_page - 1)"
      >
        Prev
      </button>

      <span>
        Page {{ pagination.current_page }}
        of {{ pagination.last_page }}
      </span>

      <button
        :disabled="pagination.current_page === pagination.last_page"
        @click="changePage(pagination.current_page + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/axios'
import ReportCard from '@/components/admin/ReportCard.vue'
import { useNotificationStore } from '@/stores/notificationStore'

const notifications = useNotificationStore()

const reports = ref([])
const statusFilter = ref('')

const pagination = ref({
  current_page: 1,
  last_page: 1
})

const loadReports = async (page = 1) => {
  try {
    const res = await api.get('/admin/reports', {
      params: {
        status: statusFilter.value,
        page: page
      }
    })

    reports.value = res.data.data
    pagination.value.current_page = res.data.current_page
    pagination.value.last_page = res.data.last_page

  } catch (err) {
    console.error(err)
    notifications.error('Failed to load reports')
  }
}

const resolveReport = async (id) => {
  try {
    await api.patch(`/admin/reports/${id}`, {
      status: 'resolved'
    })

    notifications.success('Report resolved successfully')
    loadReports(pagination.value.current_page)

  } catch (err) {
    console.error(err)
    notifications.error('Failed to resolve report')
  }
}

const markInProgress = async (id) => {
  try {
    await api.patch(`/admin/reports/${id}`, {
      status: 'in_progress'
    })

    notifications.info('Report marked as in progress')
    loadReports(pagination.value.current_page)

  } catch (err) {
    console.error(err)
    notifications.error('Failed to update report')
  }
}

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  loadReports(page)
}

onMounted(() => {
  loadReports()
})
</script>

<style scoped>
.reports-page {
  padding: 40px;
  max-width: 1100px;
  margin: 0 auto;
}

.reports-page h2 {
  font-size: 28px;
  margin-bottom: 20px;
}

.filters {
  margin-bottom: 24px;
}

.filters select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  cursor: pointer;
}

.report-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}

@media (max-width: 992px) {
  .report-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .report-grid {
    grid-template-columns: 1fr;
  }
}

.empty {
  text-align: center;
  margin-top: 40px;
  color: #888;
  font-size: 16px;
}

.pagination {
  margin-top: 32px;
  display: flex;
  gap: 12px;
  justify-content: center;
  align-items: center;
}

.pagination button {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination button:hover:not(:disabled) {
  background: #f5f5f5;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
