<template>

    <div v-if="!partner" class="empty">Loading...</div>
    <div class="chat-page">
      <h1>Chat with {{ partner?.name || '...' }}</h1>

    <div class="messages">
      <div
        v-for="m in messages"
        :key="m.id"
        :class="['message', m.sender_id === user.id ? 'out' : 'in']"
      >
        <div class="meta">{{ m.sender.name }} • {{ new Date(m.created_at).toLocaleString() }}</div>
        <div class="body">{{ m.body }}</div>
      </div>
    </div>

    <form @submit.prevent="sendMessage" class="composer">
      <input v-model="body" placeholder="Write a message..." />
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
const user = ref({})
const partner = ref(null)
let channel = null

onMounted(async () => {
  try {
    const meRes = await api.get('/me')
    user.value = meRes.data

    const partnerId = route.params.id
    const list = await api.get('/freelancers')
    partner.value = (list.data || []).find((f) => String(f.id) === String(partnerId))

    const res = await api.get(`/messages/${partnerId}`)
    messages.value = res.data || []

    let echo = null
    try {
      const echoModule = await import('@/services/echo')
      echo = echoModule.initEcho
        ? echoModule.initEcho()
        : echoModule.default && echoModule.default.initEcho
          ? echoModule.default.initEcho()
          : null

      if (echo) {
        channel = echo.private(`user.${user.value.id}`)
        channel.listen('MessageSent', (payload) => {
          messages.value.push(payload)
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
  if (!body.value.trim()) return
  try {
    const res = await api.post('/messages', {
      receiver_id: route.params.id,
      body: body.value,
    })
    messages.value.push(res.data)
    body.value = ''
  } catch (e) {
    console.error(e)
  }
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
}
.composer input {
  flex: 1;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
}
.composer button {
  padding: 10px 14px;
  border-radius: 8px;
  background: #5b3df5;
  color: white;
  border: none;
}
</style>
