<template>
  <div class="home">
    <HeroSection />

    <section class="categories">
      <h2>Popular Categories</h2>
      <CategoryList />
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
<script>
import HeroSection from '@/components/HomePage/HeroSection.vue'
import CategoryList from '@/components/HomePage/CategoryList.vue'
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue'
export default {
  name: 'HomePage',
  components: {
    HeroSection,
    CategoryList,
    FreelancerCard,
  },
  data() {
    return {
      freelancers: [
        {
          id: 1,
          name: 'bebebebeeb',
          role: 'Frontend Developer',
          rating: 4.8,
          reviews: 300,
          location: 'Slovakia, Nitra',
          skills: ['Vue.js'],
        },
        {
          id: 2,
          name: 'kfjvnkjfnvjkf',
          role: 'UI/UX Designer',
          rating: 4.9,
          reviews: 210,
          location: 'Bratislava',
          skills: ['Figma'],
        },
      ],
    }
  },
}
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
