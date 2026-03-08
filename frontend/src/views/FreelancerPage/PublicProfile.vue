<template>
  <div class="public-profile-layout">

    <FreelancerProfileCard v-if="freelancer" :freelancer="freelancer" @open-certificate="selectedCertificate = $event"
      @message="showMessageModal = true" @back="router.push('/freelancers')" />

    <CertificateModal v-if="selectedCertificate" :image="selectedCertificate" @close="selectedCertificate = null" />

    <MessageModal v-if="showMessageModal" :freelancer="freelancer" @close="showMessageModal = false" />

    <div v-if="isLoading" class="loading">
      Loading freelancer...
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/axios'
import FreelancerProfileCard from '@/components/FreelancerPageMenu/FreelancerProfileCard.vue'
import CertificateModal from '@/components/FreelancerPageMenu/CertificateModal.vue'
import MessageModal from '@/components/FreelancerPageMenu/MessageModal.vue'
const route = useRoute()
const router = useRouter()

const freelancer = ref(null)
const isLoading = ref(true)

const selectedCertificate = ref(null)
const showMessageModal = ref(false)

const fetchFreelancer = async (id) => {
  try {
    const res = await api.get(`/freelancers/${id}`)
    freelancer.value = res.data?.data || res.data

  } catch (e) {
    console.error('Failed to load freelancer:', e)
    router.push('/freelancers')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  const id = route.params.id
  if (id) fetchFreelancer(id)
})
</script>

<style scoped>
.public-profile-layout {
  max-width: 1000px;
  margin: 40px auto;
}

.loading {
  text-align: center;
  margin-top: 40px;
}

@media (max-width: 768px) {
  .public-card {
    flex-direction: column;
    gap: 20px;
    padding: 20px;
  }

  .public-left {
    width: 100%;
    text-align: center;
  }

  .public-right {
    width: 100%;
  }

  .cert-grid {
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
  }

  .avatar-img {
    width: 120px;
    height: 120px;
    margin-bottom: 10px;
  }
}
</style>
