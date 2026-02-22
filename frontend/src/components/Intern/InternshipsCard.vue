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
  </div>
</template>

<script setup>
defineProps({
  intern: Object
})
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
</style>
