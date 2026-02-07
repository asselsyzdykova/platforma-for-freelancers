<template>
  <div class="projects">
    <h1>Projects</h1>
    <p class="subtitle">Browse available projects and find work that matches your skills</p>

    <div class="project-grid">
      <div class="project-card" v-for="project in projects" :key="project.id">
        <h3>{{ project.title }}</h3>
        <p class="description">{{ project.description }}</p>

        <div class="client" v-if="project.client">
          <img v-if="project.client.avatar_url" :src="project.client.avatar_url" class="avatar" />
          <span>{{ project.client.name }}</span>
        </div>

        <div class="meta">
          <span class="budget">💰 {{ project.budget }} €</span>
          <span class="category">{{ project.category }}</span>
        </div>

        <div class="tags" v-if="project.tags?.length">
          <span class="tag" v-for="tag in project.tags" :key="tag">
            {{ tag }}
          </span>
        </div>

        <button class="btn" @click="respondToJob(project)">Response to a job</button>
      </div>
    </div>

    <div class="pagination" v-if="totalPages > 1">
      <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
      <button
        v-for="page in totalPages"
        :key="page"
        :class="{ active: page === currentPage }"
        @click="currentPage = page"
      >
        {{ page }}
      </button>
      <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
    </div>

    <div v-if="showProposalModal" class="modal-backdrop" @click.self="closeProposalModal">
      <div class="modal">
        <div class="modal-header">
          <div>
            <h2>Send Proposal</h2>
            <p class="modal-subtitle">
              {{ selectedProject?.title }} · Budget {{ selectedProject?.budget }} €
            </p>
          </div>
          <button class="icon-btn" @click="closeProposalModal">✕</button>
        </div>

        <div class="modal-body">
          <label class="field">
            Message
            <textarea
              v-model="proposalForm.message"
              rows="4"
              placeholder="Write a short message to the client"
            ></textarea>
          </label>

          <label class="field">
            Proposed budget (€)
            <input
              v-model="proposalForm.budget"
              type="number"
              min="1"
              step="1"
              placeholder="e.g. 500"
            />
          </label>
        </div>

        <div class="modal-actions">
          <button class="btn-secondary" @click="closeProposalModal">Cancel</button>
          <button class="btn-primary" @click="submitProposal">Send proposal</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'

export default {
  name: 'ProjectsPage',

  data() {
    return {
      projects: [],
      notifications: useNotificationStore(),
      showProposalModal: false,
      selectedProject: null,
      currentPage: 1,
      totalPages: 1,
      pageSize: 8,
      proposalForm: {
        message: '',
        budget: '',
      },
    }
  },

  mounted() {
    this.loadProjects()
  },

  methods: {
    async loadProjects() {
      try {
        const res = await api.get('/projects', {
          params: { page: this.currentPage, per_page: this.pageSize },
        })
        this.projects = res.data?.data || []
        this.totalPages = res.data?.meta?.last_page || 1
      } catch (e) {
        console.error('Failed to load projects', e)
        this.projects = []
        this.totalPages = 1
      }
    },

    async respondToJob(project) {
      this.selectedProject = project
      this.proposalForm = {
        message: '',
        budget: project?.budget || '',
      }
      this.showProposalModal = true
    },

    closeProposalModal() {
      this.showProposalModal = false
      this.selectedProject = null
    },

    async submitProposal() {
      try {
        const message = this.proposalForm.message
        const budget = Number(this.proposalForm.budget)

        if (!message || message.trim() === '') {
          this.notifications.warning('Message cannot be empty.')
          return
        }
        if (isNaN(budget) || budget <= 0) {
          this.notifications.warning('Please enter a valid number for budget.')
          return
        }

        if (!this.selectedProject) {
          this.notifications.error('Project not selected.')
          return
        }

        await api.post(`/projects/${this.selectedProject.id}/apply`, {
          message: message.trim(),
          budget: budget,
        })

        this.notifications.success('Response sent! The client will receive a notification.')
        this.closeProposalModal()
      } catch (e) {
        console.error('Failed to send response', e)

        if (e.response && e.response.status === 403) {
          this.notifications.warning(e.response.data?.message || 'Upgrade required to apply.')
        } else if (e.response && e.response.status === 422) {
          const errors = e.response.data.errors
          this.notifications.error(
            'Failed to send response:\n' +
              Object.entries(errors)
                .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                .join('\n'),
          )
        } else {
          this.notifications.error('Failed to send response. See console for details.')
        }
      }
    },
  },

  watch: {
    currentPage() {
      this.loadProjects()
    },
  },
}
</script>

<style scoped>
.projects {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 24px;
}

.subtitle {
  color: #666;
  margin-bottom: 32px;
}

.project-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
}

.project-card {
  background: #f3efff;
  border-radius: 20px;
  padding: 24px;
}

.project-card h3 {
  margin-bottom: 8px;
}

.description {
  color: #555;
  margin-bottom: 16px;
}

.client {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  font-size: 14px;
  color: #444;
}

.avatar {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  object-fit: cover;
}

.meta {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 14px;
}

.tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.tag {
  background: #e6e0ff;
  padding: 6px 12px;
  border-radius: 12px;
  font-size: 13px;
}

.btn {
  background: #5b3df5;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 12px;
  cursor: pointer;
}

.btn:hover {
  opacity: 0.9;
}

.pagination {
  margin-top: 32px;
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.pagination button {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}

.pagination button.active {
  background: #5b3df5;
  color: #fff;
  border-color: #5b3df5;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 15, 25, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  z-index: 2000;
}

.modal {
  width: 100%;
  max-width: 520px;
  background: #fff;
  border-radius: 18px;
  padding: 22px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 16px;
}

.modal-header h2 {
  margin: 0 0 4px;
}

.modal-subtitle {
  margin: 0;
  color: #6b7280;
  font-size: 14px;
}

.icon-btn {
  border: none;
  background: #f3f4f6;
  border-radius: 10px;
  width: 32px;
  height: 32px;
  cursor: pointer;
}

.modal-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 14px;
  color: #111827;
}

.field input,
.field textarea {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 10px 12px;
  font-size: 14px;
  outline: none;
}

.field input:focus,
.field textarea:focus {
  border-color: #5b3df5;
  box-shadow: 0 0 0 3px rgba(91, 61, 245, 0.15);
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 18px;
}

.btn-secondary {
  background: #f3f4f6;
  color: #111827;
  border: none;
  padding: 10px 14px;
  border-radius: 12px;
  cursor: pointer;
}

.btn-primary {
  background: #5b3df5;
  color: #fff;
  border: none;
  padding: 10px 14px;
  border-radius: 12px;
  cursor: pointer;
}
</style>
