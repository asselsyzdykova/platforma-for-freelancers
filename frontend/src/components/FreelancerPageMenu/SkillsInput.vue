<template>
  <div class="form-group skills-input-wrapper">
    <div class="skills-input">
      <input
        v-model="input"
        type="text"
        placeholder="Add a skill"
        @input="onInput"
        @keydown.enter.prevent="addSkill()"
      />
      <button type="button" @click="addSkill()">Add</button>
    </div>

    <div v-if="showDropdown && filteredSkills.length" class="cities-dropdown">
      <div
        v-for="skill in filteredSkills"
        :key="skill"
        class="city-option"
        @click="addSkill(skill)"
      >
        {{ skill }}
      </div>
    </div>

    <div v-if="skills.length" class="skills-list">
      <span v-for="skill in skills" :key="skill" class="skill-chip">
        {{ skill }}
        <button type="button" class="remove-skill" @click="$emit('removeSkill', skill)">×</button>
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue'

const props = defineProps({
  skills: { type: Array, default: () => [] },
  allSkills: { type: Array, default: () => [] },
})

const emits = defineEmits(['addSkill', 'removeSkill'])

const input = ref('')
const showDropdown = ref(false)

const filteredSkills = computed(() => {
  if (!input.value) return []
  return props.allSkills
    .filter(skill => skill.toLowerCase().includes(input.value.toLowerCase()))
    .filter(skill => !props.skills.includes(skill))
    .slice(0, 10)
})

const addSkill = (value = null) => {
  const skill = value || input.value.trim()
  if (!skill) return
  emits('addSkill', skill)
  input.value = ''
  showDropdown.value = false
}

const onInput = () => {
  showDropdown.value = true
}
</script>

<style scoped>
.skills-input {
  display: flex;
  gap: 10px;
}
.skills-input input {
  flex: 1;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
}
.skills-input button {
  padding: 10px 16px;
  background: #5b3df5;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
}
.skills-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 4px;
}
.skill-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  background: #efeaff;
  color: #3d2db3;
  border-radius: 999px;
  font-size: 13px;
}
.remove-skill {
  background: transparent;
  border: none;
  color: #3d2db3;
  cursor: pointer;
  font-size: 14px;
  line-height: 1;
  padding: 0;
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
</style>
