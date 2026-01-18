<template>
  <div class="edit-profile-page">
    <h1>Edit Profile</h1>

    <div class="form-group avatar-group">
      <label>Avatar</label>
      <div class="avatar-preview" v-if="form.avatarPreview">
        <img :src="form.avatarPreview" alt="Avatar Preview" />
      </div>
      <input type="file" @change="onAvatarChange" accept="image/*" />
    </div>

    <form @submit.prevent="saveProfile">
      <div class="form-group">
        <label>About Me</label>
        <textarea v-model="form.about"></textarea>
      </div>

      <div class="form-group">
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

      <div class="form-group">
        <label>Skills (comma separated)</label>
        <input v-model="form.skills" type="text" />
      </div>

      <button type="submit">Save</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notificationStore'

// World cities data
const worldCities = [
  'New York',
  'Los Angeles',
  'Chicago',
  'Houston',
  'Phoenix',
  'Philadelphia',
  'San Antonio',
  'San Diego',
  'Dallas',
  'San Jose',
  'London',
  'Manchester',
  'Birmingham',
  'Leeds',
  'Glasgow',
  'Liverpool',
  'Sheffield',
  'Bristol',
  'Edinburgh',
  'Paris',
  'Marseille',
  'Lyon',
  'Toulouse',
  'Nice',
  'Nantes',
  'Strasbourg',
  'Montpellier',
  'Bordeaux',
  'Lille',
  'Berlin',
  'Munich',
  'Cologne',
  'Frankfurt',
  'Hamburg',
  'Dusseldorf',
  'Dortmund',
  'Essen',
  'Leipzig',
  'Dresden',
  'Madrid',
  'Barcelona',
  'Valencia',
  'Seville',
  'Bilbao',
  'Malaga',
  'Murcia',
  'Palma',
  'Las Palmas',
  'Alicante',
  'Rome',
  'Milan',
  'Naples',
  'Turin',
  'Palermo',
  'Genoa',
  'Bologna',
  'Florence',
  'Bari',
  'Catania',
  'Moscow',
  'Saint Petersburg',
  'Novosibirsk',
  'Yekaterinburg',
  'Nizhny Novgorod',
  'Kazan',
  'Chelyabinsk',
  'Omsk',
  'Samara',
  'Rostov-on-Don',
  'Tokyo',
  'Delhi',
  'Shanghai',
  'Mumbai',
  'Beijing',
  'Osaka',
  'Shenzhen',
  'Bangkok',
  'Hong Kong',
  'Jakarta',
  'Toronto',
  'Vancouver',
  'Mexico City',
  'Sao Paulo',
  'Rio de Janeiro',
  'Buenos Aires',
  'Salvador',
  'Brasilia',
  'Fortaleza',
  'Belo Horizonte',
  'Sydney',
  'Melbourne',
  'Brisbane',
  'Perth',
  'Adelaide',
  'Gold Coast',
  'Canberra',
  'Hobart',
  'Geelong',
  'Dubai',
  'Abu Dhabi',
  'Cairo',
  'Alexandria',
  'Johannesburg',
  'Cape Town',
  'Lagos',
  'Nitra',
  'Nairobi',
  'Istanbul',
  'Ankara',
  'Prague',
  'Budapest',
  'Warsaw',
  'Athens',
  'Bucharest',
  'Sofia',
  'Bratislava',
  'Ljubljana',
  'Zagreb',
  'Belgrade',
  'Amsterdam',
  'Rotterdam',
  'The Hague',
  'Utrecht',
  'Eindhoven',
  'Groningen',
  'Alkmaar',
  'Breda',
  'Arnhem',
  'Leiden',
  'Vienna',
  'Zurich',
  'Geneva',
  'Basel',
  'Bern',
  'Lausanne',
  'Lucerne',
  'St. Gallen',
  'Winterthur',
  'Schaffhausen',
  'Dublin',
  'Cork',
  'Limerick',
  'Galway',
  'Waterford',
  'Droichead Atha',
  'Swords',
  'Navan',
  'Dundalk',
  'Athlone',
  'Copenhagen',
  'Aarhus',
  'Odense',
  'Aalborg',
  'Esbjerg',
  'Randers',
  'Kolding',
  'Horsens',
  'Vejle',
  'Silkeborg',
  'Stockholm',
  'Gothenburg',
  'Malmo',
  'Uppsala',
  'Vasteras',
  'Orebro',
  'Linkoping',
  'Helsingborg',
  'Jonkoping',
  'Norrkoping',
  'Helsinki',
  'Espoo',
  'Tampere',
  'Vantaa',
  'Turku',
  'Oulu',
  'Jyvaskyla',
  'Kuopio',
  'Lahti',
  'Pori',
  'Oslo',
  'Bergen',
  'Trondheim',
  'Stavanger',
  'Kristiansand',
  'Drammen',
  'Fredrikstad',
  'Tromsø',
  'Sandefjord',
  'Lillehammer',
  'Astana',
  'Almaty',
  'Shymkent',
  'Karaganda',
  'Taraz',
  'Pavlodar',
  'Ust-Kamenogorsk',
  'Semey',
  'Aktau',
  'Atyrau',
  'Kostanay',
  'Kyzylorda',
  'Zhezkazgan',
  'Petropavl',
  'Taldykorgan',
  'Ekibastuz',
  'Ridder',
]

const router = useRouter()
const notifications = useNotificationStore()
const citySearch = ref('')
const showCitiesList = ref(false)
const filteredCities = computed(() => {
  if (!citySearch.value) return worldCities.slice(0, 10)
  return worldCities
    .filter((city) => city.toLowerCase().includes(citySearch.value.toLowerCase()))
    .slice(0, 20)
})

const form = ref({
  about: '',
  location: '',
  skills: '',
  avatar: null,
  avatarPreview: null,
})

const selectCity = (city) => {
  form.value.location = city
  citySearch.value = city
  showCitiesList.value = false
}

const filterCities = () => {
  showCitiesList.value = true
}

onMounted(async () => {
  try {
    const response = await api.get('/freelancer/profile', {
      headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
    })
    if (response.data) {
      form.value = {
        ...form.value,
        about: response.data.about || '',
        location: response.data.location || '',
        skills: response.data.skills?.join(', ') || '',
        avatarPreview: response.data.avatar_url || null,
      }
      citySearch.value = response.data.location || ''
    }
  } catch (error) {
    console.error(error)
    notifications.error('Failed to load profile')
  }
})

const onAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.avatar = file
    form.value.avatarPreview = URL.createObjectURL(file)
  }
}

const saveProfile = async () => {
  try {
    const formData = new FormData()
    formData.append('about', form.value.about || '')
    formData.append('location', form.value.location || '')

    const skills = form.value.skills
      .split(',')
      .map((s) => s.trim())
      .filter((s) => s.length > 0)

    formData.append('skills', JSON.stringify(skills))

    if (form.value.avatar) {
      formData.append('avatar', form.value.avatar)
    }

    const res = await api.post('/freelancer/profile', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
        'Content-Type': 'multipart/form-data',
      },
    })

    if (res.data.avatar_url) {
      form.value.avatarPreview = res.data.avatar_url
    }

    notifications.success('Profile saved successfully!')
    router.push('/freelancer-profile')
  } catch (error) {
    console.error('Full error response:', error.response?.data)
    const errors = error.response?.data?.errors || {}
    const errorMessage =
      Object.entries(errors)
        .map(([key, msgs]) => `${key}: ${msgs.join(', ')}`)
        .join('\n') || 'Failed to save profile. Check all fields.'
    notifications.error(errorMessage)
  }
}
</script>

<style scoped>
.edit-profile-page {
  max-width: 600px;
  margin: 40px auto;
  background: #f3efff;
  padding: 32px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.form-group {
  margin-bottom: 16px;
  display: flex;
  flex-direction: column;
}

input,
textarea {
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
}

button {
  padding: 12px 20px;
  background: #5b3df5;
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
}

.avatar-group img {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
}

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
  color: #5b3df5;
  font-weight: 500;
}
</style>
