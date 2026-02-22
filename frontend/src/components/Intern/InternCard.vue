<template>
  <div class="internships-container">
    <div class="internships-grid">
      <div
        class="card-in">
        <div class="header">
          <img
            v-if="intern.avatar_url"
            :src="intern.avatar_url"
            class="avatar-img"
            alt="Client avatar"
          />
          <h3 class="client-name">{{ intern.name || 'Company' }}</h3>
        </div>

        <h2 class="company">{{ intern.company }}</h2>
        <h3 class="title">{{ intern.title }}</h3>

        <p class="description">{{ intern.description }}</p>

        <div class="meta">
          <p class="cena">{{ intern.price }}</p>
          <p class="cas">Duration: {{ intern.time }}</p>
          <p class="cas">Number of interns: {{ intern.number }}</p>
        </div>

        <button class="btn-apply" @click="toApply(intern.id)">
          To Apply
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const props = defineProps({
  intern: {
    type: Object,
    required: true
  }
})
const router = useRouter()

const toApply = () => {
  router.push(`/internships/${props.intern.id}`)
}
</script>



<style scoped>
.internships-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.internships-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 28px;
}

.card-in {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(93, 58, 155, 0.15);
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card-in:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 32px rgba(93, 58, 155, 0.25);
}

.header {
  display: flex;
  align-items: center;
  gap: 16px;
}

.avatar-img {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e0d4ff;
  background: #f8f5ff;
}

.client-name {
  margin: 0;
  font-size: 1.4rem;
  font-weight: 700;
  color: #3c1f7b;
}

.company {
  margin: 8px 0 4px;
  font-size: 1.5rem;
  color: #2c3e50;
}

.title {
  margin: 0 0 12px;
  font-size: 1.25rem;
  color: #5D3A9B;
  font-weight: 600;
}

.description {
  margin: 0;
  color: #555;
  line-height: 1.5;
  flex-grow: 1;
}

.meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
  font-size: 0.95rem;
  color: #444;
}

.cena {
  font-weight: bold;
  font-size: 1.15rem;
  color: #2e7d32;
}

.cas {
  margin: 4px 0;
}

.btn-apply {
  margin-top: auto;
  align-self: center;
  width: 100%;
  max-width: 220px;
  padding: 12px 24px;
  border-radius: 12px;
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: white;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.25s ease;
}

.btn-apply:hover {
  background: white;
  color: #5D3A9B;
  box-shadow: 0 0 0 3px rgba(93, 58, 155, 0.3);
}
</style>
