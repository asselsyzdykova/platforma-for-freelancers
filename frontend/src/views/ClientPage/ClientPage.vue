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

      <div v-if="showDeleteModal" class="modal-backdrop" @click.self="closeDeleteModal">
        <div class="modal-card">
          <h3>Delete project?</h3>
          <p>This action can’t be undone.</p>
          <div class="modal-actions">
            <button class="secondary" @click="closeDeleteModal">Cancel</button>
            <button class="danger" @click="confirmDelete">Delete</button>
          </div>
        </div>
      </div>

      <div v-if="showResultModal" class="modal-backdrop" @click.self="closeResultModal">
        <div class="modal-card" :class="resultType">
          <h3>{{ resultTitle }}</h3>
          <p>{{ resultMessage }}</p>
          <div class="modal-actions">
            <button class="primary-btn" @click="closeResultModal">OK</button>
          </div>
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

          <RouterLink :to="{ name: 'EditClientProfile' }" exact-active-class="active" class="edit-btn">Edit Page
          </RouterLink>

          <RouterLink :to="{ name: 'CreateProject' }" exact-active-class="active" class="primary-btn">+ Create Project
          </RouterLink>
        </div>
      </div>

      <section class="intern">
        <div class="intern-header">
          <h3>Would you like to offer an internship?</h3>
          <RouterLink :to="{ name: 'CreateIntern' }" exact-active-class="active" class="primary-btn">Create Internship
          </RouterLink>
        </div>
      </section>

      <section class="projects">
        <h2>ACTIVE PROJECTS</h2>

        <template v-if="projects.length">
          <div class="projects-list">
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

          <div class="pagination" v-if="totalPages > 1">
            <button :disabled="currentPage === 1" @click="currentPage--">Prev</button>
            <button v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }"
              @click="currentPage = page">
              {{ page }}
            </button>
            <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
          </div>
        </template>

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
      showDeleteModal: false,
      pendingDeleteId: null,
      showResultModal: false,
      resultTitle: '',
      resultMessage: '',
      resultType: 'success',
      currentPage: 1,
      totalPages: 1,
      pageSize: 3,
      notificationTimer: null,
      userCreatedAt: null,
    }
  },

  mounted() {
    this.loadClientProfile()
    this.loadUserMeta()
    this.loadClientProjects()
    this.loadNotifications()

    this.notificationTimer = setInterval(() => {
      this.loadNotifications()
    }, 15000)

    window.addEventListener('projectCreated', this.loadClientProjects)
  },

  beforeUnmount() {
    window.removeEventListener('projectCreated', this.loadClientProjects)
    if (this.notificationTimer) {
      clearInterval(this.notificationTimer)
      this.notificationTimer = null
    }
  },

  computed: {
    memberSince() {
      const createdAt = this.userCreatedAt || this.client?.user?.created_at || this.client?.created_at
      if (!createdAt) return '—'
      return new Date(createdAt).getFullYear()
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
        if (res.data?.user?.created_at) {
          this.userCreatedAt = res.data.user.created_at
        }
      } catch (e) {
        console.error('Failed to load client profile', e)
      }
    },

    async loadUserMeta() {
      try {
        const res = await api.get('/me')
        this.userCreatedAt = res.data?.created_at || res.data?.user?.created_at || this.userCreatedAt
      } catch (e) {
        console.error('Failed to load user meta', e)
      }
    },

    async loadClientProjects() {
      try {
        const res = await api.get('/client/projects', {
          params: { page: this.currentPage, per_page: this.pageSize },
        })
        this.projects = res.data?.data || []
        this.totalPages = res.data?.meta?.last_page || 1

        const stats = res.data?.meta?.stats
        this.stats.posted = stats?.posted ?? 0
        this.stats.active = stats?.active ?? 0
        this.stats.completed = stats?.completed ?? 0
      } catch (e) {
        console.warn('Projects not loaded yet (backend later)')
        console.error(e)
      }
    },
    viewProposals(projectId) {
      this.$router.push({ name: 'ClientProjectProposals', params: { id: projectId } })
    },

    async deleteProject(projectId) {
      this.pendingDeleteId = projectId
      this.showDeleteModal = true
    },

    closeDeleteModal() {
      this.showDeleteModal = false
      this.pendingDeleteId = null
    },

    async confirmDelete() {
      if (!this.pendingDeleteId) return

      try {
        await api.delete(`/client/projects/${this.pendingDeleteId}`)
        this.openResultModal('Success', 'Project deleted successfully!', 'success')
        await this.loadClientProjects()
      } catch (e) {
        console.error('Failed to delete project', e)
        this.openResultModal('Failed', 'Failed to delete project.', 'error')
      } finally {
        this.closeDeleteModal()
      }
    },

    openResultModal(title, message, type) {
      this.resultTitle = title
      this.resultMessage = message
      this.resultType = type
      this.showResultModal = true
    },

    closeResultModal() {
      this.showResultModal = false
      this.resultTitle = ''
      this.resultMessage = ''
      this.resultType = 'success'
    },

    async loadNotifications() {
      try {
        const res = await api.get('/client/notifications')
        const list = res.data?.data || res.data || []
        this.notifications = Array.isArray(list)
          ? list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          : []
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

  watch: {
    currentPage() {
      this.loadClientProjects()
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
  align-items: stretch;
  background: #f7f6ff;
}

.client-content {
  width: 60%;
  padding: 30px;
  margin: 0 auto;
  min-height: 100vh;
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
  text-align: center;
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

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-card {
  background: #fff;
  padding: 24px;
  border-radius: 16px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-card.success h3 {
  color: #16a34a;
}

.modal-card.error h3 {
  color: #ef4444;
}

.modal-card h3 {
  margin-bottom: 8px;
}

.modal-card p {
  color: #666;
  margin-bottom: 20px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.pagination {
  margin-top: 24px;
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

.intern {
  margin: 40px 0;
}

.intern-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.intern-header h3 {
  margin: 0;
  flex: 1;
}

.intern-header .primary-btn {
  white-space: nowrap;
  flex-shrink: 0;
}
</style>
