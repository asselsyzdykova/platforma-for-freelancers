<template>
  <div class="intern-card">
    <h3>{{ intern.title }}</h3>
    <p>{{ intern.description }}</p>
    <p><strong>Company:</strong> {{ intern.company }}</p>
    <p>
      <strong>Type:</strong> {{ intern.stipendType }}
      <span v-if="intern.stipendType === 'paid'"> - {{ intern.price }}</span>
    </p>
    <p><strong>Positions left:</strong> {{ intern.number }}</p>
    <p><strong>Time:</strong> {{ intern.time }}</p>

    <div class="btn2">
      <button class="btn" @click="goToProposals">View proposals</button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()

const props =defineProps({
  intern: Object
})
const goToProposals = () => {
  router.push(`/internships/${props.intern.id}/applications`)
}
</script>

<style scoped>
.intern-card {
  background: #f3efff;
  padding: 20px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.intern-card h3 {
  margin-bottom: 8px;
}

.btn2 {
  display: flex;
  justify-content: right;
}

.btn {
  cursor: pointer;
  border-radius: 10px;
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  border: none;
  padding: 10px;
  color: white;
}

.btn:hover {
  background: white;
  color: #5D3A9B;
}
</style>
