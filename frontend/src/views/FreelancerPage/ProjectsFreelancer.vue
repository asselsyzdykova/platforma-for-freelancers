<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>Projects</h1>

      <div class="projects-card">

        <div class="project-item" v-for="project in projects" :key="project.id">

          <div class="project-header">
            <h3>{{ project.name }}</h3>

            <span class="status" :class="project.status">
              {{ project.status }}
            </span>
          </div>

          <p class="client">
            <strong>Client:</strong>
            {{ project.client?.name || 'Unknown' }}
          </p>
          <p class="description">
            {{ getActiveMilestone(project) }}
          </p>
          <div class="progress">
            <div class="progress-fill" :style="{ width: getProgress(project) + '%' }"></div>
          </div>

          <p class="deadline">
            <strong>Deadline:</strong>
            {{ project.deadline || 'Not set' }}
          </p>

        </div>

      </div>
    </div>
  </div>
</template>
<script setup>
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'
import { ref, onMounted } from 'vue'
import api from '@/services/axios'
const projects = ref([])

const loadProjects = async () => {
  const res = await api.get('/freelancer/projects')
  projects.value = res.data
}
onMounted(loadProjects)

const getActiveMilestone = (project) => {

  if (!project.milestones || project.milestones.length === 0) {
    return 'No milestones'
  }

  const active = project.milestones.find(
    m => m.status !== 'completed'
  )

  return active ? active.title : 'Project completed'
}

const getProgress = (project) => {

  if (!project.milestones || project.milestones.length === 0) {
    return 0
  }

  const total = project.milestones.length

  const completed = project.milestones.filter(
    m => m.status === 'completed'
  ).length

  return Math.round((completed / total) * 100)
}
</script>
<style scoped>
.page-layout {
  display: flex;
  min-height: 100vh;
  background: #f7f6ff;
}

.content {
  flex: 1;
  padding: 40px;
}

h1 {
  font-size: 32px;
  margin-bottom: 24px;
}

.projects-card {
  background: #e9e3ff;
  border-radius: 30px;
  padding: 40px;
  max-height: 600px;
  overflow-y: auto;
}

.project-item {
  background: white;
  border-radius: 18px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.status {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  text-transform: capitalize;
}

.status.in-progress {
  background: #fff3cd;
  color: #856404;
}

.status.completed {
  background: #d4edda;
  color: #155724;
}

.client,
.deadline {
  font-size: 14px;
  margin-bottom: 6px;
}

.description {
  font-size: 14px;
  margin: 12px 0;
}

.progress {
  height: 10px;
  background: #ddd;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 10px;
}

.progress-fill {
  height: 100%;
  background: #2cff00;
  transition: 0.3s;
}

@media (max-width: 768px) {
  .page-layout {
    flex-direction: column;
  }

  .content {
    padding: 20px;
  }

  .projects-card {
    padding: 20px;
    border-radius: 20px;
  }
}

@media (max-width: 600px) {
  h1 {
    font-size: 24px;
  }

  .project-item {
    padding: 16px;
  }

  .project-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
  }
}
</style>
