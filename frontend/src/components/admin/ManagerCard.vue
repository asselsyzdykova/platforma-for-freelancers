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
}

.pill.away {
  background: #fef3c7;
  color: #b45309;
}

.department {
  color: #6b7280;
}
</style>
