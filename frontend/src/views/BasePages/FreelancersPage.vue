<template>
  <div class="freelancers-page">
    <h1>Find Freelancers</h1>
    <p class="subtitle">
      Browse professionals by skills, rating and location
    </p>

    <!-- FILTERS -->
    <div class="filters">
      <input
        v-model="search"
        type="text"
        placeholder="Search by name or skill..."
      />

      <select v-model="category">
        <option value="">All categories</option>
        <option value="Frontend">Frontend</option>
        <option value="Backend">Backend</option>
        <option value="Design">Design</option>
      </select>
    </div>

    <!-- LIST -->
    <div class="freelancer-grid">
      <FreelancerCard
        v-for="freelancer in filteredFreelancers"
        :key="freelancer.id"
        :freelancer="freelancer"
      />
    </div>

    <p v-if="!filteredFreelancers.length" class="empty">
      No freelancers found
    </p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/axios'
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue'

const search = ref('')
const category = ref('')
const freelancers = ref([])

// LOAD FREELANCERS FROM BACKEND
onMounted(async () => {
  try {
    const res = await api.get('/freelancers')
    freelancers.value = res.data
  } catch (e) {
    console.error('Failed to load freelancers', e)
  }
})

// FILTER LOGIC
const filteredFreelancers = computed(() => {
  return freelancers.value.filter(f => {
    const matchCategory =
      !category.value || f.role === category.value

    const matchSearch =
      f.name.toLowerCase().includes(search.value.toLowerCase()) ||
      (f.skills || []).join(' ').toLowerCase().includes(search.value.toLowerCase())

    return matchCategory && matchSearch
  })
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
