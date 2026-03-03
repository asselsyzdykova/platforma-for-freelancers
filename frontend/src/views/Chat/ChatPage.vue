<template>
  <div class="page-layout">
    <SidebarMenu v-if="user.role === 'freelancer'" :userName="user.name" />
    <ClientSidebar v-if="user.role === 'user'" />

    <div class="chat-page">
      <div class="header-chat">
        <h1>Chat with {{ partner?.name || '...' }}</h1>
        <button v-if="user.role != 'admin'" class="report" @click="showReportModal = true">Report</button>
      </div>

      <ReportModal v-model="showReportModal" :reportedUserId="route.params.id" />

      <div class="messages" ref="messagesContainer">
        <button v-if="hasMore" class="load-more" @click="loadMore" :disabled="isLoadingMore">
          {{ isLoadingMore ? 'Loading...' : 'Load older messages' }}
        </button>
        <MessageItem v-for="m in messages" :key="m.id" :message="m" :user-id="user.id"
          @open-attachment="selectedAttachment = $event" />
      </div>

      <AttachmentModal :url="selectedAttachment" :visible="!!selectedAttachment" @close="closeAttachment" />
      <ChatComposer @send="handleSendMessage" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/axios'
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'
import ReportModal from '@/components/chat/ReportModal.vue'
import AttachmentModal from '@/components/chat/AttachmentModal.vue'
import MessageItem from '@/components/chat/MessageItem.vue'
import ChatComposer from '@/components/chat/ChatComposer.vue'
import { useNotificationStore } from '@/stores/notificationStore'

const notifications = useNotificationStore()
const route = useRoute()
const messages = ref([])
const selectedAttachment = ref(null)
const currentPage = ref(1)
const hasMore = ref(false)
const isLoadingMore = ref(false)
const user = ref({})
const partner = ref(null)
let channel = null
const showReportModal = ref(false)
const messagesContainer = ref(null)
const shouldScrollToBottom = ref(true)

//scroll
const scrollToBottom = async (smooth = true) => {
  await nextTick()
  if (!messagesContainer.value) return

  const container = messagesContainer.value
  const distanceToBottom = container.scrollHeight - container.scrollTop - container.clientHeight

  if (shouldScrollToBottom.value || distanceToBottom < 200) {
    container.scrollTo({
      top: container.scrollHeight,
      behavior: smooth ? 'smooth' : 'auto'
    })
  }
}

const handleSendMessage = async ({ body, file }) => {
  if (!body.trim() && !file) return

  try {
    const formData = new FormData()
    formData.append('receiver_id', route.params.id)
    if (body.trim()) formData.append('body', body)
    if (file) formData.append('attachment', file)

    const res = await api.post('/messages', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    messages.value.push(res.data)
    nextTick(() => scrollToBottom(true))
  } catch (e) {
    console.error(e)
    notifications.error('Failed to send message')
  }
}

const markConversationRead = async (partnerId) => {
  try {
    await api.post(`/messages/${partnerId}/read`)
  } catch (e) {
    console.error('Failed to mark messages as read', e)
  }
}

const fetchMessages = async (page = 1, append = true) => {
  const partnerId = route.params.id
  const res = await api.get(`/messages/${partnerId}`, {
    params: { page, per_page: 20 },
  })

  const data = res.data?.data || []
  if (append) {
    messages.value = [...data, ...messages.value]
  } else {
    messages.value = data
  }

  const meta = res.data?.meta
  hasMore.value = meta ? meta.current_page < meta.last_page : false
}

onMounted(async () => {
  try {
    const meRes = await api.get('/me')
    user.value = meRes.data

    const partnerRes = await api.get(`/users/${route.params.id}`)
    partner.value = partnerRes.data

    currentPage.value = 1
    await fetchMessages(currentPage.value, false)
    await markConversationRead(route.params.id)
    shouldScrollToBottom.value = true
    await scrollToBottom(false)
    try {
      const echoModule = await import('@/services/echo')
      const echo = echoModule.initEcho ? echoModule.initEcho() : echoModule.default?.initEcho

      if (echo) {
        channel = echo.private(`user.${user.value.id}`)
        channel.listen('MessageSent', async (payload) => {
          messages.value.push(payload)
          if (payload.sender_id === Number(route.params.id)) {
            await markConversationRead(route.params.id)
          }
          scrollToBottom(true)
        })
      }
    } catch (err) {
      console.warn('Echo not available (live chat disabled)', err)
    }
  } catch (e) {
    console.error(e)
  }
  messagesContainer.value?.addEventListener('scroll', () => {
    if (!messagesContainer.value) return
    const container = messagesContainer.value
    const atBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 100
    shouldScrollToBottom.value = atBottom
  })

  scrollToBottom()
})

channel?.listen('MessageSent', async (payload) => {
  messages.value.push(payload)
  if (payload.sender_id === Number(route.params.id)) {
    await markConversationRead(route.params.id)
  }
  scrollToBottom(true)
})

watch(messages, () => {
}, { deep: true })

const loadMore = async () => {
  if (!hasMore.value || isLoadingMore.value) return
  isLoadingMore.value = true
  try {
    currentPage.value += 1
    await fetchMessages(currentPage.value, true)
  } catch (e) {
    console.error(e)
  } finally {
    isLoadingMore.value = false
  }
}

const closeAttachment = () => {
  selectedAttachment.value = null
}

onBeforeUnmount(() => {
  if (channel) {
    channel.stopListening('MessageSent')
  }
})
</script>

<style scoped>
.report {
  background: red;
  color: white;
  padding: 5px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  margin-bottom: 10px;
}

.chat-page {
  max-width: 1200px;
  margin: 32px auto;
  padding: 0 28px 40px;
}

.page-layout {
  display: flex;
  min-height: 100vh;
  background: #f7f6ff;
}

.chat-page h1 {
  font-size: 28px;
  margin-bottom: 16px;
  color: #2b2b2b;
}

.messages {
  max-height: 62vh;
  overflow-y: auto;
  padding: 18px;
  background: #f7f6ff;
  border-radius: 16px;
  margin-bottom: 14px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  box-shadow: inset 0 0 0 1px rgba(91, 61, 245, 0.08);
}

.composer {
  display: flex;
  gap: 10px;
  align-items: center;
  background: #ffffff;
  padding: 10px;
  border-radius: 14px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.composer textarea {
  flex: 1;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #fafafa;
  resize: none;
  font-family: inherit;
  font-size: 14px;
  line-height: 1.4;
  min-height: 42px;
  max-height: 120px;
  overflow-y: auto;
}

.composer textarea:focus {
  outline: none;
  border-color: #5b3df5;
  box-shadow: 0 0 0 3px rgba(91, 61, 245, 0.12);
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
  padding: 10px 16px;
  border-radius: 10px;
  background: #5b3df5;
  color: white;
  border: none;
  box-shadow: 0 8px 16px rgba(91, 61, 245, 0.2);
  cursor: pointer;
}

.load-more {
  align-self: center;
  padding: 6px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
  font-size: 13px;
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
