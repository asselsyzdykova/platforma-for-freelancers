<template>
  <div class="panel recent-signups-panel">
    <div class="panel-head">
      <h3>Recent signups</h3>
      <select :value="filter" @change="$emit('update:filter', $event.target.value)" class="filter">
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

      <template v-if="users.length">
        <div v-for="user in users" :key="user.id" class="table-row">
          <span class="user">
            <span class="avatar">{{ initials(user.name) }}</span>
            <span>
              <strong>{{ user.name || '—' }}</strong>
              <small>{{ user.email }}</small>
            </span>
          </span>
          <span class="role-cell">
            <span class="pill" :class="rolePillClass(user.role)">
              {{ user.role || 'User' }}
            </span>
          </span>
          <span class="university-cell">
            {{ user.university?.trim() ? user.university : '—' }}
          </span>
          <span>{{ user.joined }}</span>
        </div>
      </template>

      <div v-else class="empty-message">
        No recent signups found.
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps(['users', 'filter'])
defineEmits(['update:filter'])

const initials = (name) => {
  if (!name) return '?'
  return name.split(' ').filter(Boolean).map(n => n[0]).slice(0, 2).join('').toUpperCase()
}

const rolePillClass = (role) => {
  const r = (role || '').toLowerCase()
  if (r === 'freelancer') return 'freelancer-pill'
  if (r === 'client') return 'client-pill'
  return 'user-pill'
}
</script>

<style scoped>
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
.role-cell {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  min-width: 80px;
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

.empty-message {
  grid-column: 1 / -1;
  width: 100%;
  text-align: center;
  padding: 18px 0;
  color: #a1a1aa;
  font-size: 15px;
}
.panel {
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.empty-row {
  text-align: center;
  color: #a1a1aa;
  font-style: italic;
  grid-column: 1 / -1;
}

@media (max-width: 850px) {
  .table-row {
    grid-template-columns: 1fr;
    gap: 8px;
    padding: 16px 0;
    position: relative;
  }

  .table-row.header {
    display: none;
  }
  .user {
    margin-bottom: 4px;
  }

  .role-cell {
    justify-content: flex-start;
  }

  .university-cell::before {
    content: "University: ";
    font-weight: 600;
    color: #94a3b8;
    font-size: 12px;
  }

  .table-row > span:last-child {
    color: #94a3b8;
    font-size: 12px;
  }

  .table-row > span:last-child::before {
    content: "Joined: ";
    font-weight: 600;
  }

  .university-cell {
    max-width: 100%;
  }
}
@media (max-width: 480px) {
  .panel {
    padding: 12px;
  }

  .panel-head {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .filter {
    width: 100%;
  }
}
</style>
