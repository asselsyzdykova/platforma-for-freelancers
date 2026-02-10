<template>
  <div class="profile-layout">
    <ClientSidebar />

    <div class="proposals-page">
      <div class="header-row">
        <h1>Project proposals</h1>
        <button class="secondary" @click="goBack">Back</button>
      </div>
      <p class="subtitle">Applications from freelancers</p>

      <div v-if="loading" class="empty">Loading...</div>

      <template v-else>
        <template v-if="proposals.length">
          <div class="proposals-list">
            <div class="proposal-card" v-for="item in proposals" :key="item.id">
            <div class="left">
              <div class="avatar">
                <img v-if="item.freelancer?.avatar_url" :src="item.freelancer.avatar_url" alt="" />
                <span v-else>{{ initials(item.freelancer?.name) }}</span>
              </div>
              <div>
                <h3>{{ item.freelancer?.name || 'Freelancer' }}</h3>
                <p class="time">{{ formatDate(item.created_at) }}</p>
              </div>
            </div>

            <div class="body">
              <p>{{ item.message }}</p>
            </div>

            <div class="meta">
              <div>
                <span class="label">Budget</span>
                <div class="value">{{ item.budget }} €</div>
              </div>
              <div>
                <span class="label">Status</span>
                <div class="value status">{{ item.status }}</div>
              </div>
            </div>

            <div class="actions">
              <button class="primary" @click="openDetails(item.id)">View details</button>
            </div>
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
        </template>

        <p v-else class="empty">No proposals yet</p>
      </template>
    </div>
  </div>
</template>

<script>
import api from '@/services/axios'
import ClientSidebar from '@/components/ClientPageMenu/SidebarMenu.vue'

export default {
  name: 'ProjectProposals',
  components: { ClientSidebar },
  data() {
    return {
      proposals: [],
      loading: true,
      currentPage: 1,
      totalPages: 1,
      pageSize: 6,
    }
  },
  mounted() {
    this.loadProposals()
  },
  methods: {
    async loadProposals() {
      this.loading = true
      try {
        const res = await api.get(`/client/projects/${this.$route.params.id}/proposals`, {
          params: { page: this.currentPage, per_page: this.pageSize },
        })
        this.proposals = res.data?.data || []
        this.totalPages = res.data?.meta?.last_page || 1
      } catch (e) {
        console.error('Failed to load proposals', e)
        this.proposals = []
        this.totalPages = 1
      } finally {
        this.loading = false
      }
    },
    openDetails(id) {
      this.$router.push({ name: 'ApplicationDetails', params: { id } })
    },
    goBack() {
      this.$router.back()
    },
    initials(name) {
      if (!name) return 'U'
      return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase()
    },
    formatDate(date) {
      return new Date(date).toLocaleString()
    },
  },
  watch: {
    currentPage() {
      this.loadProposals()
    },
  },
}
</script>

<style scoped>
.profile-layout {
  display: flex;
  min-height: 100vh;
}

.proposals-page {
  flex: 1;
  padding: 30px;
  max-width: 900px;
}

.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.subtitle {
  color: #666;
  margin-bottom: 20px;
}

.proposals-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.proposal-card {
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
  display: grid;
  gap: 16px;
}

.left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  overflow: hidden;
}

.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.time {
  font-size: 12px;
  color: #999;
}

.body {
  color: #444;
}

.meta {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
}

.label {
  font-size: 12px;
  color: #888;
}

.value {
  font-weight: 600;
}

.status {
  color: #f59e0b;
}

.actions {
  display: flex;
  justify-content: flex-end;
}

.primary {
  background: #5b4b8a;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.secondary {
  background: #eee;
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.empty {
  color: #777;
  text-align: center;
  margin-top: 40px;
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
</style>
