<template>
  <div class="edit-profile-page">
    <div class="card">
      <h1>Edit Client Profile</h1>

      <form @submit.prevent="saveProfile">
        <div class="avatar-block">
          <img
            v-if="previewAvatar || form.avatar_url"
            :src="previewAvatar || form.avatar_url"
            class="avatar"
          />
          <input type="file" @change="onAvatarChange" />
        </div>

        <div class="field">
          <label>Name</label>
          <input v-model="form.name" type="text" required />
        </div>

        <div class="field">
          <label>Company</label>
          <input v-model="form.company" type="text" />
        </div>

        <div class="field">
          <label>Location</label>
          <div class="city-selector">
            <input
              v-model="citySearch"
              type="text"
              placeholder="Search city..."
              @focus="showCitiesList = true"
              @input="filterCities"
            />
            <div v-if="showCitiesList" class="cities-dropdown">
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
          </div>
          <div v-if="form.location" class="selected-city">Selected: {{ form.location }}</div>
        </div>

        <div class="field">
          <label>About</label>
          <textarea v-model="form.about" rows="4"></textarea>
        </div>

        <div class="actions">
          <button type="submit" class="primary">Save changes</button>
          <button type="button" class="secondary" @click="cancel">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notificationStore'

const router = useRouter()
const notifications = useNotificationStore()
const cities = ref([])
const citySearch = ref('')
const showCitiesList = ref(false)
const filteredCities = computed(() => {
  if (!citySearch.value) return cities.value.slice(0, 10)
  return cities.value
    .filter((city) => city.toLowerCase().includes(citySearch.value.toLowerCase()))
    .slice(0, 20)
})

const form = ref({
  name: '',
  company: '',
  location: '',
  about: '',
  avatar_url: null,
})
const previewAvatar = ref(null)
let avatarFile = null

onMounted(async () => {
  try {
    const [profileResponse, citiesResponse] = await Promise.all([
      api.get('/client/profile'),
      api.get('/cities'),
    ])

    if (Array.isArray(citiesResponse.data)) {
      cities.value = citiesResponse.data
    }

    if (profileResponse.data) {
      form.value = {
        ...form.value,
        ...profileResponse.data,
      }
      previewAvatar.value = profileResponse.data.avatar_url || null
      citySearch.value = profileResponse.data.location || ''
    }
  } catch (e) {
    console.error('Failed to load client profile', e)
  }
})

const selectCity = (city) => {
  form.value.location = city
  citySearch.value = city
  showCitiesList.value = false
}

const filterCities = () => {
  showCitiesList.value = true
}

const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  avatarFile = file
  previewAvatar.value = URL.createObjectURL(file)
}

const saveProfile = async () => {
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('company', form.value.company || '')
    formData.append('location', form.value.location || '')
    formData.append('about', form.value.about || '')

    if (avatarFile) formData.append('avatar', avatarFile)

    const res = await api.post('/client/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (res.data.avatar_url) previewAvatar.value = res.data.avatar_url

    notifications.success('Profile saved successfully!')
    router.push('/client-profile')
  } catch (e) {
    console.error('Failed to save client profile', e.response?.data || e)
    notifications.error('Failed to save profile. Check all fields.')
  }
}

const cancel = () => {
  router.push('/client-profile')
}
</script>

<style scoped>
.edit-profile-page {
  display: flex;
  justify-content: center;
  padding: 40px 20px;
}

.card {
  width: 100%;
  max-width: 520px;
  background: #fff;
  padding: 30px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

h1 {
  margin-bottom: 25px;
  text-align: center;
}

.avatar-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 25px;
}

.avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
}

.field {
  margin-bottom: 16px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
}

input,
textarea {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.city-selector {
  position: relative;
}

.city-selector input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
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
  max-height: 250px;
  overflow-y: auto;
  z-index: 10;
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
  color: #5b4b8a;
  font-weight: 500;
}

.actions {
  display: flex;
  gap: 12px;
  margin-top: 25px;
}

.primary {
  flex: 1;
  background: #5b4b8a;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
}

.secondary {
  flex: 1;
  background: #eee;
  border: none;
  padding: 12px;
  border-radius: 10px;
  cursor: pointer;
}
</style>
