<template>
  <aside class="sidebar">
    <nav class="menu">
      <RouterLink :to="{ name: 'ClientProfile' }" class="menu-item" active-class="active">
        Profile
      </RouterLink>

      <RouterLink :to="{ name: 'ClientInternships' }" class="menu-item" active-class="active">
        Internships
      </RouterLink>

      <RouterLink :to="{ name: 'ClientChats' }" class="menu-item" active-class="active">
        <span>Chats</span>
        <span v-if="hasUnread" class="chat-badge"></span>
      </RouterLink>

      <RouterLink :to="{ name: 'ClientSettings' }" class="menu-item" active-class="active">
        Settings
      </RouterLink>

      <RouterLink :to="{ name: 'ClientSupport' }" class="menu-item" active-class="active">
        Support
      </RouterLink>
    </nav>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/axios'

defineProps({
  userName: {
    type: String,
    default: 'Client Name',
  },
})

const hasUnread = ref(false)

const loadUnread = async () => {
  try {
    const res = await api.get('/conversations')
    const list = res.data?.data || res.data || []
    const unread = list.reduce((sum, conv) => sum + (conv.unread_count || 0), 0)
    hasUnread.value = unread > 0
  } catch (e) {
    console.error('Failed to load chat unread count', e)
  }
}

onMounted(loadUnread)
</script>

<style scoped>
.sidebar {
  width: 260px;
  min-height: 100%;
  align-self: stretch;
  background: #e6e0ff;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
}

.menu {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.menu-item {
  padding: 12px 20px;
  border-radius: 14px;
  color: #333;
  text-decoration: none;
  font-size: 15px;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.menu-item:hover {
  background: white;
}

.menu-item.active {
  background: #5b4b8a;
  color: white;
}

.chat-badge {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  background: #ef4444;
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.7);
}

@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    min-height: auto;
    height: auto;
    padding: 10px 5px;
    position: sticky;
    top: 0;
    z-index: 100;
    flex-direction: row;
    align-items: center;
    overflow-x: auto;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    background: #e6e0ff;
  }

  .menu {
    flex-direction: row;
    gap: 8px;
    width: 100%;
    justify-content: flex-start;
    padding: 0 10px;
    white-space: nowrap;
  }

  .menu-item {
    padding: 8px 15px;
    font-size: 13px;
    flex-shrink: 0;
    border-radius: 10px;
  }

  .sidebar::-webkit-scrollbar {
    display: none;
  }
  .sidebar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
}
</style>
