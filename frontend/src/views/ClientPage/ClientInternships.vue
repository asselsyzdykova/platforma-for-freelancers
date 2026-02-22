<template>
  <div class="profile-layout">
    <ClientSidebar />

    <div class="projects-page">
      <h1>Your Projects</h1>
      <p class="subtitle">Projects you posted on the platform</p>

      <div v-if="loading" class="empty">Loading...</div>

      <div v-else>
        <div v-if="projects.length" class="projects-list">
          <div class="project-card" v-for="project in projects" :key="project.id">
            <h3>{{ project.title }}</h3>
            <p class="desc">{{ project.description }}</p>

            <div class="meta">
              <span>💰 {{ project.budget }} €</span>
              <span>📅 {{ project.deadline }}</span>
              <span>📌 {{ project.status }}</span>
            </div>

            <div class="actions">
              <button class="secondary" @click="viewProposals(project.id)">View proposals</button>
              <button class="danger" @click="deleteProject(project.id)">Delete project</button>
            </div>
          </div>
        </div>

        <p v-else class="empty">No projects yet</p>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'

export default {
  name: 'ClientProjects',

  components: { ClientSidebar },

  data() {
    return {
      projects: [],
      loading: true,
      notifications: useNotificationStore(),
    }
  },

  async mounted() {
    await this.loadProjects()
  },

  methods: {
    async loadProjects() {
      this.loading = true
      try {
        const res = await api.get('/client/projects')
        this.projects = res.data
      } catch (e) {
        console.error('Failed to load client projects', e)
      } finally {
        this.loading = false
      }
    },

    viewProposals(projectId) {
      this.$router.push({ name: 'ApplicationDetails', params: { id: projectId } })
    },

    async deleteProject(projectId) {
      if (!confirm('Are you sure you want to delete this project?')) return
      try {
        await api.delete(`/client/projects/${projectId}`)
        await this.loadProjects()
        this.notifications.success('Project deleted')
      } catch (e) {
        console.error('Failed to delete project', e)
        this.notifications.error('Failed to delete project')
      }
    },
  },
}
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}
.projects-page {
  width: 60%;
  padding: 30px;
  margin: 0 auto;
}
.subtitle {
  color: #666;
  margin-bottom: 20px;
}
.projects-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.project-card {
  background: #fff;
  border-radius: 12px;
  padding: 18px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
}
.meta {
  display: flex;
  gap: 18px;
  margin-top: 10px;
}
.actions {
  display: flex;
  gap: 10px;
  margin-top: 12px;
}
.secondary {
  background: #eee;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
}
.danger {
  background: #ff4d4f;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
}
.empty {
  color: #777;
  text-align: center;
  margin-top: 60px;
}
</style>
