<template>
  <div class="project-card">
    <h3>{{ project.title }}</h3>
    <p class="desc">{{ project.description }}</p>
    <div class="meta">
      <span>💰 {{ project.budget }} €</span>
      <span>📅 {{ project.deadline }}</span>
      <span>📌 {{ project.status }}</span>
    </div>
    <div class="actions">
      <button class="secondary" @click="$emit('view', project.id)">View proposals</button>
      <button class="danger" @click="$emit('delete', project.id)">Delete project</button>
      <RouterLink :to="{ name: 'CreateProject', query: { editId: project.id } }" class="secondary">
        Update
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
defineProps(['project'])
defineEmits(['view', 'delete'])
</script>
<style scoped>
.project-card {
  background: #fff;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
}

.project-card h3 {
  margin-bottom: 6px;
}

.desc {
  color: #555;
  margin-bottom: 12px;
}

.meta {
  display: flex;
  gap: 20px;
  font-size: 14px;
  margin-bottom: 14px;
}

.actions {
  display: flex;
  gap: 12px;
}

.secondary {
  background: #eee;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.danger {
  background: #ff4d4f;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}
</style>
