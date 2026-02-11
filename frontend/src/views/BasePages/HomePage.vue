<template>
  <div class="home">
    <section class="hero">
      <div class="hero-left">
        <span class="pill">Verified Slovak student talent</span>
        <h1>Hire student freelancers for real work — fast.</h1>
        <p>
          Find vetted university students for design, development, marketing and data. Built for
          short gigs and semester schedules.
        </p>
        <div class="hero-actions">
          <RouterLink class="btn primary" to="/freelancers">Find talent</RouterLink>
          <RouterLink class="btn ghost" to="/projects">Browse projects</RouterLink>
        </div>
        <div class="hero-stats">
          <div class="stat">
            <strong>{{ filteredFreelancers.length }}</strong>
            <span>Freelancers</span>
          </div>
          <div class="stat">
            <strong>24h</strong>
            <span>Avg response</span>
          </div>
          <div class="stat">
            <strong>Safe</strong>
            <span>Verified emails</span>
          </div>
        </div>
      </div>
      <div class="hero-right">
        <div class="hero-card">
          <h3>Post a project</h3>
          <p>Describe the task, budget, and timeline. Students apply quickly.</p>
          <RouterLink class="btn primary" to="/create-project">Create project</RouterLink>
        </div>
        <div class="hero-card outline">
          <h3>Looking for work?</h3>
          <p>Build your profile and get offers from local clients.</p>
          <RouterLink class="btn ghost" to="/register">Join as student</RouterLink>
        </div>
      </div>
    </section>

    <section class="student-banner">
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

    <section class="trust">
      <div class="trust-card">
        <div>
          <h2>Built for student teams and local clients</h2>
          <p>Short projects, clear budgets, and verified university emails keep things safe.</p>
        </div>
        <div class="trust-logos">
          <span>Campus labs</span>
          <span>Startup clubs</span>
          <span>NGO partners</span>
          <span>Local agencies</span>
        </div>
      </div>
    </section>

    <section class="discover">
      <div class="section-head">
        <div>
          <h2>Discover student talent</h2>
          <p>Search by skill, interest or name. Only verified Slovakia-based students.</p>
        </div>
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

    <section class="steps">
      <div class="section-head">
        <h2>How it works</h2>
        <p>Post, match, and collaborate — all within a student-friendly flow.</p>
      </div>
      <div class="steps-grid">
        <div class="step">
          <span class="step-num">1</span>
          <h3>Post a brief</h3>
          <p>Share goals, budget, and delivery time. Students respond fast.</p>
        </div>
        <div class="step">
          <span class="step-num">2</span>
          <h3>Review proposals</h3>
          <p>Compare portfolios, skills, and timelines before you decide.</p>
        </div>
        <div class="step">
          <span class="step-num">3</span>
          <h3>Build together</h3>
          <p>Chat, share files, and deliver work in one place.</p>
        </div>
      </div>
    </section>

    <section class="freelancers">
      <div class="section-head">
        <div>
          <h2>Top student freelancers</h2>
          <p>Fresh talent ready for internships, short gigs and semester-friendly projects.</p>
        </div>
        <div class="slider-actions">
          <button class="icon-btn" type="button" @click="scrollSlider(-1)">‹</button>
          <button class="icon-btn" type="button" @click="scrollSlider(1)">›</button>
        </div>
      </div>

      <div class="freelancer-slider" ref="sliderRef">
        <div class="freelancer-slide" v-for="freelancer in filteredFreelancers" :key="freelancer.id">
          <FreelancerCard :freelancer="freelancer" />
        </div>
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
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue'

const freelancers = ref([])
const query = ref('')
const selectedCategory = ref('all')
const activeCity = ref('Bratislava')
const sliderRef = ref(null)

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
  const list = freelancers.value.filter((f) => {
    const skills = Array.isArray(f.skills) ? f.skills : []
    const name = String(f.name || '').toLowerCase()
    const role = String(f.role || '').toLowerCase()
    const skillText = skills.join(' ').toLowerCase()

    const matchesQuery = !q || name.includes(q) || role.includes(q) || skillText.includes(q)

    if (selected?.id === 'all') return matchesQuery

    const matchesCategory = selected?.match?.some((m) => skillText.includes(m))
    return matchesQuery && matchesCategory
  })

  return list
    .slice()
    .sort((a, b) => (Number(b.rating) || 0) - (Number(a.rating) || 0))
    .slice(0, 5)
})

onMounted(async () => {
  try {
    const res = await api.get('/freelancers', { params: { per_page: 100 } })
    freelancers.value = res.data?.data || []
  } catch (e) {
    console.error(e)
  }
})

const scrollSlider = (direction) => {
  if (!sliderRef.value) return
  const cardWidth = sliderRef.value.querySelector('.freelancer-slide')?.offsetWidth || 300
  sliderRef.value.scrollBy({ left: direction * (cardWidth + 24), behavior: 'smooth' })
}
</script>

<style scoped>
.home {
  position: relative;
  padding: 48px 40px 96px;
  overflow: hidden;
  background: radial-gradient(circle at top, #f4f2ff 0%, #ffffff 60%);
}

section {
  margin-top: 70px;
}

.hero {
  display: grid;
  gap: 32px;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  align-items: center;
}

.hero-left h1 {
  font-size: 42px;
  line-height: 1.1;
  margin: 16px 0 12px;
}

.hero-left p {
  color: #4b5563;
  max-width: 520px;
}

.pill {
  display: inline-flex;
  padding: 6px 12px;
  border-radius: 999px;
  background: rgba(91, 61, 245, 0.1);
  color: linear-gradient(135deg, #5D3A9B, #7c3aed);
  font-weight: 600;
  font-size: 12px;
  letter-spacing: 0.02em;
}

.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin: 20px 0 26px;
}

.hero-stats {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}

.stat {
  background: rgba(124, 93, 250, 0.12);
  border: 1px solid rgba(124, 93, 250, 0.18);
  border-radius: 14px;
  padding: 12px 16px;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(6px);
}

.stat strong {
  display: block;
  font-size: 18px;
}

.stat span {
  font-size: 12px;
  color: #6b7280;
}

.hero-right {
  display: grid;
  gap: 16px;
}

.hero-card {
  background: rgba(124, 93, 250, 0.12);
  border: 1px solid rgba(124, 93, 250, 0.18);
  padding: 22px;
  border-radius: 18px;
  box-shadow: 0 16px 36px rgba(15, 23, 42, 0.1);
  backdrop-filter: blur(6px);
}

.hero-card.outline {
  background: rgba(124, 93, 250, 0.08);
  border: 1px solid rgba(124, 93, 250, 0.16);
  box-shadow: none;
}

.hero-card h3 {
  margin-bottom: 6px;
}

.student-banner {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  padding: 30px 34px;
  border-radius: 22px;
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: white;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 20px 40px rgba(91, 61, 245, 0.2);
}

.student-banner .eyebrow {
  text-transform: uppercase;
  letter-spacing: 0.2em;
  font-size: 12px;
  opacity: 0.8;
  margin-bottom: 8px;
}

.student-banner h2 {
  font-size: 28px;
  margin-bottom: 8px;
}

.banner-actions {
  display: flex;
  gap: 12px;
}

.trust-card {
  background: rgba(124, 93, 250, 0.12);
  border: 1px solid rgba(124, 93, 250, 0.18);
  border-radius: 20px;
  padding: 26px;
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(6px);
}

.trust-logos {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  color: #6b7280;
  font-weight: 600;
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
  color: linear-gradient(135deg, #5D3A9B, #7c3aed);
  border: 1px solid #d7d3ff;
}

.btn.ghost {
  border: 1px solid #d7d3ff;
  color: linear-gradient(135deg, #5D3A9B, #7c3aed);
  background: white;
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
  background: rgba(124, 93, 250, 0.14);
  border: 1px solid rgba(124, 93, 250, 0.2);
  color: linear-gradient(135deg, #5D3A9B, #7c3aed);
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
  background: rgba(124, 93, 250, 0.12);
  border: 1px solid rgba(124, 93, 250, 0.18);
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(6px);
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
  border: 1px solid rgba(124, 93, 250, 0.2);
  background: rgba(124, 93, 250, 0.1);
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.chip.active {
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: white;
  border-color: linear-gradient(135deg, #5D3A9B, #7c3aed);
  box-shadow: 0 10px 20px rgba(91, 61, 245, 0.2);
}

.steps-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}

.step {
  background: rgba(124, 93, 250, 0.12);
  border: 1px solid rgba(124, 93, 250, 0.18);
  padding: 20px;
  border-radius: 18px;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(6px);
}

.step-num {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #5D3A9B;
  color: white;
  font-weight: 700;
  margin-bottom: 10px;
}

.freelancer-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.slider-actions {
  display: flex;
  gap: 10px;
}

.icon-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: 1px solid rgba(124, 93, 250, 0.2);
  background: rgba(124, 93, 250, 0.12);
  color: #4f46e5;
  font-size: 18px;
  cursor: pointer;
}

.freelancer-slider {
  display: flex;
  gap: 24px;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  padding-bottom: 6px;
}

.freelancer-slider::-webkit-scrollbar {
  height: 6px;
}

.freelancer-slider::-webkit-scrollbar-thumb {
  background: rgba(124, 93, 250, 0.3);
  border-radius: 999px;
}

.freelancer-slide {
  flex: 0 0 280px;
  scroll-snap-align: start;
}

.campus-card {
  background: rgba(124, 93, 250, 0.12);
  border: 1px solid rgba(124, 93, 250, 0.18);
  border-radius: 24px;
  padding: 28px;
  display: grid;
  gap: 24px;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
  backdrop-filter: blur(6px);
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
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(124, 93, 250, 0.2);
  cursor: pointer;
  font-size: 13px;
  transition: all 0.2s ease;
}

.city.active {
  background: #5D3A9B;
  color: white;
}

.city-card {
  background: rgba(124, 93, 250, 0.1);
  border: 1px solid rgba(124, 93, 250, 0.18);
  border-radius: 18px;
  padding: 20px;
  box-shadow: inset 0 0 0 1px rgba(124, 93, 250, 0.14);
  backdrop-filter: blur(6px);
}

.city-card ul {
  margin-top: 12px;
  padding-left: 18px;
  color: #4b5563;
}


.cta-card .btn.ghost {
  border-color: rgba(255, 255, 255, 0.5);
  color: white;
  background: transparent;
}

.student-banner .btn.ghost {
  border-color: rgba(255, 255, 255, 0.5);
  color: white;
  background: transparent;
}

@media (max-width: 900px) {
  .hero-left h1 {
    font-size: 34px;
  }

  .home {
    padding: 36px 20px 72px;
  }
}
</style>
