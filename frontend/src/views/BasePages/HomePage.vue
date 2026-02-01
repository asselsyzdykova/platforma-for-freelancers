<template>
  <div class="home">
    <HeroSection />

    <section class="categories">
      <h2>Popular Categories</h2>
      <CategoryList :freelancers="freelancers" />
    </section>

    <section class="freelancers">
      <h2>Top Freelancers</h2>

      <div class="freelancer-grid">
        <FreelancerCard
          v-for="freelancer in freelancers"
          :key="freelancer.id"
          :freelancer="freelancer"
        />
      </div>
    </section>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/axios'
import CategoryList from '@/components/HomePage/CategoryList.vue'
import HeroSection from '@/components/HomePage/HeroSection.vue'

const freelancers = ref([])

onMounted(async () => {
  try {
    const res = await api.get('/freelancers')
    freelancers.value = res.data
  } catch (e) {
    console.error(e)
  }
})
</script>
<style scoped>
.home {
  padding: 40px;
}
section {
  margin-top: 60px;
}
.freelancer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}
</style>
