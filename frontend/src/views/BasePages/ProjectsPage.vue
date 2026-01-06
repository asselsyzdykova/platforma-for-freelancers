<template>
  <div class="projects">
    <h1>Projects</h1>
    <p class="subtitle">
      Browse available projects and find work that matches your skills
    </p>

    <div class="project-grid">
      <div
        class="project-card"
        v-for="project in projects"
        :key="project.id"
      >
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
          <span
            class="tag"
            v-for="tag in project.tags"
            :key="tag"
          >
            {{ tag }}
          </span>
        </div>

        <button class="btn">View Project</button>
      </div>
    </div>
  </div>
</template>


<script>
import api from "@/services/axios";

export default {
  name: "ProjectsPage",

  data() {
    return {
      projects: [],
    };
  },

  mounted() {
    this.loadProjects();
  },

  methods: {
    async loadProjects() {
      try {
        const res = await api.get("/projects");
        this.projects = res.data;
      } catch (e) {
        console.error("Failed to load projects", e);
      }
    },
  },
};
</script>



<style scoped>
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
</style>
