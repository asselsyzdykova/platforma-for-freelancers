<template>
  <div class="edit-profile-page">
    <h1>Edit Profile</h1>

    <AvatarUpload :avatarPreview="form.avatarPreview" @update:avatar="form.avatar = $event" />

    <form @submit.prevent="saveProfile">
      <div class="form-group">
        <label>About Me</label>
        <textarea v-model="form.about"></textarea>
      </div>

      <CitySelector :cities="cities" v-model:selectedCity="form.location" />

      <SkillsInput :skills="form.skills" :allSkills="allSkills" @addSkill="addSkill" @removeSkill="removeSkill" />

      <CertificatesUpload :certificates="form.certificates" :new-certificates="form.newCertificates"
        @add-certificate="handleAddCertificates" @remove-existing-certificate="removeExistingCertificate"
        @remove-new-certificate="removeNewCertificate" />

      <button type="submit">Save</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/axios'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notificationStore'
import AvatarUpload from '@/components/FreelancerPageMenu/AvatarUpload.vue'
import CitySelector from '@/components/FreelancerPageMenu/CitySelector.vue'
import SkillsInput from '@/components/FreelancerPageMenu/SkillsInput.vue'
import CertificatesUpload from '@/components/FreelancerPageMenu/CertificatesUpload.vue'

const router = useRouter()
const notifications = useNotificationStore()

const cities = ref([])
const allSkills = ref([])

const form = ref({
  about: '',
  location: '',
  skills: [],
  avatar: null,
  avatarPreview: null,
  certificates: [],
  newCertificates: [],
})

const addSkill = (skillFromDropdown = null) => {
  const value = skillFromDropdown || ''
  if (!value.trim()) return

  const trimmed = value.trim()
  if (!form.value.skills.includes(trimmed)) {
    form.value.skills.push(trimmed)
  }
}

const removeSkill = (skill) => {
  form.value.skills = form.value.skills.filter(item => item !== skill)
}

const handleAddCertificates = (files) => {
  const maxSize = 4 * 1024 * 1024
  const valid = files.filter(file => {
    if (file.size > maxSize) {
      notifications.error(`File "${file.name}" is too large (max 4 MB)`)
      return false
    }
    return true
  })

  if (valid.length) {
    form.value.newCertificates.push(...valid)
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
    formData.append('certificates_existing', JSON.stringify(form.value.certificates))
    form.value.newCertificates.forEach(file => {
      formData.append('certificates[]', file)
    })

    const res = await api.post('/freelancer/profile', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`,
        'Content-Type': 'multipart/form-data',
      },
    })

    if (res.data?.avatar_url) {
      form.value.avatarPreview = res.data.avatar_url
    }

    notifications.success('Profile saved successfully!')
    router.push('/freelancer-profile')
  } catch (error) {
    console.error(error.response?.data)

    const errors = error.response?.data?.errors || {}
    const msg = Object.entries(errors)
      .map(([k, v]) => `${k}: ${v.join(', ')}`)
      .join('\n') || 'Failed to save profile'

    notifications.error(msg)
  }
}

onMounted(async () => {
  try {
    const [profileRes, citiesRes, skillsRes] = await Promise.all([
      api.get('/freelancer/profile', {
        headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` },
      }),
      api.get('/cities'),
      api.get('/skills'),
    ])

    cities.value = Array.isArray(citiesRes.data) ? citiesRes.data : []
    allSkills.value = Array.isArray(skillsRes.data) ? skillsRes.data : []

    const data = profileRes.data || {}
    form.value.about = data.about || ''
    form.value.location = data.location || ''
    form.value.skills = Array.isArray(data.skills) ? data.skills : []
    form.value.certificates = Array.isArray(data.certificate_urls) ? data.certificate_urls : data.certificates || []
    form.value.avatarPreview = data.avatar_url || null

  } catch (error) {
    console.error(error)
    notifications.error('Failed to load profile data')
  }
})
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
