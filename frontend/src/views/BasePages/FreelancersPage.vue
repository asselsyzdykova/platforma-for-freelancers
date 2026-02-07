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
      <FreelancerCard
        v-for="freelancer in freelancers"
        :key="freelancer.id"
        :freelancer="freelancer"
      />
    </div>

    <div class="pagination" v-if="totalPages > 1">
      <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
      <button
        v-for="page in totalPages"
        :key="page"
        :class="{ active: page === currentPage }"
        @click="currentPage = page"
      >
        {{ page }}
      </button>
      <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
    </div>

    <p v-if="!freelancers.length" class="empty">No freelancers found</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import api from '@/services/axios'
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue'

const search = ref('')
const category = ref('')
const freelancers = ref([])
const currentPage = ref(1)
const pageSize = 8
const totalPages = ref(1)

const categories = computed(() => {
  const allSkills = freelancers.value.flatMap((f) => f.skills || [])
  return [...new Set(allSkills)]
})

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

onMounted(loadFreelancers)

watch([search, category], () => {
  currentPage.value = 1
  loadFreelancers()
})

watch(currentPage, () => {
  loadFreelancers()
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

.pagination {
  margin-top: 32px;
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
