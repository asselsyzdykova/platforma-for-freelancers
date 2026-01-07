<template>
  <div class="application-page">
    <h1>Project Application</h1>

    <div v-if="application">
      <p><strong>Freelancer:</strong> {{ application.freelancer.name }}</p>
      <p><strong>Project:</strong> {{ application.project.title }}</p>
      <p><strong>Proposal:</strong> {{ application.proposal_text }}</p>
      <p><strong>Status:</strong> {{ application.status }}</p>

      <div class="actions">
        <button @click="goToFreelancerProfile">View Freelancer</button>
        <button @click="setUnderReview">Mark as Under Review</button>
        <button @click="acceptApplication">Accept</button>
        <button @click="rejectApplication">Reject</button>
      </div>
    </div>

    <div v-else>
      Loading application...
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/axios";

const route = useRoute();
const router = useRouter();
const application = ref(null);

const loadApplication = async () => {
  try {
    const res = await api.get(`/client/applications/${route.params.id}`);
    application.value = res.data;
  } catch (e) {
    console.error("Failed to load application", e);
  }
};

const goToFreelancerProfile = () => {
  if (application.value && application.value.freelancer.id) {
    router.push(`/freelancer/${application.value.freelancer.id}`);
  }
};

const setUnderReview = async () => {
  try {
    await api.post(`/client/applications/${route.params.id}/review`);
    alert("Application marked as under review");
    application.value.status = "Under Review";
  } catch (e) {
    console.error(e);
    alert("Failed to mark as under review");
  }
};

const acceptApplication = async () => {
  try {
    await api.post(`/client/applications/${route.params.id}/accept`);
    alert("Application accepted");
    application.value.status = "Accepted";
  } catch (e) {
    console.error(e);
    alert("Failed to accept application");
  }
};

const rejectApplication = async () => {
  try {
    await api.post(`/client/applications/${route.params.id}/reject`);
    alert("Application rejected");
    application.value.status = "Rejected";
  } catch (e) {
    console.error(e);
    alert("Failed to reject application");
  }
};

onMounted(loadApplication);
</script>

<style scoped>
.application-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 20px;
  background: #f7f6ff;
  border-radius: 16px;
}

.actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.actions button {
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  background: #5b4b8a;
  color: white;
  font-weight: 600;
}

.actions button:hover {
  opacity: 0.9;
}
</style>
