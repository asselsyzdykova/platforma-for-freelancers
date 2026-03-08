<template>
  <div class="form-group certificates-group">
    <label>Certificates</label>
    <input type="file" multiple @change="onFilesChange" accept="image/*" />
    <div class="certificates-list">
      <div v-for="(cert, index) in certificates" :key="index" class="certificate-item">
        <span>{{ cert }}</span>
        <button type="button" class="remove-skill" @click="$emit('removeExistingCertificate', index)">×</button>
      </div>
      <div v-for="(cert, index) in newCertificates" :key="`new-${index}`" class="certificate-item">
        <span>{{ cert.name }}</span>
        <button type="button" class="remove-skill" @click="$emit('removeNewCertificate', index)">×</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const { certificates, newCertificates } = defineProps({
  certificates: { type: Array, default: () => [] },
  newCertificates: { type: Array, default: () => [] },
})

const emits = defineEmits(['addCertificate', 'removeExistingCertificate', 'removeNewCertificate'])
const maxSizeBytes = 4 * 1024 * 1024

const onFilesChange = (event) => {
  const files = Array.from(event.target.files || [])
  const accepted = files.filter(f => f.size <= maxSizeBytes)
  if (accepted.length) emits('addCertificate', accepted)
}
</script>

<style scoped>
.certificates-list {
  margin-top: 8px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.certificate-item {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f3efff;
  padding: 4px 8px;
  border-radius: 8px;
}
.remove-skill {
  background: transparent;
  border: none;
  color: #3d2db3;
  cursor: pointer;
  font-size: 14px;
  line-height: 1;
  padding: 0;
}
</style>
