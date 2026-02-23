<template>
  <div class="profile-layout">
    <SidebarMenu />
    <div class="page">
      <h1>Internship Proposals</h1>

      <div v-if="loading" class="empty">Loading...</div>

      <div v-else>
        <div v-if="proposals.length === 0" class="empty">
          No proposals yet
        </div>

        <div v-else class="proposal-list">
          <div class="proposal-card" v-for="proposal in proposals" :key="proposal.id">
            <h3>{{ proposal.name }}</h3>
            <p>{{ proposal.university }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import api from '@/services/axios'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import SidebarMenu from '@/components/ClientPageMenu/SidebarMenu.vue'

const route = useRoute()
const internshipId = route.params.id

const proposals = ref([])
const loading = ref(false)

const loadProposals = async () => {
  loading.value = true
  try {
    const response = await api.get(`/internships/${internshipId}/applications`)
    proposals.value = response.data.data || []
  } catch (e) {
    console.error('Failed to load proposals', e)
    proposals.value = []
  } finally {
    loading.value = false
  }
}

onMounted(loadProposals)
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}

.page {
  width: 60%;
  margin: 40px auto;
}

.empty {
  text-align: center;
  color: #777;
  margin-top: 50px;
}

.proposal-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.proposal-card {
  background: #f3efff;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
</style>
