import { defineStore } from "pinia";
import api from "@/services/axios";

export const useProjectStore = defineStore("project", {
  state: () => ({
    projects: [],
    stats: {
      posted: 0,
      active: 0,
      completed: 0,
    },
  }),

  actions: {
    async loadProjects() {
      try {
        const res = await api.get("/client/projects");
        this.projects = res.data;

        this.stats.posted = res.data.length;
        this.stats.active = res.data.filter(p => p.status === "In progress").length;
        this.stats.completed = res.data.filter(p => p.status === "Completed").length;
      } catch (e) {
        console.error(e);
      }
    },

    async createProject(payload) {
      await api.post("/client/projects", payload);
      await this.loadProjects();
    },

    async closeProject(projectId) {
      await api.post(`/client/projects/${projectId}/close`);
      await this.loadProjects();
    },
  },
});
