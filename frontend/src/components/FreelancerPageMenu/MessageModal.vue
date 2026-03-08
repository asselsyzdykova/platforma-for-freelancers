<template>
  <div class="message-modal" @click.self="$emit('close')">

    <div class="message-modal-card">

      <button
        class="message-modal-close"
        @click="$emit('close')"
      >
        ×
      </button>

      <h3>
        Message {{ freelancer?.name }}
      </h3>

      <textarea
        v-model="messageText"
        placeholder="Write your message..."
      ></textarea>

      <div class="message-modal-actions">

        <button
          class="secondary"
          @click="$emit('close')"
        >
          Cancel
        </button>

        <button
          @click="sendMessage"
          :disabled="isSending || !messageText.trim()"
        >
          {{ isSending ? 'Sending...' : 'Send' }}
        </button>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/services/axios'

const props = defineProps({
  freelancer:Object
})

const messageText = ref('')
const isSending = ref(false)

const sendMessage = async()=>{

  if(!props.freelancer?.id) return

  isSending.value=true

  try{

    await api.post('/messages',{
      receiver_id:props.freelancer.id,
      body:messageText.value
    })

    messageText.value=''

  }catch(e){

    console.error(e)

  }finally{

    isSending.value=false

  }

}
</script>

<style scoped>
.message-modal{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.6);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:10000;
}

.message-modal-card{
  width:min(520px,92vw);
  background:white;
  padding:22px;
  border-radius:16px;
}

textarea{
  width:100%;
  min-height:120px;
  margin-top:12px;
  padding:12px;
  border-radius:12px;
  border:1px solid #ddd;
}

.message-modal-actions{
  display:flex;
  justify-content:flex-end;
  gap:10px;
  margin-top:12px;
}

.secondary{
  background:#eee;
}

button{
  padding:10px 16px;
  border:none;
  border-radius:10px;
  cursor:pointer;
}

.message-modal-close{
  position:absolute;
  top:12px;
  right:12px;
}
</style>
