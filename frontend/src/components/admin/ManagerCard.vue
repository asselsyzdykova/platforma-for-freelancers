<template>
  <div class="manager-card">
    <div class="manager-content">
      <div class="manager-meta">
        <span class="avatar manager">{{ getInitials(manager.name) }}</span>
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
      <button class="btn ghost" @click="$emit('message', manager)">Message</button>
      <button class="btn rem" @click="$emit('delete', manager)">Remove</button>
      <button class="btn task" @click="$emit('task', manager)">Task</button>
    </div>
  </div>
</template>

<script setup>
defineProps(['manager'])
defineEmits(['message', 'delete', 'task'])

const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').filter(Boolean).map(p => p[0]).slice(0, 2).join('').toUpperCase()
}
</script>
<style scoped>
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

.pill.active {
  background: #dcfce7;
  color: #15803d;
  border-radius: 8px;
  padding: 3px;
}

.pill.away {
  background: #fef3c7;
  color: #b45309;
}

.department {
  color: #6b7280;
}

.btn {
  border: none;
  border-radius: 10px;
  padding: 8px 14px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn.task {
  background: linear-gradient(135deg, #6366f1, #a855f7);
  color: white;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.btn.task:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3);
  filter: brightness(1.1);
}

.btn.rem {
  background: #fff1f2;
  color: #e11d48;
  border: 1px solid #ffe4e6;
}

.btn.rem:hover {
  background: #ffe4e6;
  color: #be123c;
}

.btn.ghost {
  background: white;
  color: #475569;
  border: 1px solid #e2e8f0;
}

.btn.ghost:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
  color: #1e293b;
}

.btn:active {
  transform: scale(0.96);
}

@media (max-width: 600px) {
  .manager-card {
    flex-direction: column;
    align-items: stretch;
  }

  .manager-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 8px;
  }

  .btn.task {
    grid-column: span 2;
    padding: 12px;
  }

  .btn.ghost, .btn.rem {
    padding: 10px;
  }
}
</style>
