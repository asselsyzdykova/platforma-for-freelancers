<template>
  <div class="admin-page">
    <header class="admin-header">
      <div>
        <h1>Admin Dashboard</h1>
        <p class="subtitle">Overview of platform activity and student marketplace health.</p>
      </div>
      <div class="actions">
          <RouterLink :to="{ name: 'AdminChats' }" class="btn ghost">My Chats</RouterLink>
          <button class="btn ghost">Export report</button>
          <button class="btn primary">Create announcement</button>
      </div>
    </header>

    <section class="stats-grid">
      <div class="stat-card">
        <p class="label">Total users</p>
        <h2>{{ stats.totalUsers }}</h2>
        <span class="trend" :class="stats.userGrowth < 0 ? 'down' : 'up'">
          {{ stats.userGrowth < 0 ? '▼' : '▲' }} {{ Math.abs(stats.userGrowth) }}% this month
        </span>
      </div>
      <div class="stat-card">
        <p class="label">Student freelancers</p>
        <h2>{{ stats.freelancers }}</h2>
        <span class="trend" :class="stats.freelancerGrowth < 0 ? 'down' : 'up'">
          {{ stats.freelancerGrowth < 0 ? '▼' : '▲' }} {{ Math.abs(stats.freelancerGrowth) }}% this month
        </span>
      </div>
      <div class="stat-card">
        <p class="label">Active projects</p>
        <h2>{{ stats.activeProjects }}</h2>
        <span class="trend" :class="stats.projectGrowth < 0 ? 'down' : 'up'">
          {{ stats.projectGrowth < 0 ? '▼' : '▲' }} {{ Math.abs(stats.projectGrowth) }}% this month
        </span>
      </div>
      <div class="stat-card">
        <p class="label">Pending reviews</p>
        <h2>{{ stats.pendingReviews }}</h2>
        <span class="trend">{{ stats.pendingNote }}</span>
      </div>
    </section>

    <section class="admin-panels custom-admin-panels">
      <div class="panel recent-signups-panel">
        <div class="panel-head">
          <h3>Recent signups</h3>
          <select v-model="filterRole" class="filter">
            <option value="all">All roles</option>
            <option value="freelancer">Freelancers</option>
            <option value="client">Clients</option>
          </select>
        </div>
        <div class="table" aria-label="Recent signups table">
          <div class="table-row header">
            <span>User</span>
            <span>Role</span>
            <span>University</span>
            <span>Joined</span>
          </div>
          <template v-if="filteredUsers.length">
            <div v-for="user in filteredUsers" :key="user.id" class="table-row">
              <span class="user">
                <span class="avatar" :aria-label="user.name || user.email">
                  {{ user.name ? initials(user.name) : '?' }}
                </span>
                <span>
                  <strong>{{ user.name || '—' }}</strong>
                  <small>{{ user.email }}</small>
                </span>
              </span>
              <span class="role-cell">
                <span
                  class="pill"
                  :class="rolePillClass(user.role)"
                  :aria-label="user.role"
                >
                  {{ user.role ? capitalize(user.role) : 'User' }}
                </span>
              </span>
              <span class="university-cell">
                {{ user.university && user.university.trim() ? user.university : '—' }}
              </span>
              <span>{{ formatJoined(user.joined) }}</span>
            </div>
          </template>
          <div v-else class="table-row empty-row">
            <span class="empty-message" colspan="4">No recent signups found.</span>
          </div>
        </div>
      </div>
      <div class="panel system-overview-panel">
        <div class="panel-head">
          <h3>System overview</h3>
          <button class="btn link">View logs</button>
        </div>
        <div class="system-cards">
          <div class="system-card">
            <p>Avg. response time</p>
            <h4>{{ stats.avgResponse }}s</h4>
          </div>
          <div class="system-card">
            <p>Support tickets</p>
            <h4>{{ stats.tickets }}</h4>
          </div>
          <div class="system-card">
            <p>Stripe status</p>
            <h4 class="status ok">Operational</h4>
          </div>
        </div>
        <div class="announcements">
          <h4>Announcements</h4>
          <ul>
            <li v-for="item in announcements" :key="item.id">
              <strong>{{ item.title }}</strong>
              <span>{{ item.meta }}</span>
            </li>
          </ul>
        </div>
      </div>
    </section>

    <section class="admin-panels managers-section">
      <div class="panel">
        <div class="panel-head">
          <h3>Managers</h3>
          <RouterLink :to="{ name: 'CreateManager' }" class="btn link">
        Add Manager
      </RouterLink>
      </div>
        <div class="manager-list">
          <div v-for="manager in managers" :key="manager.id" class="manager-card">
            <div class="manager-content">
              <div class="manager-meta">
                <span class="avatar manager">{{ initials(manager.name) }}</span>
                <div>
                  <strong>{{ manager.name }}</strong>
                  <small>{{ manager.email }}</small>
                </div>
              </div>
              <div class="manager-info">
                <span class="pill" :class="manager.status">{{ manager.status }}</span>
                <span class="department">{{ manager.department }}</span>
              </div>
            </div>
            <div class="manager-actions">
              <button class="btn ghost" @click="openMessageModal(manager)">Message</button>
              <button class="btn rem" @click="askDeleteManager(manager)" :disabled="deletingManagerId === manager.id">
                {{ deletingManagerId === manager.id ? 'Removing...' : 'Remove' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="quick-actions">
      <h3>Quick actions</h3>
      <div class="action-grid">
        <button class="action-card">
          <h4>Verify student account</h4>
          <p>Approve or reject new freelancer signups.</p>
        </button>
        <button class="action-card">
          <h4>Feature a freelancer</h4>
          <p>Promote top student talent for the week.</p>
        </button>
        <button class="action-card">
          <h4>Audit messages</h4>
          <p>Review reported chats and apply actions.</p>
        </button>
      </div>
    </section>

    <div v-if="toast.show" :class="['custom-toast', toast.type]">
      {{ toast.message }}
    </div>

    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-dialog">
        <h4>Delete manager?</h4>
        <p>Are you sure you want to delete <b>{{ managerToDelete?.name }}</b>?</p>
        <div class="modal-actions">
          <button class="btn rem" @click="confirmDeleteManager">Delete</button>
          <button class="btn ghost" @click="cancelDeleteManager">Cancel</button>
        </div>
      </div>
    </div>

    <div v-if="showMessageModal" class="modal-overlay">
      <div class="modal-dialog">
        <h4>Send message to {{ messageRecipient?.name || messageRecipient?.email }}</h4>
        <textarea v-model="messageBody" placeholder="Write your message..." rows="6" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e5e7eb"></textarea>
        <div style="display:flex;gap:12px;justify-content:center;margin-top:14px;">
          <button class="btn primary" @click="sendMessage" :disabled="messageLoading">{{ messageLoading ? 'Sending...' : 'Send' }}</button>
          <button class="btn ghost" @click="closeMessageModal" :disabled="messageLoading">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import api from '@/services/axios'

const filterRole = ref('all')

const stats = reactive({
  totalUsers: 0,
  userGrowth: 0,
  freelancers: 0,
  freelancerGrowth: 0,
  activeProjects: 0,
  projectGrowth: 0,
  pendingReviews: 31,
  pendingNote: '7 require manual check',
  avgResponse: 1.8,
  tickets: 14,
})

const loadAdminStats = async () => {
  try {
    const { data } = await api.get('/admin/stats')
    if (typeof data?.total_users === 'number') {
      stats.totalUsers = data.total_users
      stats.freelancers = data.total_freelancers
      stats.activeProjects = data.active_projects
      recentUsers.value = (data.recent_signups || []).map((user) => ({
        ...user,
        joined: user.created_at ? new Date(user.created_at).toLocaleDateString() : '—',
      }))

    }
    if (typeof data?.user_growth === 'number') {
      stats.userGrowth = data.user_growth
      stats.freelancerGrowth = data.freelancer_growth
      stats.projectGrowth = data.project_growth
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

onMounted(() => {
  loadAdminStats()
  loadManagers()
})

const recentUsers = ref([])

const announcements = [
  { id: 1, title: 'Spring internship fair', meta: 'Published 2 hours ago' },
  { id: 2, title: 'New verification checklist', meta: 'Published yesterday' },
  { id: 3, title: 'Payment update', meta: 'Scheduled for Friday' },
]


const managers = ref([])
const deletingManagerId = ref(null)
const toast = ref({ show: false, message: '', type: 'success' })
const showToast = (message, type = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => { toast.value.show = false }, 2500)
}

const showDeleteModal = ref(false)
const managerToDelete = ref(null)

const askDeleteManager = (manager) => {
  managerToDelete.value = manager
  showDeleteModal.value = true
}

const confirmDeleteManager = async () => {
  if (!managerToDelete.value) return
  deletingManagerId.value = managerToDelete.value.id
  showDeleteModal.value = false
  try {
    await api.delete(`/admin/managers/${managerToDelete.value.id}`)
    managers.value = managers.value.filter((m) => m.id !== managerToDelete.value.id)
    showToast('Manager deleted successfully', 'success')
  } catch {
    showToast('Failed to delete manager', 'error')
  } finally {
    deletingManagerId.value = null
    managerToDelete.value = null
  }
}

const cancelDeleteManager = () => {
  showDeleteModal.value = false
  managerToDelete.value = null
}

const filteredUsers = computed(() => {
  if (filterRole.value === 'all') return recentUsers.value
  return recentUsers.value.filter((user) => user.role === filterRole.value)
})

const initials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .filter(Boolean)
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

const capitalize = (str) =>
  typeof str === 'string' && str.length ? str.charAt(0).toUpperCase() + str.slice(1) : str

const rolePillClass = (role) => {
  switch ((role || '').toLowerCase()) {
    case 'freelancer':
      return 'freelancer-pill'
    case 'client':
      return 'client-pill'
    case 'admin':
      return 'admin-pill'
    default:
      return 'user-pill'
  }
}

const formatJoined = (date) => {
  if (!date || date === '—') return '—'
  const d = new Date(date)
  if (isNaN(d)) return date
  return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
}

const showMessageModal = ref(false)
const messageRecipient = ref(null)
const messageBody = ref('')
const messageLoading = ref(false)


const openMessageModal = (manager) => {
  messageRecipient.value = manager
  messageBody.value = ''
  showMessageModal.value = true
}

const closeMessageModal = () => {
  showMessageModal.value = false
  messageRecipient.value = null
  messageBody.value = ''
}

const sendMessage = async () => {
  if (!messageRecipient.value) return
  if (!messageBody.value.trim()) {
    showToast('Please write a message first', 'error')
    return
  }
  messageLoading.value = true
  try {
    await api.post('/messages', {
      receiver_id: messageRecipient.value.user_id || messageRecipient.value.id,
      body: messageBody.value,
    })
    showToast('Message sent', 'success')
    closeMessageModal()
  } catch {
    showToast('Failed to send message', 'error')
  } finally {
    messageLoading.value = false
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
/* Toast styles */
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
  box-shadow: 0 6px 24px rgba(80,80,120,0.13);
  padding: 12px 20px;
  font-size: 0.98rem;
  font-weight: 600;
  z-index: 10001;
  text-align: center;
  opacity: 0.98;
}
.custom-toast.success { border: 2px solid #16a34a; color: #15803d }
.custom-toast.error { border: 2px solid #dc2626; color: #b91c1c }
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

.btn.primary {
  background: #5b3df5;
  color: white;
}

.btn.ghost {
  background: white;
  border: 1px solid #e5e7eb;
  color: #4338ca;
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

.stat-card {
  background: white;
  padding: 18px;
  border-radius: 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.stat-card .label {
  color: #6b7280;
  font-size: 13px;
  margin-bottom: 6px;
}

.stat-card h2 {
  margin: 0 0 6px;
}

.trend {
  font-size: 12px;
  color: #6b7280;
}

.trend.up {
  color: #16a34a;
}

.trend.down {
  color: #dc2626;
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
.system-overview-panel {
  grid-column: 2 / 3;
  min-width: 0;
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

.panel-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.filter {
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  padding: 6px 10px;
}

.table {
  display: grid;
  gap: 10px;
}

.table-row {
  display: grid;
  grid-template-columns: 220px 120px 220px 120px;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
}

.role-cell {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  min-width: 80px;
}

.table-row.header {
  font-weight: 600;
  color: #6b7280;
  border-bottom: 1px solid #e5e7eb;
}

.user {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user small {
  display: block;
  color: #94a3b8;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #e0e7ff;
  display: grid;
  place-items: center;
  font-weight: 600;
  color: #4338ca;
}

.pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  text-transform: capitalize;
  background: #e5e7eb;
}

.pill.freelancer-pill {
  background: #ede9fe;
  color: #6d28d9;
}
.pill.client-pill {
  background: #dcfce7;
  color: #15803d;
}
.pill.admin-pill {
  background: #fee2e2;
  color: #b91c1c;
}
.pill.user-pill {
  background: #e5e7eb;
  color: #64748b;
}

.university-cell {
  word-break: break-word;
  overflow-wrap: anywhere;
  max-width: 220px;
  white-space: pre-line;
}

.empty-row {
  text-align: center;
  color: #a1a1aa;
  font-style: italic;
  grid-column: 1 / -1;
}
.empty-message {
  grid-column: 1 / -1;
  width: 100%;
  text-align: center;
  padding: 18px 0;
  color: #a1a1aa;
  font-size: 15px;
}

.manager-list {
  display: grid;
  gap: 12px;
}

.manager-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  padding: 12px;
  border-radius: 16px;
  border: 1px solid #eef2ff;
  background: #f9f9ff;
}

.manager-content {
  display: grid;
  gap: 8px;
  min-width: 220px;
  flex: 1 1 260px;
}

.manager-meta {
  display: flex;
  align-items: center;
  gap: 10px;
}

.manager-meta small {
  display: block;
  color: #94a3b8;
}

.avatar.manager {
  background: #fff0f7;
  color: #be185d;
}

.manager-info {
  display: flex;
  flex-wrap: wrap;
  gap: 8px 10px;
  align-items: center;
  font-size: 13px;
}

.pill.active {
  background: #dcfce7;
  color: #15803d;
}

.pill.away {
  background: #fef3c7;
  color: #b45309;
}

.department {
  color: #6b7280;
}

.manager-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: flex-end;
  flex: 0 0 auto;
}

.manager-actions .btn {
  white-space: nowrap;
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
