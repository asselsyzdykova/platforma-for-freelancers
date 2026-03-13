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
      <div v-for="user in candidates" :key="user.id" class="moderation-item">
        <FreelancerCard :freelancer="user" />

        <div class="admin-actions">
          <button @click="verifyUser(user.id)" class="approve-btn">
            ✅ Approve Verification
          </button>
          <button @click="openRejectModal(user)" class="reject-btn">
            ❌ Needs Changes
          </button>
        </div>
      </div>
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
import { ref, onMounted } from 'vue';
import api from '@/services/axios';
import FreelancerCard from '@/components/HomePage/FreelancerCard.vue';
import { useNotificationStore } from '@/stores/notificationStore'

const notifications = useNotificationStore()
const candidates = ref([]);
const loading = ref(true);
const sending = ref(false);

const showModal = ref(false);
const selectedUser = ref(null);
const rejectReason = ref('');

const fetchCandidates = async () => {
  try {
    const response = await api.get('/manager/verifications');
    candidates.value = response.data;
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
  sending.value = true;
  try {
    await api.post(`/manager/verifications/${selectedUser.value.id}/reject`, {
      reason: rejectReason.value
    });
    candidates.value = candidates.value.filter(u => u.id !== selectedUser.value.id);
    closeModal();
    notifications.info('Feedback sent to freelancer inbox.');
  } catch {
    notifications.error('Failed to send rejection');
  } finally {
    sending.value = false;
  }
};

onMounted(fetchCandidates);
</script>

<style scoped>
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
