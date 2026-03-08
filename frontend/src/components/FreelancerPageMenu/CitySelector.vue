<template>
  <div class="form-group city-selector">
    <input
      type="text"
      v-model="search"
      placeholder="Search city..."
      @focus="showList = true"
      @input="filterCities"
    />
    <div v-if="showList" class="cities-dropdown">
      <div
        v-for="city in filteredCities"
        :key="city"
        class="city-option"
        @click="selectCity(city)"
      >
        {{ city }}
      </div>
      <div v-if="filteredCities.length === 0" class="no-results">No cities found</div>
    </div>
    <div v-if="selectedCity" class="selected-city">Selected: {{ selectedCity }}</div>
  </div>
</template>

<script setup>
import { ref, computed, watch, defineProps, defineEmits } from 'vue'

const props = defineProps({
  cities: { type: Array, default: () => [] },
  modelValue: String
})
const emits = defineEmits(['update:selectedCity'])

const search = ref(props.modelValue || '')
const showList = ref(false)
const selectedCity = ref(props.modelValue || '')

watch(() => props.modelValue, (val) => {
  selectedCity.value = val
  search.value = val || ''
})

const filteredCities = computed(() => {
  if (!search.value) return props.cities.slice(0, 10)
  return props.cities
    .filter(c => c.toLowerCase().includes(search.value.toLowerCase()))
    .slice(0, 20)
})

const selectCity = (city) => {
  selectedCity.value = city
  search.value = city
  showList.value = false
  emits('update:selectedCity', city)
}

const filterCities = () => {
  showList.value = true
}
</script>

<style scoped>
.city-selector {
  position: relative;
}
.city-selector input {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
  box-sizing: border-box;
}
.cities-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 10px 10px;
  max-height: 200px;
  overflow-y: auto;
  z-index: 60;
}
.city-option {
  padding: 10px;
  cursor: pointer;
  border-bottom: 1px solid #f0f0f0;
  transition: background-color 0.2s;
}
.city-option:hover {
  background-color: #f3efff;
}
.no-results {
  padding: 10px;
  text-align: center;
  color: #999;
}
.selected-city {
  margin-top: 8px;
  font-size: 14px;
  color: #5b3df5;
  font-weight: 500;
}
</style>
