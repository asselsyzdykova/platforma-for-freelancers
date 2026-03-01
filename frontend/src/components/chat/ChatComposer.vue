<template>
  <form @submit.prevent="handleSend" class="composer">
    <textarea
      v-model="body"
      placeholder="Write a message..."
      rows="1"
      @keydown.enter.exact.prevent="addNewLine"
      @keydown.ctrl.enter.prevent="handleSend"
      @keydown.meta.enter.prevent="handleSend"
    ></textarea>

    <label class="file-button">
      <input type="file" @change="onFileChange" />
      📎
    </label>
    <span v-if="attachmentName" class="file-name">{{ attachmentName }}</span>
    <button type="submit">Send</button>
  </form>
</template>

<script setup>
import { ref, defineEmits, defineProps } from 'vue'

const props = defineProps({
  initialBody: { type: String, default: '' }
})

const emit = defineEmits(['send'])

const body = ref(props.initialBody)
const attachmentFile = ref(null)
const attachmentName = ref('')

const handleSend = () => {
  if (!body.value.trim() && !attachmentFile.value) return
  emit('send', { body: body.value, file: attachmentFile.value })
  body.value = ''
  attachmentFile.value = null
  attachmentName.value = ''
}

const addNewLine = () => {
  body.value += '\n'
}

const onFileChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  attachmentFile.value = file
  attachmentName.value = file.name
}
</script>

<style scoped>
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
</style>
