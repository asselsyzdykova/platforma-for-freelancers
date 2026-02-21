<template>
  <div class="intern">
    <div class="first-block">
      <h2>Internships</h2>
    </div>
    <div class="intern-card">
      <InternCard
      v-for="intern in interns"
        :key="intern.id"
        :intern="intern"/>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import InternCard from '@/components/Intern/InternCard.vue'
import api from '@/services/axios'

const interns = ref([])

onMounted(async () => {
    console.log('Mounted called')

  try {
    const response = await api.get('/internships')
    console.log('DATA:', response.data)
    interns.value = response.data
  } catch (error) {
    console.error('Failed to load internships:', error)
  }
})
</script>
<style scoped>
.first-block{
  border-radius: 10px;
  margin: 30px;
  box-shadow: 0 2px 6px rgba(93, 58, 155, 0.9);
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(10px);
  margin-left: 500px;
  margin-right: 500px;
}
.first-block h2 {
  color: #2c3e50;
  font-size: 36px;
  text-align: center;
}
</style>
