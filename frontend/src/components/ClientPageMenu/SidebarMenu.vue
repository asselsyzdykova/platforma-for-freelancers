<template>
  <aside class="sidebar">
    <nav class="menu">
      <RouterLink :to="{ name: 'ClientProfile' }" class="menu-item" active-class="active">
        Profile
      </RouterLink>

      <RouterLink :to="{ name: 'ClientProjects' }" class="menu-item" active-class="active">
        Projects
      </RouterLink>

      <RouterLink :to="{ name: 'ClientChats' }" class="menu-item" active-class="active">
        Chats
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
defineProps({
  userName: {
    type: String,
    default: 'Client Name',
  },
})
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
}

.menu-item:hover {
  background: white;
}

.menu-item.active {
  background: #5b4b8a;
  color: white;
}
</style>
