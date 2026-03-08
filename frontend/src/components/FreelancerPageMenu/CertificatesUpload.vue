<template>
  <div class="form-group certificates-group">
    <label>Certificates</label>
    <input
      type="file"
      multiple
      accept="image/*,.pdf,.jpeg,.jpg,.png,.gif"
      @change="onFilesChange"
    />
    <small class="hint">Max size: 4 MB. Images and PDF only</small>

    <div class="certificates-list" v-if="certificates.length || newCertificates.length">
      <div
        v-for="(cert, index) in certificates"
        :key="`existing-${index}`"
        class="certificate-item"
      >
        <span>{{ getFileName(cert) }}</span>
        <button
          type="button"
          class="remove-btn"
          @click="$emit('remove-existing-certificate', index)"
        >
          ×
        </button>
      </div>

      <div
        v-for="(cert, index) in newCertificates"
        :key="`new-${index}`"
        class="certificate-item"
      >
        <span>{{ cert.name }}</span>
        <button
          type="button"
          class="remove-btn"
          @click="$emit('remove-new-certificate', index)"
        >
          ×
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const {
  certificates = [],
  newCertificates = [],
} = defineProps({
  certificates: {
    type: Array,
    default: () => [],
  },
  newCertificates: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits([
  'add-certificate',
  'remove-existing-certificate',
  'remove-new-certificate',
])

const MAX_SIZE_BYTES = 4 * 1024 * 1024

const onFilesChange = (event) => {
  if (!event?.target?.files) return

  const files = Array.from(event.target.files)
  const validFiles = files.filter((file) => {
    if (file.size > MAX_SIZE_BYTES) {
      alert(`File "${file.name}" is too large (max 4 MB)`)
      return false
    }
    return true
  })

  if (validFiles.length) {
    emit('add-certificate', validFiles)
  }

  event.target.value = ''
}

const getFileName = (cert) => {
  if (typeof cert === 'string') {
    return cert.split('/').pop() || 'Certificate'
  }
  return 'Unknown file'
}
</script>

<style scoped>
.form-group {
  margin-bottom: 20px;
}

.hint {
  font-size: 12px;
  color: #666;
  display: block;
  margin-top: 4px;
}

.certificates-list {
  margin-top: 12px;
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.certificate-item {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f3efff;
  padding: 6px 12px;
  border-radius: 10px;
  font-size: 14px;
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.remove-btn {
  background: transparent;
  border: none;
  color: #ff4d4f;
  font-size: 16px;
  cursor: pointer;
  padding: 0 4px;
  line-height: 1;
}

.remove-btn:hover {
  color: #d9363e;
}
</style>
