<template>
  <div class="toast-container">
    <transition-group name="toast" tag="div" class="toast-list">
      <div v-for="toast in notifications.toasts" :key="toast.id" class="toast" :class="toast.type">
        <div class="toast-message">{{ toast.message }}</div>
        <button class="toast-close" @click="notifications.remove(toast.id)">✕</button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useNotificationStore } from '@/stores/notificationStore'

const notifications = useNotificationStore()
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  pointer-events: none;
}

.toast-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.toast {
  min-width: 260px;
  max-width: 360px;
  background: #ffffff;
  border-radius: 12px;
  padding: 12px 14px;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  pointer-events: auto;
  border: 1px solid #e8e8ef;
}

.toast-message {
  font-size: 14px;
  color: #1b1b1f;
  line-height: 1.4;
}

.toast-close {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #6b6b78;
  font-size: 14px;
}

.toast.success {
  border-left: 5px solid #22c55e;
}

.toast.error {
  border-left: 5px solid #ef4444;
}

.toast.info {
  border-left: 5px solid #3b82f6;
}

.toast.warning {
  border-left: 5px solid #f59e0b;
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.2s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
