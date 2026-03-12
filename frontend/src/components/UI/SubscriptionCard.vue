<template>
  <div class="plan-card" :class="{ 'highlighted': isHighlighted }">
    <div v-if="isHighlighted" class="badge">Most Popular</div>

    <h2 class="plan-name">{{ name }}</h2>
    <div class="price-container">
      <span class="currency">€</span>
      <span class="amount">{{ price }}</span>
      <span class="period">/ month</span>
    </div>

    <ul class="features-list">
      <li v-for="(feature, index) in features" :key="index" :class="{ 'inactive': feature.disabled }">
        <span class="icon">{{ feature.disabled ? '✕' : '✔' }}</span>
        <span class="text">{{ feature.text }}</span>
      </li>
    </ul>

    <button class="btn" :class="buttonClass" :disabled="disabled || loading" @click="$emit('subscribe')">
      <span v-if="loading" class="loader"></span>
      <span v-else>{{ buttonLabel }}</span>
    </button>
  </div>
</template>

<script setup>
defineProps({
  name: String,
  price: [String, Number],
  features: Array,
  isHighlighted: Boolean,
  buttonLabel: String,
  buttonClass: String,
  disabled: Boolean,
  loading: Boolean
});

defineEmits(['subscribe']);
</script>

<style scoped>
.plan-card {
  background: #fff;
  border: 1px solid #eef0f2;
  border-radius: 20px;
  padding: 32px 24px;
  display: flex;
  flex-direction: column;
  position: relative;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  height: 100%;
  max-width: 340px;
  margin: 0 auto;
  width: 100%;
}

.highlighted {
  border: 2px solid #5D3A9B;
  box-shadow:
    0 10px 15px -3px rgba(93, 58, 155, 0.1),
    0 20px 25px -5px rgba(93, 58, 155, 0.2),
    0 0 15px rgba(93, 58, 155, 0.05);
  transform: scale(1.05);
  z-index: 2;
}

.badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, #5D3A9B 0%, #7b4fc3 100%);
  color: #fff;
  padding: 5px 16px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  box-shadow: 0 4px 10px rgba(93, 58, 155, 0.3);
}

.highlighted:hover {
  transform: translateY(-10px) scale(1.07);
  box-shadow:
    0 20px 25px -5px rgba(93, 58, 155, 0.2),
    0 30px 35px -10px rgba(93, 58, 155, 0.3);
}

.plan-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
}

.amount {
  font-size: 36px;
  font-weight: 800;
  color: #1a1a1a;
}

.plan-name {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 8px;
}

.features-list {
  list-style: none;
  padding: 0;
  margin: 24px 0 32px 0;
  flex-grow: 1;
}

.features-list li {
  gap: 10px;
  margin-bottom: 12px;
  font-size: 14px;
}

.btn {
  width: 100%;
  padding: 16px;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: 0.2s;
}

.btn.primary {
  background: #5D3A9B;
  color: #fff;
}

.btn.primary:hover {
  background: #4a2e7c;
  transform: scale(1.02);
}
.btn.disabled {
  background: #f4f4f7;
  color: #94a3b8;
  cursor: not-allowed;
}
.spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  display: inline-block;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
