<template>
  <div class="pagination" v-if="totalPages > 1">
    <button
      :disabled="modelValue === 1"
      @click="updatePage(modelValue - 1)"
      class="nav-btn"
    >
      Prev
    </button>

    <div class="page-numbers" v-if="mode === 'numbers'">
      <button
        v-for="page in totalPages"
        :key="page"
        :class="{ active: page === modelValue }"
        @click="updatePage(page)"
      >
        {{ page }}
      </button>
    </div>

    <span v-else class="page-info">
      Page {{ modelValue }} of {{ totalPages }}
    </span>

    <button
      :disabled="modelValue === totalPages"
      @click="updatePage(modelValue + 1)"
      class="nav-btn"
    >
      Next
    </button>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  },
  mode: {
    type: String,
    default: 'numbers'
  }
})

const emit = defineEmits(['update:modelValue'])

const updatePage = (newPage) => {
  emit('update:modelValue', newPage)
}
</script>

<style scoped>
.pagination {
  margin-top: 32px;
  display: flex;
  gap: 12px;
  justify-content: center;
  align-items: center;
}

.page-numbers {
  display: flex;
  gap: 8px;
}

button {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

button:hover:not(:disabled) {
  border-color: #5b3df5;
  color: #5b3df5;
}

button.active {
  background: #5b3df5;
  color: #fff;
  border-color: #5b3df5;
}

button:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.page-info {
  font-weight: 500;
  color: #444;
}
</style>
