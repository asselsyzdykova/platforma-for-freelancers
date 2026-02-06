<template>
  <div class="chat-page">
    <h1>Chat with {{ partner?.name || '...' }}</h1>

    <div class="messages">
      <div
        v-for="m in messages"
        :key="m.id"
        :class="['message', m.sender_id === user.id ? 'out' : 'in']"
      >
        <div class="meta">{{ m.sender.name }} • {{ new Date(m.created_at).toLocaleString() }}</div>
        <div class="body" v-if="m.body">{{ m.body }}</div>
        <div class="attachment" v-if="m.attachment_url">
          <button
            v-if="isImage(m.attachment_url)"
            class="attachment-image-button"
            @click="openAttachment(m.attachment_url)"
          >
            <img :src="m.attachment_url" alt="Attachment" />
          </button>
          <div v-else class="attachment-file" @click="downloadAttachment(m)">
            <span class="attachment-icon">📄</span>
            <span class="attachment-filename">{{ m.attachment_name || 'File' }}</span>
          </div>
          <a
            class="attachment-download"
            :href="m.attachment_url"
            :download="m.attachment_name || ''"
          >
            Download
          </a>
        </div>
      </div>
    </div>

    <div v-if="selectedAttachment" class="attachment-modal" @click.self="closeAttachment">
      <button class="attachment-modal-close" @click="closeAttachment">×</button>
      <img :src="selectedAttachment" alt="Attachment preview" />
      <a class="attachment-modal-download" :href="selectedAttachment" download>Download</a>
    </div>

    <form @submit.prevent="sendMessage" class="composer">
      <input v-model="body" placeholder="Write a message..." />
      <label class="file-button">
        <input type="file" @change="onFileChange" />
        📎
      </label>
      <span v-if="attachmentName" class="file-name">{{ attachmentName }}</span>
      <button type="submit">Send</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axios'

const route = useRoute()
const messages = ref([])
const body = ref('')
const attachmentFile = ref(null)
const attachmentName = ref('')
const selectedAttachment = ref(null)
const user = ref({})
const partner = ref(null)
let channel = null

const markConversationRead = async (partnerId) => {
  try {
    await api.post(`/messages/${partnerId}/read`)
  } catch (e) {
    console.error('Failed to mark messages as read', e)
  }
}

onMounted(async () => {
  try {
    const meRes = await api.get('/me')
    user.value = meRes.data

    const partnerId = route.params.id

    const res = await api.get(`/messages/${partnerId}`)
    messages.value = res.data || []

    await markConversationRead(partnerId)

    if (messages.value.length > 0) {
      const firstMsg = messages.value[0]
      partner.value = firstMsg.sender_id === user.value.id ? firstMsg.receiver : firstMsg.sender
    }

    try {
      const echoModule = await import('@/services/echo')
      const echo = echoModule.initEcho ? echoModule.initEcho() : echoModule.default?.initEcho

      if (echo) {
        channel = echo.private(`user.${user.value.id}`)
        channel.listen('MessageSent', async (payload) => {
          messages.value.push(payload)
          if (payload.sender_id === Number(partnerId)) {
            await markConversationRead(partnerId)
          }
        })
      }
    } catch (err) {
      console.warn('Echo not available (live chat disabled)', err)
    }
  } catch (e) {
    console.error(e)
  }
})

onBeforeUnmount(() => {
  if (channel) {
    channel.stopListening('MessageSent')
  }
})

const sendMessage = async () => {
  if (!body.value.trim() && !attachmentFile.value) return
  try {
    const formData = new FormData()
    formData.append('receiver_id', route.params.id)
    if (body.value.trim()) {
      formData.append('body', body.value)
    }
    if (attachmentFile.value) {
      formData.append('attachment', attachmentFile.value)
    }

    const res = await api.post('/messages', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    messages.value.push(res.data)
    body.value = ''
    attachmentFile.value = null
    attachmentName.value = ''
  } catch (e) {
    console.error(e)
  }
}

const onFileChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  attachmentFile.value = file
  attachmentName.value = file.name
}

const isImage = (url) => {
  return /\.(png|jpe?g|gif|webp)$/i.test(String(url))
}

const openAttachment = (url) => {
  selectedAttachment.value = url
}

const closeAttachment = () => {
  selectedAttachment.value = null
}

const downloadAttachment = (message) => {
  if (!message?.attachment_url) return
  const link = document.createElement('a')
  link.href = message.attachment_url
  link.download = message.attachment_name || ''
  link.click()
}
</script>

<style scoped>
.chat-page {
  max-width: 800px;
  margin: 40px auto;
}
.messages {
  max-height: 60vh;
  overflow-y: auto;
  padding: 16px;
  background: #f7f6ff;
  border-radius: 12px;
  margin-bottom: 12px;
}
.message {
  margin-bottom: 12px;
  padding: 10px;
  border-radius: 10px;
  max-width: 70%;
}
.message.in {
  background: white;
  align-self: flex-start;
}
.message.out {
  background: #dfe7ff;
  margin-left: auto;
}
.meta {
  font-size: 12px;
  color: #666;
  margin-bottom: 8px;
}
.body {
  font-size: 15px;
}
.composer {
  display: flex;
  gap: 8px;
  align-items: center;
}
.composer input {
  flex: 1;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}
.file-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}
.file-button input {
  display: none;
}
.file-name {
  font-size: 12px;
  color: #666;
  max-width: 140px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.composer button {
  padding: 10px 14px;
  border-radius: 8px;
  background: #5b3df5;
  color: white;
  border: none;
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
  border-radius: 8px;
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
