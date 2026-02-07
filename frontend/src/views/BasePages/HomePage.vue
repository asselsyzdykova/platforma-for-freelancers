<template>
  <div class="home">
    <HeroSection />

    <section class="slovakia-banner">
      <div class="banner-text">
        <p class="eyebrow">For students in Slovakia only</p>
        <h2>Najdi si flexibilnú prácu popri škole</h2>
        <p>
          Podporujeme slovenských študentov: reálne projekty, férové odmeny a bezpečná komunikácia.
        </p>
      </div>
      <div class="banner-actions">
        <RouterLink class="btn primary" to="/register">Create student account</RouterLink>
        <RouterLink class="btn ghost" to="/freelancers">Explore talent</RouterLink>
      </div>
    </section>

    <section class="discover">
      <div class="section-head">
        <div>
          <h2>Discover student talent</h2>
          <p>Search by skill, interest or name. Only verified Slovakia-based students.</p>
        </div>
        <div class="count">{{ filteredFreelancers.length }} freelancers</div>
      </div>

      <div class="filters">
        <label class="search">
          <span>🔎</span>
          <input v-model="query" type="text" placeholder="Search UI, Vue, marketing..." />
        </label>
        <div class="chips">
          <button
            v-for="cat in categories"
            :key="cat.id"
            :class="['chip', { active: selectedCategory === cat.id }]"
            @click="selectedCategory = cat.id"
            type="button"
          >
            {{ cat.label }}
          </button>
        </div>
      </div>
    </section>

    <section class="categories">
      <h2>Popular Categories</h2>
      <CategoryList :freelancers="freelancers" />
    </section>

    <section class="freelancers">
      <div class="section-head">
        <h2>Top student freelancers</h2>
        <p>Fresh talent ready for internships, short gigs and semester-friendly projects.</p>
      </div>

      <div class="freelancer-grid">
        <FreelancerCard
          v-for="freelancer in filteredFreelancers"
          :key="freelancer.id"
          :freelancer="freelancer"
        />
      </div>
    </section>

    <section class="campus">
      <div class="campus-card">
        <div class="campus-left">
          <h2>Campus hubs</h2>
          <p>Pick a city to see what students are building right now.</p>
          <div class="city-tabs">
            <button
              v-for="city in cities"
              :key="city.name"
              :class="['city', { active: activeCity === city.name }]"
              @click="activeCity = city.name"
              type="button"
            >
              {{ city.name }}
            </button>
          </div>
        </div>
        <div class="campus-right">
          <div class="city-card">
            <h3>{{ activeCity }}</h3>
            <p>{{ cityHighlights[activeCity].tagline }}</p>
            <ul>
              <li v-for="item in cityHighlights[activeCity].points" :key="item">{{ item }}</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/axios'
import CategoryList from '@/components/HomePage/CategoryList.vue'
import HeroSection from '@/components/HomePage/HeroSection.vue'

const freelancers = ref([])
const query = ref('')
const selectedCategory = ref('all')
const activeCity = ref('Bratislava')

const categories = [
  { id: 'all', label: 'All', match: [] },
  { id: 'web', label: 'Web dev', match: ['web', 'frontend', 'backend', 'vue', 'react', 'js'] },
  { id: 'design', label: 'Design', match: ['design', 'ui', 'ux', 'figma', 'graphic'] },
  { id: 'marketing', label: 'Marketing', match: ['marketing', 'social', 'seo', 'content'] },
  { id: 'writing', label: 'Writing', match: ['writing', 'copy', 'content', 'blog'] },
  { id: 'data', label: 'Data', match: ['data', 'analysis', 'excel', 'sql'] },
]

const cities = [
  { name: 'Bratislava' },
  { name: 'Košice' },
  { name: 'Žilina' },
  { name: 'Nitra' },
  { name: 'Prešov' },
  { name: 'Banská Bystrica' },
]

const cityHighlights = {
  Bratislava: {
    tagline: 'Startup energy and fintech projects.',
    points: ['Product design', 'Frontend gigs', 'Pitch decks'],
  },
  Košice: {
    tagline: 'Tech labs and creative agencies.',
    points: ['Branding', 'Backend support', 'Mobile UI'],
  },
  Žilina: {
    tagline: 'Engineering-focused teams.',
    points: ['3D visuals', 'Research support', 'Web apps'],
  },
  Nitra: {
    tagline: 'E-commerce and content.',
    points: ['Social media', 'Shop setup', 'Copywriting'],
  },
  Prešov: {
    tagline: 'Local businesses growing online.',
    points: ['Landing pages', 'SEO basics', 'Logo refresh'],
  },
  'Banská Bystrica': {
    tagline: 'Community projects and NGOs.',
    points: ['Grant decks', 'Visual identity', 'WordPress help'],
  },
}

const filteredFreelancers = computed(() => {
  const q = query.value.trim().toLowerCase()
  const selected = categories.find((c) => c.id === selectedCategory.value)
  return freelancers.value.filter((f) => {
    const skills = Array.isArray(f.skills) ? f.skills : []
    const name = String(f.name || '').toLowerCase()
    const role = String(f.role || '').toLowerCase()
    const skillText = skills.join(' ').toLowerCase()

    const matchesQuery = !q || name.includes(q) || role.includes(q) || skillText.includes(q)

    if (selected?.id === 'all') return matchesQuery

    const matchesCategory = selected?.match?.some((m) => skillText.includes(m))
    return matchesQuery && matchesCategory
  })
})

onMounted(async () => {
  try {
    const res = await api.get('/freelancers', { params: { per_page: 100 } })
    freelancers.value = res.data?.data || []
  } catch (e) {
    console.error(e)
  }
})
</script>

<style scoped>
.home {
  position: relative;
  padding: 40px 40px 80px;
  overflow: hidden;
  background: radial-gradient(circle at top, #f7f5ff 0%, #ffffff 60%);
}
section {
  margin-top: 60px;
}
.slovakia-banner {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  padding: 28px 32px;
  border-radius: 20px;
  background: linear-gradient(135deg, #5b3df5, #7c3aed);
  color: white;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 20px 40px rgba(91, 61, 245, 0.2);
}
.slovakia-banner .eyebrow {
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-size: 12px;
  opacity: 0.8;
  margin-bottom: 8px;
}
.slovakia-banner h2 {
  font-size: 28px;
  margin-bottom: 8px;
}
.banner-actions {
  display: flex;
  gap: 12px;
}
.btn {
  padding: 10px 18px;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.btn.primary {
  background: white;
  color: #5b3df5;
}
.btn.ghost {
  border: 1px solid rgba(255, 255, 255, 0.5);
  color: white;
}
.section-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}
.section-head p {
  color: #6b7280;
}
.count {
  background: #f3f4ff;
  color: #4f46e5;
  padding: 8px 14px;
  border-radius: 999px;
  font-weight: 600;
  white-space: nowrap;
}
.filters {
  display: grid;
  gap: 16px;
}
.search {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 14px;
  border-radius: 14px;
  background: white;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}
.search input {
  border: none;
  outline: none;
  flex: 1;
  font-size: 14px;
}
.chips {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.chip {
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  background: white;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}
.chip.active {
  background: #5b3df5;
  color: white;
  border-color: #5b3df5;
  box-shadow: 0 10px 20px rgba(91, 61, 245, 0.2);
}
.freelancer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}
.campus-card {
  background: #f9fafb;
  border-radius: 24px;
  padding: 28px;
  display: grid;
  gap: 24px;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
}
.city-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 12px;
}
.city {
  padding: 8px 12px;
  border-radius: 10px;
  background: white;
  border: 1px solid #e5e7eb;
  cursor: pointer;
  font-size: 13px;
  transition: all 0.2s ease;
}
.city.active {
  background: #111827;
  color: white;
}
.city-card {
  background: white;
  border-radius: 18px;
  padding: 20px;
  box-shadow: inset 0 0 0 1px #f1f5f9;
}
.city-card ul {
  margin-top: 12px;
  padding-left: 18px;
  color: #4b5563;
}
</style>
