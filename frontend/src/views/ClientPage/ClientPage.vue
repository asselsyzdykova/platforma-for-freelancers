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

      <BaseModal v-if="showDeleteModal" title="Delete project?" @close="closeDeleteModal">
        <p>This action can’t be undone.</p>
        <template #actions>
          <button class="secondary" @click="closeDeleteModal">Cancel</button>
          <button class="danger" @click="confirmDelete">Delete</button>
        </template>
      </BaseModal>

      <BaseModal v-if="showResultModal" :title="resultTitle" :type="resultType" @close="closeResultModal">
        <p>{{ resultMessage }}</p>
        <template #actions>
          <button class="primary-btn" @click="closeResultModal">OK</button>
        </template>
      </BaseModal>

      <h1>CLIENT PROFILE</h1>

      <ClientHeaderCard v-if="client" :client="client" :stats="stats" :userCreatedAt="userCreatedAt" />

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
            <ActiveProjectCard v-for="project in projects" :key="project.id" :project="project" @delete="deleteProject"
              @view="viewProposals" />
          </div>
          <AppPagination v-model="currentPage" :totalPages="totalPages" mode="simple" />
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
import ActiveProjectCard from '@/components/ClientPageMenu/ActiveProjectCard.vue'
import BaseModal from '@/components/ClientPageMenu/BaseModal.vue'
import ClientHeaderCard from '@/components/ClientPageMenu/ClientHeaderCard.vue'
import AppPagination from '@/components/UI/AppPagination.vue'

export default {
  name: 'ClientProfile',

  components: {
    ClientSidebar,
    ActiveProjectCard,
    BaseModal,
    ClientHeaderCard,
    AppPagination
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
  },

  beforeUnmount() {
    window.removeEventListener('projectCreated', this.loadClientProjects)
    if (this.notificationTimer) {
      clearInterval(this.notificationTimer)
      this.notificationTimer = null
    }
  },

  computed: {
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
        const res = await api.get('/notifications')
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
          .post(`/notifications/${note.id}/read`)
          .then(() => (note.is_read = true))
          .catch((e) => console.error(e))
      }

      if (note.link) {
        this.$router.push(note.link)
      }
    },
  },

  watch: {

    $route(to) {
      if (to.name === 'ClientProfile') {
        this.loadClientProjects()
      }
    },

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

.profile-layout {
  display: flex;
  min-height: 100vh;
  align-items: stretch;
  background: #f7f6ff;
}

.client-content {
  flex: 1;
  max-width: 1200px;
  padding: 30px;
  margin: 0 auto;
  min-height: 100vh;
}

.client-page {
  max-width: 1100px;
  margin: 0 auto;
  padding: 30px;
}


.projects h2 {
  margin-bottom: 20px;
}

.projects-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.empty {
  color: #777;
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

.modal-card p {
  color: #666;
  margin-bottom: 20px;
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

@media (max-width: 1024px) {
  .client-content {
    width: 90%;
    padding: 20px;
  }
}

@media (max-width: 768px) {
  .profile-layout {
    flex-direction: column;
  }

  .client-content {
    width: 100%;
    padding: 15px;
    box-sizing: border-box;
    padding-top: 40px;
  }

  .top-bar {
    justify-content: center;
    margin-bottom: 30px;
    position: absolute;
  }

  .left,
  .right {
    text-align: center;
    align-items: center;
    width: 100%;
  }

  .avatar img {
    width: 100px;
    height: 100px;
  }

  .intern-header {
    flex-direction: column;
    text-align: center;
    gap: 15px;
  }

  .intern-header h3 {
    font-size: 18px;
  }

  .project-card {
    padding: 15px;
  }

  .meta {
    flex-direction: column;
    gap: 8px;
  }

  .actions {
    flex-direction: column;
    gap: 10px;
  }

  .actions button,
  .actions a {
    width: 100%;
    text-align: center;
  }

  h1 {
    font-size: 24px;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .top-bar {
    margin-bottom: 10px;
  }

  .primary-btn,
  .edit-btn {
    width: 100%;
  }

  .pagination {
    flex-wrap: wrap;
  }
}
</style>
