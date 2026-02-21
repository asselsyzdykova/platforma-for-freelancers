<template>
  <div class="intern">
    <div class="first-block">
      <h2>Internships</h2>
    </div>
    <div class="intern-card">
      <InternCard/>
    </div>
  </div>
</template>
<script setup>
import InternCard from '@/components/Intern/InternCard.vue'
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
