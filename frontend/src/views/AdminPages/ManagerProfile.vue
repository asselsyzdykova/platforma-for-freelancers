<template>
  <div class="manager-page" v-if="manager && stats">
    <header class="manager-header">
      <div class="header-left">
        <div class="avatar">{{ initials(manager.name) }}</div>
        <div>
          <h1>{{ manager.name }}</h1>
          <p class="subtitle">{{ manager.role }} • {{ manager.department }}</p>
          <div class="meta">
            <span>{{ manager.email }}</span>
            <span class="dot">•</span>
            <span>{{ manager.location }}</span>
          </div>
        </div>
      </div>
      <div class="header-actions">
          <RouterLink :to="{ name: 'ManagerChats' }" class="btn ghost">My Chats</RouterLink>
        <button class="btn primary">Assign task</button>
      </div>
    </header>
            <!--1 kvadrat-->
    <section class="summary-grid">
      <div class="summary-card">
        <p>Active tickets</p>
        <h2>{{ stats.activeTickets }}</h2>
        <span class="trend up">▲ {{ stats.ticketGrowth }}% this week</span>
      </div>
            <!--2 kvadrat-->
      <div class="summary-card">
        <p>Resolved cases</p>
        <h2>{{ stats.resolved }}</h2>
        <span class="trend">Last 7 days</span>
      </div>
            <!--3 kvadrat-->
      <div class="summary-card">
        <p>Avg. response time</p>
        <h2>{{ stats.responseTime }}h</h2>
        <span class="trend down">▼ {{ stats.responseDrop }}% improvement</span>
      </div>
    </section>
            <!--4 kvadrat-->
    <section class="content-grid">
      <div class="panel">
        <div class="panel-head">
          <h3>Assigned tasks</h3>
        </div>
    <div class="task-list">
      <div v-for="task in tasks" :key="task.id" class="task-card"
      @click="toggleTask(task.id)">

      <div>
        <h4>{{ task.title }}</h4>
        <span
        v-if="task.status"
        class="pill"
        :class="task.status.toLowerCase()">
        {{ task.status }}
      </span>
      <span v-else class="pill not-set">Not set</span>
    </div>

        <transition name="fade">
        <div v-if="openedTaskId === task.id" class="task-details">
          <p>{{ task.description }}</p>
          <div class="status-buttons">
            <button @click.stop="updateStatus(task, 'Urgent')">Urgent</button>
            <button @click.stop="updateStatus(task, 'In-Progress')">In Progress</button>
            <button @click.stop="updateStatus(task, 'Done')">Done</button>
          </div>
        </div>
      </transition>
        <div class="task-meta">
          <span class="deadline">Due {{ formatDate(task.deadline) }}</span>
        </div>
      </div>
    </div>

    <div class="pagination" v-if="pagination.lastPage > 1">
      <button
      class="btn link"
      :disabled="pagination.currentPage === 1"
      @click="loadTasks(pagination.currentPage - 1)">
      Prev
      </button>
      <button
      v-for="page in pagination.lastPage"
      :key="page"
      class="btn link"
      :class="{ 'primary': page === pagination.currentPage }"
      @click="loadTasks(page)">
      {{ page }}
      </button>
      <button class="btn link"
      :disabled="pagination.currentPage === pagination.lastPage"
      @click="loadTasks(pagination.currentPage + 1)">Next</button>
    </div>
  </div>
            <!--5 kvadrat-->
      <div class="panel">
        <div class="panel-head">
          <h3>Recent activity</h3>
          <button class="btn link">Audit log</button>
        </div>
        <ul class="activity">
          <li v-for="item in activity" :key="item.id">
            <span class="activity-dot"></span>
            <div>
              <strong>{{ item.title }}</strong>
              <p>{{ item.detail }}</p>
            </div>
            <span class="time">{{ item.time }}</span>
          </li>
        </ul>
      </div>
    </section>
            <!--POD VOPROSOM-->
    <section class="notes">
      <div class="panel">
        <div class="panel-head">
          <h3>Manager notes</h3>
          <button class="btn link">Add note</button>
        </div>
        <div class="notes-grid">
          <div v-for="note in notes" :key="note.id" class="note-card">
            <h4>{{ note.title }}</h4>
            <p>{{ note.body }}</p>
            <span>{{ note.date }}</span>
          </div>
        </div>
      </div>
    </section>
  </div>
  <div v-else class="loading">
    Loading manager dashboard...
  </div>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import api from '@/services/axios'
const manager = ref(null)
const stats = ref(null)
const tasks = ref([])
const activity = ref([])
const notes = ref([])

const pagination = ref({
  currentPage: 1,
  lastPage: 1,
  perPage: 5,
  total: 0,
})

const initials = (name) =>
  name
    .split(' ')
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()

const formatDate = (date) => {
  if (!date) return 'No deadline';
  const d = new Date(date);
  return d.toLocaleDateString();
};

const loadDashboard = async () => {
  try {
    const res = await api.get('/manager/dashboard')
    manager.value = res.data.manager
    stats.value = res.data.stats
    activity.value = res.data.activity
    notes.value = res.data.notes
  } catch (err) {
    console.error('Failed to load manager data:', err)
  }
}

const loadTasks = async (page = 1) => {
  try {
    const res = await api.get(`/manager/tasks?page=${page}`)
    tasks.value = res.data.data
    pagination.value.currentPage = res.data.current_page
    pagination.value.lastPage = res.data.last_page
    pagination.value.perPage = res.data.per_page
    pagination.value.total = res.data.total
  } catch (err) {
    console.error('Failed to load tasks:', err)
  }
}

onMounted(async () => {
  await loadDashboard()
  await loadTasks()
})

const openedTaskId = ref(null)

const toggleTask = (id) => {
  openedTaskId.value =
    openedTaskId.value === id ? null : id
}

const updateStatus = async (task, newStatus) => {
  try {
    await api.patch(`/manager/tasks/${task.id}`, {
      status: newStatus
    })

    task.status = newStatus
  } catch (err) {
    console.error('Failed to update status:', err)
  }
}
</script>

<style scoped>

.pill.in-progress {
  background: #e0e7ff;
  color: #4338ca;
}

.pill.urgent {
  background: #fee2e2;
  color: #b91c1c;
}

.pill.done {
  background: #dcfce7;
  color: #15803d;
}
.pill.not-set {
  background: #f1f5f9;
  color: #94a3b8;
}

.task-details {
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid #e5e7eb;
}

.status-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 10px;
}

.status-buttons button {
  padding: 6px 14px;
  border-radius: 999px;
  border: none;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

/* urgent */
.status-buttons button:nth-child(1) {
  background: #fee2e2;
  color: red;
}

.status-buttons button:nth-child(1):hover {
  background: red;
  color: white;
}

/* In Progress */
.status-buttons button:nth-child(2) {
  background: #e0e7ff;
  color: #4338ca;
}

.status-buttons button:nth-child(2):hover {
  background: #5b3df5;
  color: white;
}

/* Done */
.status-buttons button:nth-child(3) {
  background: #dcfce7;
  color: #15803d;
}

.status-buttons button:nth-child(3):hover {
  background: #16a34a;
  color: white;
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.pagination {
  margin-top: 16px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.pagination .btn.link {
  padding: 6px 12px;
  font-size: 13px;
}

.pagination .btn.primary {
  background: #5b3df5;
  color: white;
}
.manager-page {
  padding: 32px 40px 60px;
  background: #f7f6ff;
  min-height: 100vh;
}

.manager-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 28px;
}

.header-left {
  display: flex;
  gap: 16px;
  align-items: center;
}

.avatar {
  width: 64px;
  height: 64px;
  border-radius: 18px;
  display: grid;
  place-items: center;
  background: #ede9fe;
  color: #6d28d9;
  font-weight: 700;
  font-size: 20px;
}

.subtitle {
  color: #6b7280;
  margin: 4px 0 8px;
}

.meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  color: #94a3b8;
  font-size: 14px;
}

.dot {
  color: #cbd5f5;
}

.header-actions {
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

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.summary-card {
  background: white;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.summary-card p {
  color: #6b7280;
  font-size: 13px;
}

.summary-card h2 {
  margin: 6px 0;
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

.content-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
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

.task-list {
  display: grid;
  gap: 12px;
}

.task-card {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 14px;
  border-radius: 14px;
  border: 1px solid #eef2ff;
  background: #f8f9ff;
}

.task-card h4 {
  margin: 0 0 6px;
}

.task-card p {
  margin: 0;
  color: #6b7280;
  font-size: 13px;
}

.task-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
}

.pill {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  text-transform: capitalize;
  background: #e5e7eb;
}
.deadline {
  font-size: 12px;
  color: #9ca3af;
}

.activity {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  gap: 12px;
}

.activity li {
  display: grid;
  grid-template-columns: 10px 1fr auto;
  gap: 12px;
  align-items: start;
  font-size: 14px;
}

.activity-dot {
  width: 10px;
  height: 10px;
  margin-top: 6px;
  border-radius: 50%;
  background: #a855f7;
}

.activity p {
  margin: 4px 0 0;
  color: #6b7280;
}

.time {
  color: #94a3b8;
  font-size: 12px;
  white-space: nowrap;
}

.notes {
  margin-top: 24px;
}

.notes-grid {
  display: grid;
  gap: 12px;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}

.note-card {
  padding: 14px;
  border-radius: 14px;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
}

.note-card p {
  color: #6b7280;
  font-size: 13px;
}

.note-card span {
  font-size: 12px;
  color: #9ca3af;
}

@media (max-width: 900px) {
  .manager-header {
    flex-direction: column;
  }

  .task-card {
    flex-direction: column;
    align-items: flex-start;
  }

  .task-meta {
    align-items: flex-start;
  }
}
</style>
