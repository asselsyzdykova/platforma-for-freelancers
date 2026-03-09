<template>
  <div class="admin-page">
    <AdminHeader @createAnnouncement="showAnnouncementModal = true" />

    <section class="stats-grid">
      <StatCard label="Total users" :value="stats.totalUsers" :trend="stats.userGrowth" />
      <StatCard label="Student freelancers" :value="stats.freelancers" :trend="stats.freelancerGrowth" />
      <StatCard label="Active projects" :value="stats.activeProjects" :trend="stats.projectGrowth" />
      <StatCard label="Subscriptions" :value="stats.subscriptionsCount">
        <template #footer>
          <span class="trend up">Active: {{ stats.activeSubscriptions }} | Free: {{ stats.freeSubscriptions }}</span>
        </template>
      </StatCard>
    </section>

    <div class="admin-panels custom-admin-panels">
      <RecentSignupsTable :users="filteredUsers" v-model:filter="filterRole" />
    </div>

    <section class="managers-section">
      <div class="panel">
        <h3>Managers</h3>
        <ManagerCard v-for="m in managers" :key="m.id" :manager="m" @task="openTaskModal" @delete="askDeleteManager"
          @message="openMessageModal" />
      </div>
    </section>

    <TaskModal v-if="taskModalVisible" :manager="selectedManager" @submit="handleTaskSubmit" />
    <AnnouncementModal v-if="showAnnouncementModal" />
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import api from '@/services/axios'
import AdminHeader from '@/components/admin/AdminHeader.vue'
import ManagerCard from '@/components/admin/ManagerCard.vue'
import StatCard from '@/components/admin/StatCard.vue'
import RecentSignupsTable from '@/components/admin/RecentSignupsTable.vue'
import TaskModal from '@/components/admin/TaskModal.vue'
import AnnouncementModal from '@/components/admin/AnnouncementModal.vue'

const filterRole = ref('all')
const recentUsers = ref([])
const managers = ref([])
const showAnnouncementModal = ref(false)

const taskModalVisible = ref(false)
const selectedManager = ref(null)

const stats = reactive({
  totalUsers: 0,
  userGrowth: 0,
  freelancers: 0,
  freelancerGrowth: 0,
  activeProjects: 0,
  projectGrowth: 0,
  subscriptionsCount: 0,
  activeSubscriptions: 0,
  canceledSubscriptions: 0,
  freeSubscriptions: 0,
  avgResponse: 1.8,
  tickets: 14,
})

const loadAdminStats = async () => {
  try {
    const { data } = await api.get('/admin/stats')
    if (data) {
      stats.totalUsers = data.total_users || 0
      stats.freelancers = data.total_freelancers || 0
      stats.activeProjects = data.active_projects || 0

      recentUsers.value = (data.recent_signups || []).map((user) => ({
        ...user,
        joined: user.created_at ? new Date(user.created_at).toLocaleDateString() : '—',
      }))

      if (data.subscriptions) {
        stats.subscriptionsCount = data.subscriptions.subs_count
        stats.activeSubscriptions = data.subscriptions.subs_active
        stats.freeSubscriptions = data.subscriptions.subs_free
      }

      stats.userGrowth = data.user_growth || 0
      stats.freelancerGrowth = data.freelancer_growth || 0
      stats.projectGrowth = data.project_growth || 0
    }
  } catch (error) {
    console.error('Failed to load admin stats', error)
  }
}

const loadManagers = async () => {
  try {
    const { data } = await api.get('/admin/managers')
    managers.value = Array.isArray(data) ? data : []
  } catch (error) {
    console.error('Failed to load managers', error)
  }
}
const openTaskModal = (manager) => {
  selectedManager.value = manager
  taskModalVisible.value = true
}

const handleTaskSubmit = () => {
  taskModalVisible.value = false
  selectedManager.value = null
}

const askDeleteManager = async (manager) => {
  if (confirm(`Delete manager ${manager.name}?`)) {
    try {
      await api.delete(`/admin/managers/${manager.id}`)
      managers.value = managers.value.filter(m => m.id !== manager.id)
    } catch (e) {
      console.error(e)
    }
  }
}

const filteredUsers = computed(() => {
  if (filterRole.value === 'all') return recentUsers.value
  return recentUsers.value.filter((user) => user.role === filterRole.value)
})

onMounted(() => {
  loadAdminStats()
  loadManagers()
})
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(15px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0
  }

  to {
    opacity: 1
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}

.modal-dialog {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 32px rgba(80, 80, 120, 0.13);
  padding: 32px 28px 22px;
  min-width: 320px;
  max-width: 90vw;
  text-align: center;
}

.modal-dialog h4 {
  margin-bottom: 12px;
  font-size: 1.18em;
  font-weight: 700;
  color: #b91c1c;
}

.modal-dialog p {
  color: #444;
  margin-bottom: 18px;
}

.modal-actions {
  display: flex;
  gap: 16px;
  justify-content: center;
}

.custom-toast {
  position: fixed;
  bottom: 32px;
  left: 50%;
  transform: translateX(-50%);
  min-width: 220px;
  max-width: 90vw;
  background: #fff;
  color: #222;
  border-radius: 12px;
  box-shadow: 0 6px 24px rgba(80, 80, 120, 0.13);
  padding: 12px 20px;
  font-size: 0.98rem;
  font-weight: 600;
  z-index: 10001;
  text-align: center;
  opacity: 0.98;
}

.custom-toast.success {
  border: 2px solid #16a34a;
  color: #15803d
}

.custom-toast.error {
  border: 2px solid #dc2626;
  color: #b91c1c
}

.admin-page {
  padding: 32px 40px 60px;
  background: #f7f6ff;
  min-height: 100vh;
}

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
}


.btn.task {
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  cursor: pointer;
  color: white;
  border: 1px solid black;
}

.btn.task:hover {
  background: #e6e0ff;
  color: black;
}

.btn.link {
  background: transparent;
  color: #5b3df5;
}

.btn.rem {
  background: #fee2e2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

.btn.rem:hover {
  background: #fecaca;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
  margin-bottom: 30px;
}

.admin-panels {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
}

.custom-admin-panels {
  grid-template-columns: 2fr 1fr;
  align-items: start;
}

.recent-signups-panel {
  grid-column: 1 / 2;
  min-width: 0;
}

.managers-section {
  margin-top: 20px;
}

.manager-list {
  display: grid;
  gap: 12px;
}

.system-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}

.system-card {
  background: #f8fafc;
  border-radius: 14px;
  padding: 12px;
}

.status.ok {
  color: #16a34a;
}

.announcements ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.announcements li {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
}

.quick-actions {
  margin-top: 30px;
}

.action-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
  margin-top: 12px;
}

.action-card {
  text-align: left;
  padding: 16px;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  background: white;
  cursor: pointer;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
}

.action-card p {
  color: #6b7280;
}

.action-card h4 {
  color: black;
}

@media (max-width: 900px) {
  .admin-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .table-row {
    grid-template-columns: 1fr;
    gap: 6px;
  }

  .manager-card {
    grid-template-columns: 1fr;
    justify-items: flex-start;
  }

  .table-row.header {
    display: none;
  }
}
</style>
