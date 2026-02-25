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
            <div class="delete-btn">
              <button class="button">Delete</button>
            </div>
          </div>
        </div>
      </div>
      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
        <button v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }"
          @click="currentPage = page">
          {{ page }}
        </button>
        <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import api from '@/services/axios'
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import SidebarMenu from '@/components/ClientPageMenu/SidebarMenu.vue'

const route = useRoute()
const router = useRouter()

const internshipId = route.params.id

const proposals = ref([])
const loading = ref(false)

const perPage = ref(9)
const totalPages = ref(1)

const currentPage = ref(Number(route.query.page) || 1)

const loadProposals = async () => {
  loading.value = true
  try {
    const response = await api.get(`/internships/${internshipId}/applications`, {
      params: {
        page: currentPage.value,
        per_page: perPage.value
      }
    })
    const resData = response.data
    proposals.value = resData.data || []
    totalPages.value = resData.last_page || 1
  } catch (e) {
    console.error('Failed to load proposals', e)
    proposals.value = []
  } finally {
    loading.value = false
  }
}
watch(currentPage, (newPage) => {
  router.push({
    query: { ...route.query, page: newPage }
  })
  loadProposals()
})
onMounted(loadProposals)
</script>

<style scoped>
.button{
  border-radius: 10px;
  background: red;
  color: white;
  cursor: pointer;
  border: none;
  padding: 5px;
}
.delete-btn{
  display: flex;
  justify-content: flex-end;
}
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

.pagination {
  margin-top: 24px;
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.pagination button {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}

.pagination button.active {
  background: #5b3df5;
  color: #fff;
  border-color: #5b3df5;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
