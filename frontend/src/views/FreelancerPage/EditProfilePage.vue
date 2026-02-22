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
          <input v-model="citySearch" type="text" placeholder="Search city..." @focus="showCitiesList = true"
            @input="filterCities" />
          <div v-if="showCitiesList" class="cities-dropdown">
            <div v-for="city in filteredCities" :key="city" class="city-option" @click="selectCity(city)">
              {{ city }}
            </div>
            <div v-if="filteredCities.length === 0" class="no-results">No cities found</div>
          </div>
        </div>
        <div v-if="form.location" class="selected-city">Selected: {{ form.location }}</div>
      </div>

      <div class="form-group">
        <label>Skills</label>

        <div class="skills-input">
          <input v-model="skillsInput" type="text" placeholder="Add a skill" @input="onSkillInput"
            @keydown.enter.prevent="addSkill()" />
          <button type="button" @click="addSkill()">Add</button>
        </div>

        <div v-if="showSkillsDropdown && filteredSkills.length" class="cities-dropdown">
          <div v-for="skill in filteredSkills" :key="skill" class="city-option" @click="addSkill(skill)">
            {{ skill }}
          </div>
        </div>

        <div v-if="form.skills.length" class="skills-list">
          <span v-for="skill in form.skills" :key="skill" class="skill-chip">
            {{ skill }}
            <button type="button" class="remove-skill" @click="removeSkill(skill)">
              ×
            </button>
          </span>
        </div>
      </div>


      <div class="form-group certificates-group">
        <label>Certificates</label>
        <input type="file" multiple @change="onCertificatesChange" accept="image/*" />
        <div class="certificates-list">
          <div v-for="(cert, index) in form.certificates" :key="index" class="certificate-item">
            <span>{{ cert }}</span>
            <button type="button" class="remove-skill" @click="removeExistingCertificate(index)">
              ×
            </button>
          </div>
          <div v-for="(cert, index) in form.newCertificates" :key="`new-${index}`" class="certificate-item">
            <span>{{ cert.name }}</span>
            <button type="button" class="remove-skill" @click="removeNewCertificate(index)">
              ×
            </button>
          </div>
        </div>
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

const router = useRouter()
const notifications = useNotificationStore()

const cities = ref([])
const citySearch = ref('')
const showCitiesList = ref(false)

const skillsInput = ref('')
const allSkills = ref([])
const showSkillsDropdown = ref(false)

const filteredCities = computed(() => {
  if (!citySearch.value) return cities.value.slice(0, 10)
  return cities.value
    .filter((city) =>
      city.toLowerCase().includes(citySearch.value.toLowerCase())
    )
    .slice(0, 20)
})

const filteredSkills = computed(() => {
  if (!skillsInput.value) return []

  return allSkills.value
    .filter((skill) =>
      skill.toLowerCase().includes(skillsInput.value.toLowerCase())
    )
    .filter((skill) => !form.value.skills.includes(skill))
    .slice(0, 10)
})

const form = ref({
  about: '',
  location: '',
  skills: [],
  avatar: null,
  avatarPreview: null,
  certificates: [],
  newCertificates: [],
})

const maxCertificateSizeBytes = 4 * 1024 * 1024

const selectCity = (city) => {
  form.value.location = city
  citySearch.value = city
  showCitiesList.value = false
}

const filterCities = () => {
  showCitiesList.value = true
}


const addSkill = (skillFromDropdown = null) => {
  const value = skillFromDropdown || skillsInput.value.trim()
  if (!value) return

  if (!form.value.skills.includes(value)) {
    form.value.skills.push(value)
  }

  skillsInput.value = ''
  showSkillsDropdown.value = false
}

const removeSkill = (skill) => {
  form.value.skills = form.value.skills.filter(
    (item) => item !== skill
  )
}

const onSkillInput = () => {
  showSkillsDropdown.value = true
}


onMounted(async () => {
  try {
    const [profileResponse, citiesResponse, skillsResponse] =
      await Promise.all([
        api.get('/freelancer/profile', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token')}`,
          },
        }),
        api.get('/cities'),
        api.get('/skills'),
      ])

    if (Array.isArray(citiesResponse.data)) {
      cities.value = citiesResponse.data
    }

    if (Array.isArray(skillsResponse.data)) {
      allSkills.value = skillsResponse.data
    }

    if (profileResponse.data) {
      form.value = {
        ...form.value,
        about: profileResponse.data.about || '',
        location: profileResponse.data.location || '',
        skills: profileResponse.data.skills || [],
        avatarPreview: profileResponse.data.avatar_url || null,
        certificates: profileResponse.data.certificates || [],
        newCertificates: [],
      }

      citySearch.value = profileResponse.data.location || ''
    }
  } catch (error) {
    console.error(error)
    notifications.error('Failed to load profile data')
  }
})


const onAvatarChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.value.avatar = file
    form.value.avatarPreview = URL.createObjectURL(file)
  }
}

const onCertificatesChange = (event) => {
  const files = Array.from(event.target.files || [])
  if (!files.length) return

  const accepted = []

  files.forEach((file) => {
    if (file.size > maxCertificateSizeBytes) {
      notifications.error(
        'Certificate is too large. Maximum size is 4096 KB.'
      )
      return
    }
    accepted.push(file)
  })

  if (accepted.length) {
    form.value.newCertificates = [
      ...form.value.newCertificates,
      ...accepted,
    ]
  }
}

const removeExistingCertificate = (index) => {
  form.value.certificates.splice(index, 1)
}

const removeNewCertificate = (index) => {
  form.value.newCertificates.splice(index, 1)
}


const saveProfile = async () => {
  try {
    const formData = new FormData()

    formData.append('about', form.value.about || '')
    formData.append('location', form.value.location || '')
    formData.append('skills', JSON.stringify(form.value.skills))

    if (form.value.avatar) {
      formData.append('avatar', form.value.avatar)
    }

    formData.append(
      'certificates_existing',
      JSON.stringify(form.value.certificates)
    )

    if (form.value.newCertificates.length) {
      form.value.newCertificates.forEach((file) => {
        formData.append('certificates[]', file)
      })
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
    console.error(error.response?.data)

    const errors = error.response?.data?.errors || {}
    const errorMessage =
      Object.entries(errors)
        .map(([key, msgs]) => `${key}: ${msgs.join(', ')}`)
        .join('\n') ||
      'Failed to save profile. Check all fields.'

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
  position: relative;
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

.skills-input {
  display: flex;
  gap: 10px;
  margin-bottom: 8px;
  position: relative;
}

.skills-input input {
  flex: 1;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
  box-sizing: border-box;
}

.skills-input button {
  padding: 10px 16px;
  background: #5b3df5;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
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

.suggestions-box {
  position: absolute;
  left: 0;
  top: calc(100% + 8px);
  width: 100%;
  border: 1px solid #e6e6f2;
  background: #fff;
  border-radius: 8px;
  max-height: 220px;
  overflow: auto;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
  z-index: 40;
}

.suggestion {
  padding: 10px 14px;
  cursor: pointer;
  border-bottom: 1px solid #f3efff;
}

.suggestion:hover {
  background: #f3efff
}

.no-suggestion {
  padding: 10px;
  color: #999
}
</style>
