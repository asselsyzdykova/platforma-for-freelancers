<template>
  <div class="profile-layout">
    <ClientSidebar v-if="client" :userName="client.user?.name" />

    <div class="client-content">
      <div class="top-bar">
        <div class="inbox-icon" @click="goToInbox">
          📩
          <span v-if="hasUnread" class="unread-dot"></span>
        </div>
      </div>

      <h1>CLIENT PROFILE</h1>

      <div class="client-card" v-if="client">
        <div class="left">
          <div class="avatar">
            <img v-if="client.avatar_url" :src="client.avatar_url" alt="Avatar" />
          </div>
          <h2>{{ client.user?.name }}</h2>
          <p class="company" v-if="client.company">{{ client.company }}</p>
          <p class="location">📍 {{ client.location }}</p>
        </div>

        <div class="right">
          <p><strong>Projects posted:</strong> {{ stats.posted }}</p>
          <p>
            <strong>Rating:</strong> ⭐ {{ client.rating ?? '—' }}
            <span class="reviews">({{ client.reviews ?? 0 }} reviews)</span>
          </p>
          <p><strong>Active projects:</strong> {{ stats.active }}</p>
          <p><strong>Completed:</strong> {{ stats.completed }}</p>
          <p><strong>Member since:</strong> {{ memberSince }}</p>

          <button class="edit-btn" @click="editProfile">Edit profile</button>
          <button class="primary-btn" @click="createProject">+ Create project</button>
        </div>
      </div>

      <section class="projects">
        <h2>ACTIVE PROJECTS</h2>

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
              <button class="secondary">View proposals</button>
              <button class="danger" @click="deleteProject(project.id)">Delete project</button>
            </div>
          </div>
        </div>

        <p v-else class="empty">No active projects yet</p>
      </section>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import { useNotificationStore } from '@/stores/notificationStore'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'

export default {
  name: 'ClientProfile',

  components: {
    ClientSidebar,
  },

  data() {
    return {
      client: null,
      projects: [],
      stats: {
        posted: 0,
        active: 0,
        completed: 0,
      },
      notifications: [],
      notify: useNotificationStore(),
    }
  },

  mounted() {
    this.loadClientProfile()
    this.loadClientProjects()
    this.loadNotifications()

    window.addEventListener('projectCreated', this.loadClientProjects)
  },

  beforeUnmount() {
    window.removeEventListener('projectCreated', this.loadClientProjects)
  },

  computed: {
    memberSince() {
      if (!this.client?.created_at) return '—'
      return new Date(this.client.created_at).getFullYear()
    },

    hasUnread() {
      return this.notifications.some((n) => !n.is_read)
    },
  },

  methods: {
    goToInbox() {
      this.$router.push({ name: 'ClientInbox' })
    },

    async loadClientProfile() {
      try {
        const res = await api.get('/client/profile')
        this.client = res.data
      } catch (e) {
        console.error('Failed to load client profile', e)
      }
    },

    async loadClientProjects() {
      try {
        const res = await api.get('/client/projects')
        this.projects = res.data

        this.stats.posted = res.data.length
        this.stats.active = res.data.filter((p) => p.status === 'In progress').length
        this.stats.completed = res.data.filter((p) => p.status === 'Completed').length
      } catch (e) {
        console.warn('Projects not loaded yet (backend later)')
        console.error(e)
      }
    },

    editProfile() {
      this.$router.push({ name: 'EditClientProfile' })
    },

    createProject() {
      this.$router.push({ name: 'CreateProject' })
    },

    async deleteProject(projectId) {
      if (!confirm('Are you sure you want to delete this project?')) return

      try {
        await api.delete(`/client/projects/${projectId}`)
        this.notify.success('Project deleted successfully!')
        await this.loadClientProjects()
      } catch (e) {
        console.error('Failed to delete project', e)
        this.notify.error('Failed to delete project')
      }
    },

    async loadNotifications() {
      try {
        const res = await api.get('/client/notifications')
        this.notifications = res.data.sort(
          (a, b) => new Date(b.created_at) - new Date(a.created_at),
        )
      } catch (e) {
        console.warn('Notifications not loaded yet', e)
      }
    },

    openNotification(note) {
      if (!note.is_read) {
        api
          .post(`/client/notifications/${note.id}/read`)
          .then(() => (note.is_read = true))
          .catch((e) => console.error(e))
      }

      if (note.link) {
        this.$router.push(note.link)
      }
    },
  },
}
</script>

<style scoped>
.top-bar {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 20px;
}

.inbox-icon {
  position: relative;
  font-size: 24px;
  cursor: pointer;
}

.unread-dot {
  position: absolute;
  top: -4px;
  right: -4px;
  width: 10px;
  height: 10px;
  background: #22c55e;
  border-radius: 50%;
}

.reviews {
  color: #777;
  font-size: 14px;
}

.profile-layout {
  display: flex;
  min-height: 100vh;
}

.client-content {
  width: 60%;
  padding: 30px;
  margin: 0 auto;
}
.client-page {
  max-width: 1100px;
  margin: 0 auto;
  padding: 30px;
}

.client-card {
  display: flex;
  justify-content: space-between;
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
  margin-bottom: 40px;
}

.left {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.avatar img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
}

.company {
  color: #666;
}

.right {
  text-align: right;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.edit-btn {
  margin-top: 12px;
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  background: #111;
  color: #fff;
  cursor: pointer;
}

.projects h2 {
  margin-bottom: 20px;
}

.projects-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.project-card {
  background: #fff;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
}

.project-card h3 {
  margin-bottom: 6px;
}

.desc {
  color: #555;
  margin-bottom: 12px;
}

.meta {
  display: flex;
  gap: 20px;
  font-size: 14px;
  margin-bottom: 14px;
}

.actions {
  display: flex;
  gap: 12px;
}

.secondary {
  background: #eee;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.danger {
  background: #ff4d4f;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.empty {
  color: #777;
}

.primary-btn {
  margin-top: 10px;
  padding: 12px 18px;
  border-radius: 10px;
  border: none;
  background: #5b4b8a;
  color: white;
  font-weight: 600;
  cursor: pointer;
}

.primary-btn:hover {
  opacity: 0.9;
}

.notifications {
  margin-top: 40px;
  margin-left: 900px;
  text-align: right;
}

.notifications h2 {
  margin-bottom: 20px;
}

.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.notification-card {
  padding: 16px;
  border-radius: 12px;
  background: #f9f9f9;
  cursor: pointer;
  transition: background 0.2s;
  border-left: 4px solid transparent;
}

.notification-card.unread {
  background: #eef6ff;
  border-left-color: #4f46e5;
}

.notification-card:hover {
  background: #f0f0f0;
}

.notification-card .title {
  font-weight: 600;
  margin-bottom: 4px;
}

.notification-card .body {
  font-size: 14px;
  color: #555;
}

.notification-card .time {
  font-size: 12px;
  color: #999;
  float: right;
}
</style>
