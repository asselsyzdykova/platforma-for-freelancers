<template>
  <div class="profile-layout">
    <ClientSidebar />

    <div class="intern-page">
      <h1>Your Internships and proposals</h1>
      <div v-if="loading" class="empty">Loading...</div>
      <div v-else>
        <div v-if="internships.length === 0" class="empty">
          No internships yet
        </div>
        <div v-else class="internships-list">
          <InternshipsCard v-for="intern in internships" :key="intern.id" :intern="intern" @deleted="handleDeleted" />
          <AppPagination v-model="currentPage" :totalPages="totalPages" mode="simple" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import api from '@/services/axios'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'
import InternshipsCard from '@/components/Intern/InternshipsCard.vue'
import AppPagination from '@/components/UI/AppPagination.vue'
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const totalPages = ref(1)
const perPage = ref(9)

const internships = ref([])
const loading = ref(false)

const currentPage = ref(Number(route.query.page) || 1)

const loadInternships = async () => {
  loading.value = true
  try {
    const response = await api.get('/internships/my', {
      params: {
        page: currentPage.value,
        per_page: perPage.value
      }
    })

    internships.value = response.data.data
    totalPages.value = response.data.meta.last_page
  } catch (e) {
    console.error('Failed to load internships', e)
    internships.value = []
  } finally {
    loading.value = false
  }
}

const handleDeleted = (id) => {
  internships.value = internships.value.filter(item => item.id !== id)
}

loadInternships()

watch(currentPage, (newPage) => {
  router.push({
    query: { ...route.query, page: newPage }
  })
  loadInternships()
})

watch(
  () => route.query.page,
  (newPage) => {
    currentPage.value = Number(newPage) || 1
    loadInternships()
  }
)
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}

.intern-page {
  width: 60%;
  padding: 30px;
  margin: 0 auto;
}

.empty {
  color: #777;
  text-align: center;
  margin-top: 60px;
}

.internships-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
</style>
