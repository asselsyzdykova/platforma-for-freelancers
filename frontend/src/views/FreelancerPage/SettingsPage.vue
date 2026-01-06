<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>Settings</h1>

      <div class="settings-card">
        <div class="setting-item">
          <label>Full name</label>
          <input type="text" placeholder="Your name" />
        </div>

        <div class="setting-item">
          <label>Email</label>
          <input type="email" placeholder="your@email.com" />
        </div>

        <div class="setting-item">
          <label>Password</label>
          <input type="password" placeholder="********" />
        </div>

        <div class="setting-item">
          <label>Language</label>
          <select>
            <option>English</option>
            <option>Slovak</option>
            <option>Russian</option>
          </select>
        </div>

        <div class="setting-item">
          <label>Notifications</label>
          <input type="checkbox" checked />
        </div>

        <button class="save-btn">Save changes</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
</script>

<style scoped>
.page-layout {
  display: flex;
  min-height: 100vh;
  background: #f7f6ff;
}

.content {
  flex: 1;
  padding: 40px;
}

h1 {
  font-size: 32px;
  margin-bottom: 24px;
}

.settings-card {
  background: #e9e3ff;
  border-radius: 30px;
  padding: 40px;
  max-width: 600px;
}

.setting-item {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

label {
  font-size: 14px;
  margin-bottom: 6px;
}

input,
select {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
}

.save-btn {
  margin-top: 20px;
  padding: 12px 24px;
  background: #5b4b8a;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
}
</style>
