<template>
  <div class="answer-page">
    <h2>Report Response</h2>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else-if="report">
      <h3 class="report-title">{{ report.title }}</h3>
      <p class="report-status">Status: <strong>{{ report.status }}</strong></p>

      <div class="response" v-if="report.description">
        <h4>Admin Response:</h4>
        <p>{{ report.description }}</p>
        <small class="time">Answered at: {{ formatDate(report.updated_at) }}</small>
        <small v-if="report.reporter" class="admin">
          From: {{ report.reporter.name }}
        </small>
      </div>

      <div v-else class="no-response">
        <p>This report has not been answered yet.</p>
      </div>
    </div>

    <div v-else class="empty">Report not found.</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axios'

const route = useRoute()
const reportId = route.params.id

const report = ref(null)
const loading = ref(true)

const loadReport = async () => {
  try {
    const res = await api.get(`/reports/${reportId}`)
    report.value = res.data
  } catch (err) {
    console.error('Failed to load report:', err)
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => date ? new Date(date).toLocaleString() : ''
onMounted(() => loadReport())
</script>

<style scoped>
.answer-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 24px;
}

.report-title {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 8px;
}

.report-status {
  font-size: 14px;
  color: #555;
  margin-bottom: 24px;
}

.response {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 12px;
}

.response h4 {
  font-size: 18px;
  margin-bottom: 10px;
}

.response p {
  font-size: 16px;
  color: #333;
}

.time,
.admin {
  display: block;
  margin-top: 6px;
  font-size: 12px;
  color: #555;
}

.no-response {
  background: #fff3cd;
  padding: 16px;
  border-radius: 12px;
  color: #856404;
}

.loading,
.empty {
  text-align: center;
  color: #777;
  font-size: 16px;
  margin-top: 40px;
}
</style>
