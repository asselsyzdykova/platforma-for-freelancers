<template>
  <div class="manager-page">
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
        <button class="btn ghost">Message</button>
        <button class="btn primary">Assign task</button>
      </div>
    </header>

    <section class="summary-grid">
      <div class="summary-card">
        <p>Active tickets</p>
        <h2>{{ stats.activeTickets }}</h2>
        <span class="trend up">▲ {{ stats.ticketGrowth }}% this week</span>
      </div>
      <div class="summary-card">
        <p>Resolved cases</p>
        <h2>{{ stats.resolved }}</h2>
        <span class="trend">Last 7 days</span>
      </div>
      <div class="summary-card">
        <p>Avg. response time</p>
        <h2>{{ stats.responseTime }}h</h2>
        <span class="trend down">▼ {{ stats.responseDrop }}% improvement</span>
      </div>
    </section>

    <section class="content-grid">
      <div class="panel">
        <div class="panel-head">
          <h3>Assigned tasks</h3>
          <button class="btn link">View all</button>
        </div>
        <div class="task-list">
          <div v-for="task in tasks" :key="task.id" class="task-card">
            <div>
              <h4>{{ task.title }}</h4>
              <p>{{ task.description }}</p>
            </div>
            <div class="task-meta">
              <span class="pill" :class="task.status">{{ task.status }}</span>
              <span class="deadline">Due {{ task.deadline }}</span>
            </div>
          </div>
        </div>
      </div>

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
</template>

<script setup>
const manager = {
  name: 'Lucia Hrušková',
  role: 'Senior Manager',
  department: 'Community & Verification',
  email: 'lucia@bezrab.sk',
  location: 'Bratislava, Slovakia',
}

const stats = {
  activeTickets: 18,
  ticketGrowth: 6,
  resolved: 42,
  responseTime: 1.6,
  responseDrop: 12,
}

const tasks = [
  {
    id: 1,
    title: 'Review new freelancer signups',
    description: 'Verify student emails and approve accounts.',
    status: 'in-progress',
    deadline: 'Tomorrow',
  },
  {
    id: 2,
    title: 'Audit reported chat',
    description: 'Check message report from Košice project.',
    status: 'urgent',
    deadline: 'Today',
  },
  {
    id: 3,
    title: 'Coordinate internship fair',
    description: 'Confirm sponsors and update landing page.',
    status: 'scheduled',
    deadline: 'Next week',
  },
]

const activity = [
  {
    id: 1,
    title: 'Approved 12 freelancer profiles',
    detail: 'UNIBA & STU student verifications completed.',
    time: '2 hours ago',
  },
  {
    id: 2,
    title: 'Resolved ticket #482',
    detail: 'Client payout delay issue closed.',
    time: 'Yesterday',
  },
  {
    id: 3,
    title: 'Added new manager',
    detail: 'Support lead invited and assigned.',
    time: '2 days ago',
  },
]

const notes = [
  {
    id: 1,
    title: 'Verification backlog',
    body: 'Need extra help during exam season. Coordinate with support team.',
    date: 'Feb 2, 2026',
  },
  {
    id: 2,
    title: 'University outreach',
    body: 'Plan visit to Žilina campus for new partnership.',
    date: 'Jan 29, 2026',
  },
]

const initials = (name) =>
  name
    .split(' ')
    .map((part) => part[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
</script>

<style scoped>
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

.pill.in-progress {
  background: #e0e7ff;
  color: #4338ca;
}

.pill.urgent {
  background: #fee2e2;
  color: #b91c1c;
}

.pill.scheduled {
  background: #fef3c7;
  color: #b45309;
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
