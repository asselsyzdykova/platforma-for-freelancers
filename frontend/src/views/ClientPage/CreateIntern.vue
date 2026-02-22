<template>
  <div class="create-project">
    <h1>Create New Internship</h1>
    <p class="subtitle">Publish an internship offer</p>
    <form class="card" @submit.prevent="createIntern">
      <div class="field">
        <label>Internship title</label>
        <input v-model="intern.title" type="text" required />
      </div>

      <div class="field">
        <label>Company Name</label>
        <input v-model="intern.company" type="text" required />
      </div>

      <div class="field">
        <label>Description</label>
        <textarea v-model="intern.description" rows="4" required></textarea>
      </div>

      <div class="field">
        <label>Monthly Stipend</label>
        <div class="stipend-options">
          <label class="radio-option">
            <input v-model="intern.stipendType" type="radio" value="paid" />
            <span>Paid</span>
          </label>
          <label class="radio-option">
            <input v-model="intern.stipendType" type="radio" value="unpaid" />
            <span>Unpaid</span>
          </label>
        </div>
        <input v-if="intern.stipendType === 'paid'" v-model="intern.price" type="text" placeholder="€500 – €1000"
          required />
      </div>

      <div class="field">
        <label>Duration</label>
        <input v-model="intern.time" type="text" placeholder="e.g. 3 months" required />
      </div>

      <div class="field">
        <label>Number of Positions</label>
        <input v-model="intern.number" type="number" placeholder="e.g. 2" required />
      </div>

      <div class="actions">
        <button type="submit" class="primary">Publish Internship</button>
        <RouterLink :to="{ name: 'ClientProfile' }" exact-active-class="active" class="secondary">Cancel</RouterLink>
      </div>
    </form>
  </div>
</template>
<script setup>
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'

const router = useRouter()
const notifications = useNotificationStore()

const intern = reactive({
  title: '',
  company: '',
  description: '',
  stipendType: 'paid',
  price: '',
  time: '',
  number: '',
})

const createIntern = async () => {
  try {
    await api.post('/internships', intern)
    notifications.success('Internship created successfully!')
    router.push({ name: 'ClientProfile' })
  } catch (error) {
    console.error(error.response?.data || error)
    notifications.error('Failed to create internship.')
  }
}
</script>
<style scoped>
.create-project {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 24px;
}

.subtitle {
  color: #666;
  margin-bottom: 32px;
}

.card {
  background: #f3efff;
  border-radius: 20px;
  padding: 32px;
}

.field {
  margin-bottom: 20px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
}

input,
textarea,
select {
  width: 100%;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid #ddd;
}

.tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 10px;
}

.tag {
  background: #e6e0ff;
  padding: 6px 10px;
  border-radius: 12px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.tag button {
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 14px;
}

.actions {
  display: flex;
  gap: 12px;
  margin-top: 30px;
}

.primary {
  flex: 1;
  background: #5b3df5;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.secondary {
  flex: 1;
  background: #eee;
  border: none;
  padding: 12px;
  border-radius: 14px;
  cursor: pointer;
  text-align: center;
}

.stipend-options {
  display: flex;
  gap: 20px;
  margin-bottom: 12px;
}

.radio-option {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
}

.radio-option input[type="radio"] {
  width: auto;
  cursor: pointer;
}
</style>
