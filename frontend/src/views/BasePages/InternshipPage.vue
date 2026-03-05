<template>
  <div class="intern">
    <div class="first-block">
      <h2>Internships</h2>
    </div>
    <div class="internships-container">
      <div class="internships-grid">
        <InternCard v-for="intern in interns" :key="intern.id" :intern="intern" />
      </div>
      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>

        <span class="page-info">
          Page {{ currentPage }} of {{ totalPages }}
        </span>

        <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import InternCard from '@/components/Intern/InternCard.vue'
import api from '@/services/axios'

const route = useRoute()
const router = useRouter()

const interns = ref([])
const totalPages = ref(1)
const perPage = ref(9)

const currentPage = ref(Number(route.query.page) || 1)


const loadInternships = async () => {
  try {
    const response = await api.get('/internships', {
      params: {
        page: currentPage.value,
        per_page: perPage.value
      }
    })

    interns.value = response.data.data
    totalPages.value = response.data.last_page
  } catch (error) {
    console.error('Failed to load internships:', error)
  }
}

loadInternships()

watch(currentPage, (newPage) => {
  router.push({
    query: { ...route.query, page: newPage }
  })
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
.first-block {
  border-radius: 10px;
  margin: 30px auto;
  box-shadow: 0 2px 6px rgba(93, 58, 155, 0.9);
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(10px);
  max-width: 600px;
  padding: 20px;
}

.first-block h2 {
  color: #2c3e50;
  font-size: 36px;
  text-align: center;
}

.internships-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px 40px;
}

.internships-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 28px 24px;
}


@media (max-width: 900px) {
  .internships-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }

  .first-block h2 {
    font-size: 28px;
  }
}

@media (max-width: 600px) {
  .internships-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .internships-container {
    padding: 0 14px 40px;
  }

  .first-block {
    margin: 20px 14px;
    padding: 16px;
  }

  .first-block h2 {
    font-size: 24px;
  }
}

.pagination {
  margin-top: 32px;
  display: flex;
  gap: 16px;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
}

.pagination button {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 1rem;
  color: #555;
}
</style>
