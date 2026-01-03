<template>
  <div class="freelancers-page">
    <h1>Find Freelancers</h1>
    <p class="subtitle">
      Browse professionals by skills, rating and location
    </p>

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

    <!-- Freelancers list -->
    <div class="freelancer-grid">
      <FreelancerCard
        v-for="freelancer in filteredFreelancers"
        :key="freelancer.id"
        :freelancer="freelancer"
      />
    </div>
  </div>
</template>

<script>
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue'

export default {
  name: 'FreelancersPage',
  components: {
    FreelancerCard,
  },
  data() {
    return {
      search: '',
      category: '',
      freelancers: [
        {
          id: 1,
          name: 'Alice Johnson',
          role: 'Frontend',
          rating: 4.8,
          location: 'Nitra, Slovakia',
          skills: ['Vue.js', 'CSS'],
        },
        {
          id: 2,
          name: 'Bob Smith',
          role: 'Design',
          rating: 4.6,
          location: 'Bratislava',
          skills: ['Figma', 'UI/UX'],
        },
      ],
    }
  },
  computed: {
    filteredFreelancers() {
      return this.freelancers.filter(f =>
        (this.category === '' || f.role === this.category) &&
        (
          f.name.toLowerCase().includes(this.search.toLowerCase()) ||
          f.skills.join(' ').toLowerCase().includes(this.search.toLowerCase())
        )
      )
    },
  },
}
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
</style>
