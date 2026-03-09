<template>
  <div class="client-card">
    <div class="left">
      <div class="avatar">
        <img v-if="client.avatar_url" :src="client.avatar_url" alt="Avatar" />
      </div>
      <h2>{{ client.user?.name }}</h2>
      <p class="company" v-if="client.company">{{ client.company }}</p>
      <p class="location">📍 {{ client.location }}</p>
    </div>

    <div class="right">
      <p><strong>Projects posted:</strong> {{ stats.posted }}</p>
      <p>
        <strong>Rating:</strong> ⭐ {{ client.rating ?? '—' }}
        <span class="reviews">({{ client.reviews ?? 0 }} reviews)</span>
      </p>
      <p><strong>Active projects:</strong> {{ stats.active }}</p>
      <p><strong>Completed:</strong> {{ stats.completed }}</p>
      <p><strong>Member since:</strong> {{ memberSince }}</p>

      <div class="header-actions">
        <RouterLink :to="{ name: 'EditClientProfile' }" class="edit-btn">
          Edit Page
        </RouterLink>

        <RouterLink :to="{ name: 'CreateProject' }" class="primary-btn">
          + Create Project
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  client: { type: Object, required: true },
  stats: { type: Object, required: true },
  userCreatedAt: { type: String, default: null }
})

const memberSince = computed(() => {
  const createdAt = props.userCreatedAt || props.client?.user?.created_at || props.client?.created_at
  if (!createdAt) return '—'
  return new Date(createdAt).getFullYear()
})
</script>

<style scoped>
.client-card {
  display: flex;
  justify-content: space-between;
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
  margin-bottom: 40px;
}

.left {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.avatar img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  background: #eee;
}

.company { color: #666; }

.right {
  text-align: right;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.reviews {
  color: #777;
  font-size: 14px;
}

.header-actions {
  margin-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.edit-btn {
  padding: 10px 16px;
  border-radius: 10px;
  background: #111;
  color: #fff;
  text-decoration: none;
  text-align: center;
}

.primary-btn {
  padding: 12px 18px;
  border-radius: 10px;
  background: #5b4b8a;
  color: white;
  font-weight: 600;
  text-decoration: none;
  text-align: center;
}

@media (max-width: 768px) {
  .client-card {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  .right {
    text-align: center;
    align-items: center;
    width: 100%;
  }
  .header-actions { width: 100%; }
}
</style>
