<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>Support</h1>

      <div class="support-card">
        <p class="subtitle">
          If you have any issues or questions, contact our support team.
        </p>

        <div class="support-item">
          <label>Subject</label>
          <input type="text" placeholder="Enter subject" />
        </div>

        <div class="support-item">
          <label>Message</label>
          <textarea rows="5" placeholder="Describe your problem..."></textarea>
        </div>

        <button class="send-btn">Send message</button>
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
  margin-bottom: 16px;
}

.subtitle {
  margin-bottom: 20px;
  font-size: 14px;
  color: #555;
}

.support-card {
  background: #e9e3ff;
  border-radius: 30px;
  padding: 40px;
  max-width: 600px;
}

.support-item {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

label {
  font-size: 14px;
  margin-bottom: 6px;
}

input,
textarea {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ccc;
}

.send-btn {
  padding: 12px 24px;
  background: #5b4b8a;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
}
</style>
