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
.admin-page {
  padding: 32px 40px 60px;
  background: #f7f6ff;
  min-height: 100vh;
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

.managers-section {
  margin-top: 20px;
}

.panel {
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.panel h3 {
  margin-bottom: 16px;
  font-size: 18px;
  font-weight: 700;
}

.trend {
  font-size: 12px;
  font-weight: 600;
}

.trend.up {
  color: #16a34a;
}

.trend.down {
  color: #dc2626;
}

@media (max-width: 900px) {
  .custom-admin-panels {
    grid-template-columns: 1fr;
  }

  .admin-page {
    padding: 20px;
  }
}

@media (max-width: 768px) {
  .admin-page {
    padding: 16px 12px 40px;
  }

  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
  }

  .admin-panels,
  .custom-admin-panels {
    grid-template-columns: 1fr !important;
    gap: 16px;
  }

  .panel {
    padding: 16px;
    border-radius: 16px;
  }

  :deep(.table-wrapper) {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .panel h3 {
    font-size: 16px;
    margin-bottom: 12px;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }

  h1 {
    font-size: 20px;
  }
}
</style>
