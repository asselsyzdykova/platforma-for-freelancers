<template>
  <div class="project-card">
    <h3>{{ project.title }}</h3>
    <p class="description">{{ project.description }}</p>

    <div class="client" v-if="project.client">
      <img
        v-if="project.client.avatar_url"
        :src="project.client.avatar_url"
        class="avatar"
      />
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

    <span
    v-if="project.already_applied > 0"
    class="applied-label">
    ✓ You already applied
  </span>
    <button v-else class="btn" @click="$emit('respond', project)">
      Response to a job
    </button>
  </div>
</template>

<script>
export default {
  name: "ProjectCard",
  props: {
    project: {
      type: Object,
      required: true,
    },
  },

  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    },
  },
};
</script>

<style scoped>

.applied-label {
  background: #ecfdf5;
  color: #16a34a;
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 13px;
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
</style>
