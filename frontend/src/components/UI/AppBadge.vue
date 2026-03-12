<template>
  <span :class="['badge-icon', type]" :title="tooltipText">
    <template v-if="type === 'pro'">
      <span class="pro-text">PRO</span>
    </template>
    <template v-else-if="type === 'verified'">
      <svg class="check-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"
          fill="currentColor" />
      </svg>
    </template>
  </span>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    required: true,
  },
  tooltipText: String
});
</script>

<style scoped>
.badge-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  vertical-align: middle;
  margin-left: 6px;
}

.pro {
  background: linear-gradient(135deg, #5D3A9B 0%, #7b4fc3 100%);
  color: white;
  font-size: 10px;
  font-weight: 900;
  padding: 2px 6px;
  border-radius: 4px;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 4px rgba(93, 58, 155, 0.2);
}

.verified {
  color: #1DA1F2;
  width: 18px;
  height: 18px;
}

.check-svg {
  width: 100%;
  height: 100%;
}
</style>
