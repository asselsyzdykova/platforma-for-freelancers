<template>
  <div class="form-group avatar-group">
    <label>Avatar</label>
    <div class="avatar-preview" v-if="avatarPreview">
      <img :src="avatarPreview" alt="Avatar Preview" />
    </div>
    <input type="file" @change="onFileChange" accept="image/*" />
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const { avatarPreview } = defineProps({
  avatarPreview: String,
})

const emits = defineEmits(['update:avatar'])

const onFileChange = (event) => {
  const file = event.target.files[0]
  if (file) emits('update:avatar', file)
}
</script>

<style scoped>
.avatar-group img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
}
</style>
