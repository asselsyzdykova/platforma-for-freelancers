<template>
  <aside class="sidebar">
    <nav class="menu">
      <RouterLink :to="{ name: 'FreelancerProfile' }" class="menu-item" active-class="active">
        Profile
      </RouterLink>

      <RouterLink :to="{ name: 'BillingAndPayments' }" class="menu-item" active-class="active">
        Billing & Payments
      </RouterLink>

      <RouterLink :to="{ name: 'MyProjects' }" class="menu-item" active-class="active">
        Projects
      </RouterLink>

      <RouterLink :to="{ name: 'FreelancerChats' }" class="menu-item" active-class="active">
        <span>Chats</span>
        <span v-if="hasUnread" class="chat-badge"></span>
      </RouterLink>

      <RouterLink :to="{ name: 'MyProposals' }" class="menu-item" active-class="active">
        My Proposals
      </RouterLink>

      <RouterLink :to="{ name: 'Settings' }" class="menu-item" active-class="active">
        Settings
      </RouterLink>

      <RouterLink :to="{ name: 'Support' }" class="menu-item" active-class="active">
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
    default: 'Freelancer Name',
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
  min-height: 100vh;
  background: #e6e0ff;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;
}

.user-box {
  text-align: center;
  margin-bottom: 40px;
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
</style>
