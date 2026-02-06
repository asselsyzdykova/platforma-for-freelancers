<template>
  <div class="admin-page">
    <header class="admin-header">
      <div>
        <h1>Admin Dashboard</h1>
        <p class="subtitle">Overview of platform activity and student marketplace health.</p>
      </div>
      <div class="actions">
        <button class="btn ghost">Export report</button>
        <button class="btn primary">Create announcement</button>
      </div>
    </header>

    <section class="stats-grid">
      <div class="stat-card">
        <p class="label">Total users</p>
        <h2>{{ stats.totalUsers }}</h2>
        <span class="trend up">▲ {{ stats.userGrowth }}% this month</span>
      </div>
      <div class="stat-card">
        <p class="label">Student freelancers</p>
        <h2>{{ stats.freelancers }}</h2>
        <span class="trend up">▲ {{ stats.freelancerGrowth }}% this month</span>
      </div>
      <div class="stat-card">
        <p class="label">Active projects</p>
        <h2>{{ stats.activeProjects }}</h2>
        <span class="trend down">▼ {{ stats.projectDrop }}% this week</span>
      </div>
      <div class="stat-card">
        <p class="label">Pending reviews</p>
        <h2>{{ stats.pendingReviews }}</h2>
        <span class="trend">{{ stats.pendingNote }}</span>
      </div>
    </section>

    <section class="admin-panels">
      <div class="panel">
        <div class="panel-head">
          <h3>Recent signups</h3>
          <select v-model="filterRole" class="filter">
            <option value="all">All roles</option>
            <option value="freelancer">Freelancers</option>
            <option value="client">Clients</option>
          </select>
        </div>
        <div class="table">
          <div class="table-row header">
            <span>User</span>
            <span>Role</span>
            <span>University</span>
            <span>Joined</span>
          </div>
          <div v-for="user in filteredUsers" :key="user.id" class="table-row">
            <span class="user">
              <span class="avatar">{{ initials(user.name) }}</span>
              <span>
                <strong>{{ user.name }}</strong>
                <small>{{ user.email }}</small>
              </span>
            </span>
            <span class="pill" :class="user.role">{{ user.role }}</span>
            <span>{{ user.university || '—' }}</span>
            <span>{{ user.joined }}</span>
          </div>
        </div>
      </div>

      <div class="panel">
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

      <div class="panel">
        <div class="panel-head">
          <h3>Managers</h3>
          <button class="btn link">Add manager</button>
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
              <button class="btn ghost">Message</button>
              <button class="btn rem">Remove</button>
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
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const filterRole = ref('all')

const stats = {
  totalUsers: 1520,
  userGrowth: 12,
  freelancers: 860,
  freelancerGrowth: 18,
  activeProjects: 124,
  projectDrop: 4,
  pendingReviews: 31,
  pendingNote: '7 require manual check',
  avgResponse: 1.8,
  tickets: 14,
}

const recentUsers = [
  {
    id: 1,
    name: 'Mária Kováčová',
    email: 'maria@uniba.sk',
    role: 'freelancer',
    university: 'Univerzita Komenského v Bratislave',
    joined: 'Today',
  },
  {
    id: 2,
    name: 'Jakub Novák',
    email: 'jakub@student.stuba.sk',
    role: 'freelancer',
    university: 'Slovenská technická univerzita v Bratislave',
    joined: 'Yesterday',
  },
  {
    id: 3,
    name: 'Tatra Studios',
    email: 'contact@tatra.sk',
    role: 'client',
    university: null,
    joined: '2 days ago',
  },
  {
    id: 4,
    name: 'Ema Rusnáková',
    email: 'ema@student.upjs.sk',
    role: 'freelancer',
    university: 'Univerzita Pavla Jozefa Šafárika v Košiciach',
    joined: '3 days ago',
  },
]

const announcements = [
  { id: 1, title: 'Spring internship fair', meta: 'Published 2 hours ago' },
  { id: 2, title: 'New verification checklist', meta: 'Published yesterday' },
  { id: 3, title: 'Payment update', meta: 'Scheduled for Friday' },
]

const managers = [
  {
    id: 1,
    name: 'Lucia Hrušková',
    email: 'lucia@bezrab.sk',
    department: 'Community & Verification',
    status: 'active',
  },
  {
    id: 2,
    name: 'Peter Malík',
    email: 'peter@bezrab.sk',
    department: 'Projects & Partnerships',
    status: 'active',
  },
  {
    id: 3,
    name: 'Dominika Urbanová',
    email: 'dominika@bezrab.sk',
    department: 'Support',
    status: 'away',
  },
]

const filteredUsers = computed(() => {
  if (filterRole.value === 'all') return recentUsers
  return recentUsers.filter((user) => user.role === filterRole.value)
})

const initials = (name) =>
  name
    .split(' ')
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
</script>

<style scoped>
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
  grid-template-columns: 1.4fr 0.6fr 1fr 0.6fr;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
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

.pill.freelancer {
  background: #ede9fe;
  color: #6d28d9;
}

.pill.client {
  background: #dcfce7;
  color: #15803d;
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
