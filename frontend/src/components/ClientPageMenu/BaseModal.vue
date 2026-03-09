<template>
  <div class="modal-backdrop" @click.self="$emit('close')">
    <div class="modal-card" :class="type">
      <h3>{{ title }}</h3>

      <div class="modal-body">
        <slot></slot>
      </div>

      <div class="modal-actions">
        <slot name="actions"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: ''
  }
})

defineEmits(['close'])
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-card {
  background: #fff;
  padding: 24px;
  border-radius: 16px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-card.success h3 { color: #16a34a; }
.modal-card.error h3 { color: #ef4444; }

h3 { margin-bottom: 8px; }

.modal-body {
  color: #666;
  margin-bottom: 20px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

@media (max-width: 768px) {
  .modal-card {
    width: 90%;
    margin: 0 10px;
  }
}
</style>
