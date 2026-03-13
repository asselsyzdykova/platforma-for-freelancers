<template>
  <div class="moderation-page">
    <header class="page-header">
      <h1>Verification Queue</h1>
      <p>Freelancers waiting for their Premium badge check</p>
    </header>

    <div v-if="loading" class="loading">Loading candidates...</div>

    <div v-else-if="candidates.length === 0" class="empty-state">
      🎉 All caught up! No profiles to verify.
    </div>

    <div v-else class="candidates-grid">
      <div v-for="user in candidates" :key="user.id" class="moderation-item" :class="`status-${user.status}`">
        <div class="card-wrapper">
          <FreelancerCard :freelancer="user" />

          <div v-if="user.status !== 'pending'" class="status-overlay">
            <span v-if="user.status === 'rejected'">❌ Rejected</span>
            <span v-if="user.status === 'approved'">✅ Approved</span>
          </div>
        </div>

        <div class="admin-actions">
          <template v-if="user.status === 'pending'">
            <button @click="verifyUser(user.id)" class="approve-btn">
              ✅ Approve Verification
            </button>
            <button @click="openRejectModal(user)" class="reject-btn">
              ❌ Needs Changes
            </button>
          </template>

          <p v-else class="done-text">Action completed.</p>
        </div>
      </div>
      <AppPagination v-model="currentPage" :totalPages="totalPages" mode="simple" />
    </div>
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <h3>Reject Verification</h3>
          <p>User: <strong>{{ selectedUser?.name }}</strong></p>

          <textarea v-model="rejectReason"
            placeholder="Describe what needs to be fixed (e.g., 'Please upload a professional photo')..."
            rows="5"></textarea>

          <div class="modal-actions">
            <button @click="closeModal" class="cancel-btn">Cancel</button>
            <button @click="submitReject" :disabled="!rejectReason.trim() || sending" class="confirm-reject-btn">
              {{ sending ? 'Sending...' : 'Send Feedback' }}
            </button>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '@/services/axios';
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue';
import { useNotificationStore } from '@/stores/notificationStore'
import AppPagination from '@/components/UI/AppPagination.vue';
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const notifications = useNotificationStore()
const candidates = ref([]);
const loading = ref(true);
const sending = ref(false);

const showModal = ref(false);
const selectedUser = ref(null);
const rejectReason = ref('');

const totalPages = ref(1)

const currentPage = ref(Number(route.query.page) || 1)

const fetchCandidates = async (page = 1) => {
  loading.value = true
  try {
    const response = await api.get(`/manager/verifications?page=${page}`);
    candidates.value = response.data.data.map(u => ({ ...u, status: 'pending' }));
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error("Failed to load queue", error);
  } finally {
    loading.value = false;
  }
};

const verifyUser = async (userId) => {
  if (!confirm('Are you sure you want to verify this user?')) return;

  try {
    await api.post(`/manager/verifications/${userId}/approve`);
    candidates.value = candidates.value.filter(u => u.id !== userId);
    notifications.success('User verified successfully!');
  } catch {
    notifications.error('Error verifying user');
  }
};

const openRejectModal = (user) => {
  selectedUser.value = user;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedUser.value = null;
  rejectReason.value = '';
};

const submitReject = async () => {
  if (!selectedUser.value) return;
  const targetId = selectedUser.value.id;
  sending.value = true;

  try {
    await api.post(`/manager/verifications/${selectedUser.value.id}/reject`, {
      reason: rejectReason.value
    });
    const user = candidates.value.find(u => u.id === targetId);
    if (user) {
      user.status = 'rejected';
    } closeModal();
    notifications.info('Feedback sent. Profile marked as rejected.');
  } catch {
    notifications.error('Failed to send rejection');
  } finally {
    sending.value = false;
  }
};
watch(currentPage, (newPage) => {
  router.push({ query: { ...route.query, page: newPage } });
  fetchCandidates(newPage);
});

onMounted(() => fetchCandidates(currentPage.value));
</script>

<style scoped>
.card-wrapper {
  position: relative;
}

.status-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.5rem;
  font-weight: bold;
  border-radius: 20px;
  z-index: 2;
}

.status-rejected .status-overlay {
  color: #e74c3c;
}

.status-approved .status-overlay {
  color: #27ae60;
}

.done-text {
  text-align: center;
  font-size: 0.8rem;
  color: #888;
  margin-top: 5px;
}

.moderation-item.status-rejected,
.moderation-item.status-approved {
  opacity: 0.8;
  pointer-events: none;
}

.moderation-page {
  padding: 40px;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 30px;
  text-align: center;
}

.candidates-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 40px;
  justify-items: center;
}

.moderation-item {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.admin-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}

.approve-btn,
.reject-btn {
  border: none;
  padding: 12px;
  border-radius: 12px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}

.approve-btn {
  background: #27ae60;
  color: white;
}

.approve-btn:hover {
  background: #2ecc71;
  transform: translateY(-2px);
}

.reject-btn {
  background: #e74c3c;
  color: white;
}

/* MODAL STYLES */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 20px;
  width: 400px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

textarea {
  width: 100%;
  margin: 15px 0;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #ddd;
  resize: none;
  font-family: inherit;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.cancel-btn {
  background: #eee;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
}

.confirm-reject-btn {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
}

.confirm-reject-btn:disabled {
  opacity: 0.5;
}
</style>
