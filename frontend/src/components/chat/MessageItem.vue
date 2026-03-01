<template>
  <div :class="['message', message.sender_id === userId ? 'out' : 'in']">
    <div class="meta">{{ message.sender.name }} • {{ formattedDate }}</div>

    <div class="body" v-if="message.body">{{ message.body }}</div>

    <div class="attachment" v-if="message.attachment_url">
      <button
        v-if="isImage(message.attachment_url)"
        class="attachment-image-button"
        @click="$emit('open-attachment', message.attachment_url)"
      >
        <img :src="message.attachment_url" alt="Attachment" />
      </button>

      <div v-else class="attachment-file" @click="$emit('download', message)">
        <span class="attachment-icon">📄</span>
        <span class="attachment-filename">{{ message.attachment_name || 'File' }}</span>
      </div>

      <a
        class="attachment-download"
        :href="message.attachment_url"
        :download="message.attachment_name || ''"
      >
        Download
      </a>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  message: { type: Object, required: true },
  userId: { type: [Number, String], required: true }
})

const formattedDate = computed(() => {
  return new Date(props.message.created_at).toLocaleString()
})

const isImage = (url) => /\.(png|jpe?g|gif|webp)$/i.test(String(url))
</script>

<style scoped>
.message {
  padding: 12px 14px;
  border-radius: 14px;
  max-width: 70%;
  display: inline-block;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
  word-break: break-word;
  overflow-wrap: anywhere;
}

.message.in {
  background: #ffffff;
  align-self: flex-start;
  border: 1px solid rgba(0, 0, 0, 0.04);
}

.message.out {
  background: #ece6ff;
  margin-left: auto;
  border: 1px solid rgba(91, 61, 245, 0.12);
}

.meta {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 8px;
}

.body {
  font-size: 15px;
  color: #2f2f2f;
  word-break: break-word;
  overflow-wrap: anywhere;
}

.attachment {
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.attachment img {
  max-width: 220px;
  max-height: 160px;
  border-radius: 10px;
  display: block;
}

.attachment-image-button {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
}

.attachment-file {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 10px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  cursor: pointer;
  max-width: 240px;
}

.attachment-icon {
  font-size: 18px;
}

.attachment-filename {
  font-size: 13px;
  color: #4f46e5;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.attachment-download {
  font-size: 12px;
  color: #4f46e5;
  text-decoration: underline;
}
</style>
