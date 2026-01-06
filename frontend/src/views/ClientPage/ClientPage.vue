<template>
  <div class="client-page">
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
</template>

<script>
import api from "@/services/axios";


export default {
  name: "ClientProfile",

  data() {
    return {
      client: null,
      projects: [],
      stats: {
        posted: 0,
        active: 0,
        completed: 0,
      },
    };
  },

  mounted() {
    this.loadClientProfile();
    this.loadClientProjects();

    window.addEventListener("projectCreated", this.loadClientProjects);
  },

  beforeUnmount() {
    window.removeEventListener("projectCreated", this.loadClientProjects);
  },

  computed: {
    memberSince() {
      if (!this.client?.created_at) return "—";
      return new Date(this.client.created_at).getFullYear();
    },
  },

  methods: {
    async loadClientProfile() {
      try {
        const res = await api.get("/client/profile");
        this.client = res.data;
      } catch (e) {
        console.error("Failed to load client profile", e);
      }
    },

    async loadClientProjects() {
      try {
        const res = await api.get("/client/projects");
        this.projects = res.data;

        this.stats.posted = res.data.length;
        this.stats.active = res.data.filter(p => p.status === "In progress").length;
        this.stats.completed = res.data.filter(p => p.status === "Completed").length;
      } catch (e) {
        console.warn("Projects not loaded yet (backend later)");
        console.error(e);
      }
    },

    editProfile() {
      this.$router.push("/edit-client-profile");
    },

    createProject() {
      this.$router.push("/create-project");
    },

    async deleteProject(projectId) {
  if (!confirm("Are you sure you want to delete this project?")) return;

  try {
    await api.delete(`/client/projects/${projectId}`);
    alert("Project deleted successfully!");
    await this.loadClientProjects();
  } catch (e) {
    console.error("Failed to delete project", e);
    alert("Failed to delete project");
  }
},
  },
};
</script>


<style scoped>

.reviews {
  color: #777;
  font-size: 14px;
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
  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
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
  box-shadow: 0 6px 18px rgba(0,0,0,0.05);
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

</style>
