<template>
  <div class="cert-wrapper">
    <button v-if="showArrows" class="arrow" @click="prev">◀</button>
    <div class="cert-window">
      <div class="cert-track" :style="{ transform: `translateX(-${current * 240}px)` }">
        <div class="cert-card" v-for="cert in certificates" :key="cert">
          <button v-if="isImage(cert)" class="cert-image-button" @click="openCert(cert)">
            <img :src="cert" alt="Certificate" />
          </button>
          <a v-else :href="cert" target="_blank" class="cert-link">View Document</a>
        </div>
      </div>
    </div>
    <button v-if="showArrows" class="arrow" @click="next">▶</button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
const props = defineProps(['certificates'])
const emit = defineEmits(['open'])

const current = ref(0)
const visible = 3
const showArrows = computed(() => props.certificates.length > visible)
const openCert = (url) => {
  emit('open', url)
}
const next = () => { if (current.value < props.certificates.length - visible) current.value++ }
const prev = () => { if (current.value > 0) current.value-- }
const isImage = (url) => /\.(png|jpe?g|gif|webp)$/i.test(String(url))
</script>

<style scoped>
.cert-wrapper {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 12px 0;
}

.cert-window {
  width: min(760px, 72vw);
  overflow: hidden;
}

.cert-track {
  display: flex;
  gap: 18px;
  transition: transform 0.35s ease;
}

.cert-card {
  min-width: 220px;
  height: 140px;
  background: linear-gradient(135deg, #ffffff, #f6f3ff);
  border-radius: 22px;
  box-shadow: 0 10px 24px rgba(91, 61, 245, 0.12);
  border: 1px solid rgba(91, 61, 245, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-size: 14px;
  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease;
}


.cert-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 16px;
}

.cert-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 28px rgba(91, 61, 245, 0.18);
}

.cert-image-button {
  background: none;
  border: none;
  padding: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}

.arrow {
  background: #ffffff;
  border: 1px solid rgba(91, 61, 245, 0.2);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  font-size: 18px;
  cursor: pointer;
  box-shadow: 0 8px 16px rgba(91, 61, 245, 0.15);
  transition:
    transform 0.15s ease,
    box-shadow 0.15s ease;
}

.arrow:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 20px rgba(91, 61, 245, 0.2);
}
</style>
