<template>
  <div class="create-project">
    <h1>{{ isEdit ? 'Edit Project' : 'Create New Project' }}</h1>
    <p class="subtitle">{{ isEdit ? 'Update your project details' : 'Publish your project and find the right freelancer'
      }}</p>

    <form class="card" @submit.prevent="submitProject">
      <div class="field">
        <label>Project title</label>
        <input v-model="project.title" type="text" required />
      </div>

      <div class="field">
        <label>Description</label>
        <textarea v-model="project.description" rows="4" required></textarea>
      </div>

      <div class="field">
        <label>Budget</label>
        <input v-model="project.budget" type="text" placeholder="$500 – $1000" required />
      </div>

      <div class="field">
        <select v-model="project.category" required>
          <option disabled value="">Select category</option>
          <option v-for="category in categories" :key="category">
            {{ category }}
          </option>
        </select>
      </div>

      <div class="field" v-if="project.category === 'Other'">
        <label>Specify category</label>
        <input v-model="project.otherCategory" type="text" placeholder="Type category" />
      </div>

      <div class="field">
        <label>Skills / Tags</label>
        <input v-model="tagInput" @keydown.enter.prevent="addTag" placeholder="Press Enter to add tag" />
        <div class="tags">
          <span class="tag" v-for="(tag, index) in project.tags" :key="index">
            {{ tag }}
            <button type="button" @click="removeTag(index)">×</button>
          </span>
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="primary">{{ isEdit ? 'Update Project' : 'Publish Project' }}</button>
        <RouterLink :to="{ name: 'ClientProfile' }" exact-active-class="active" class="secondary">Cancel</RouterLink>
      </div>
    </form>
  </div>
</template>

<script>
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'
import { categories } from '@/constants/categories'

export default {
  name: 'CreateProject',

  data() {
    return {
      project: {
        title: '',
        description: '',
        budget: '',
        category: '',
        otherCategory: '',
        tags: [],
      },
      tagInput: '',
      notifications: useNotificationStore(),
      categories,
      isEdit: false,
      editId: null,
    }
  },

  mounted() {
    const editId = this.$route.query.editId
    if (editId) {
      this.editId = editId
      this.isEdit = true
      this.loadProject(editId)
    }
  },
  methods: {
    addTag() {
      if (!this.tagInput.trim()) return
      this.project.tags.push(this.tagInput.trim())
      this.tagInput = ''
    },

    removeTag(index) {
      this.project.tags.splice(index, 1)
    },

    async loadProject(id) {
      try {
        const res = await api.get(`/client/projects/${id}`)
        const p = res.data

        this.project.title = p.title || ''
        this.project.description = p.description || ''
        this.project.budget = p.budget || ''
        this.project.category = categories.includes(p.category) ? p.category : 'Other'
        this.project.otherCategory = categories.includes(p.category) ? '' : p.category
        this.project.tags = Array.isArray(p.tags) ? p.tags : []
      } catch (e) {
        console.error('Failed to load project for edit', e)
        this.notifications.error('Failed to load project data')
      }
    },

    async submitProject() {
      try {
        if (this.project.category === 'Other' && !this.project.otherCategory.trim()) {
          this.notifications.error('Please specify the category.')
          return
        }

        const payload = {
          title: this.project.title,
          description: this.project.description,
          budget: this.project.budget,
          category: this.project.category === 'Other' ? this.project.otherCategory.trim() : this.project.category,
          tags: this.project.tags,
        }

        if (this.isEdit && this.editId) {
          await api.patch(`/client/projects/${this.editId}`, payload)
          this.notifications.success('Project updated successfully!')
        } else {
          await api.post('/client/projects', payload)
          this.notifications.success('Project created successfully!')
        }

        window.dispatchEvent(new Event('projectCreated'))
        this.$router.push({ name: 'ClientProfile' })
      } catch (error) {
        console.error(error.response?.data || error)
        this.notifications.error(this.isEdit ? 'Failed to update project.' : 'Failed to create project.')
      }
    },
  },
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
  pointer-events: auto;
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
</style>
