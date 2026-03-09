<template>
  <div class="stat-card">
    <p class="label">{{ label }}</p>
    <h2>{{ value }}</h2>
    <slot name="footer">
      <span v-if="trend !== undefined" class="trend" :class="trend < 0 ? 'down' : 'up'">
        {{ trend < 0 ? '▼' : '▲' }} {{ Math.abs(trend) }}% this month
      </span>
    </slot>
  </div>
</template>

<script setup>
defineProps(['label', 'value', 'trend'])
</script>
<style scoped>
.stat-card {
  background: white;
  padding: 18px;
  border-radius: 16px;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}

.stat-card .label {
  color: #6b7280;
  font-size: 13px;
  margin-bottom: 6px;
}

.stat-card h2 {
  margin: 0 0 6px;
}
.trend {
  font-size: 12px;
  color: #6b7280;
}

.trend.up {
  color: #16a34a;
}

.trend.down {
  color: #dc2626;
}
</style>
