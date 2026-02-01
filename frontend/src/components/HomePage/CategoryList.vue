<template>
  <div class="categories">
    <span v-for="cat in topCategories" :key="cat.name">
      {{ cat.name }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  freelancers: {
    type: Array,
    required: true,
  },
})

const topCategories = computed(() => {
  const counter = {}

  const list = props.freelancers || []

  list.forEach(f => {
    ;(f.skills || []).forEach(skill => {
      counter[skill] = (counter[skill] || 0) + 1
    })
  })

  return Object.entries(counter)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 10)
})
</script>


<style scoped>
.categories {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.categories span {
  background: #ece6ff;
  padding: 10px 16px;
  border-radius: 14px;
}
</style>
