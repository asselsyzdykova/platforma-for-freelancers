<template>
  <div class="freelancers-page">
    <h1>Find Freelancers</h1>
    <p class="subtitle">Browse professionals by skills, rating and location</p>

    <div class="filters">
      <input v-model="search" type="text" placeholder="Search by name or skill..." />

      <select v-model="category">
        <option value="">All categories</option>
        <option v-for="cat in categories" :key="cat" :value="cat">
          {{ cat }}
        </option>
      </select>
    </div>

    <div class="freelancer-grid">
      <FreelancerCard v-for="freelancer in freelancers" :key="freelancer.id" :freelancer="freelancer" />
    </div>

    <AppPagination v-model="currentPage" :totalPages="totalPages" mode="simple" />

    <p v-if="!freelancers.length" class="empty">No freelancers found</p>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/axios'
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue'
import AppPagination from '@/components/UI/AppPagination.vue'

const route = useRoute()
const router = useRouter()


const search = ref('')
const category = ref('')
const freelancers = ref([])
const pageSize = 8
const totalPages = ref(1)

const currentPage = ref(Number(route.query.page) || 1)
const categories = ref([])

const loadCategories = async () => {
  try {
    const res = await api.get('/skills')
    categories.value = res.data || []
  } catch (e) {
    console.error('failed to load categories', e)
  }
}
let debounceTimeout = null

const loadFreelancers = async () => {
  try {
    const res = await api.get('/freelancers', {
      params: {
        page: currentPage.value,
        per_page: pageSize,
        search: search.value || undefined,
        category: category.value || undefined,
      },
    })

    freelancers.value = res.data?.data || []
    totalPages.value = res.data?.meta?.last_page || 1
  } catch (e) {
    console.error('Failed to load freelancers', e)
    freelancers.value = []
    totalPages.value = 1
  }
}

const updateRouteAndFetch = () => {
  router.replace({
    query: {
      ...route.query,
      page: currentPage.value > 1 ? currentPage.value : undefined,
      search: search.value || undefined,
      category: category.value || undefined
    }
  })
  loadFreelancers()
}

onMounted(() => {
  loadFreelancers()
  loadCategories()
})
watch([search, category], () => {
  currentPage.value = 1

  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    updateRouteAndFetch()
  }, 500)
})

watch(currentPage, (newPage, oldPage) => {
  if (newPage !== oldPage) {
    updateRouteAndFetch()
  }
})
</script>

<style scoped>
.freelancers-page {
  padding: 40px;
}

.subtitle {
  color: #666;
  margin-bottom: 24px;
}

.filters {
  display: flex;
  gap: 16px;
  margin-bottom: 32px;
}

.filters input,
.filters select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.freelancer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.empty {
  margin-top: 40px;
  color: #888;
}
</style>
