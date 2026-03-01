<template>
  <div v-if="visible" class="attachment-modal" @click.self="$emit('close')">
    <button class="attachment-modal-close" @click="$emit('close')">×</button>
    <img :src="url" alt="Attachment preview" />
    <a class="attachment-modal-download" :href="url" download>Download</a>
  </div>
</template>

<script setup>
const { url, visible } = defineProps({
  url: { type: String, required: true },
  visible: { type: Boolean, default: false }
})
</script>

<style scoped>
.attachment-modal {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  gap: 12px;
}

.attachment-modal img {
  max-width: 90vw;
  max-height: 80vh;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.attachment-modal-close {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.9);
  color: #2b2b2b;
  font-size: 24px;
  cursor: pointer;
}

.attachment-modal-download {
  padding: 8px 14px;
  border-radius: 10px;
  background: #ffffff;
  color: #4f46e5;
  text-decoration: none;
  font-weight: 600;
}
</style>
