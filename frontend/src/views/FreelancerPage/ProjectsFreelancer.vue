<template>
  <div class="page-layout">
    <SidebarMenu />

    <div class="content">
      <h1>Projects</h1>

      <div class="projects-card">
        <div class="project-item" v-for="(project, index) in projects" :key="index">
          <div class="project-header">
            <h3>{{ project.name }}</h3>
            <span class="status" :class="project.status">
              {{ project.status }}
            </span>
          </div>

          <p class="client"><strong>Client:</strong> {{ project.client }}</p>

          <p class="description">
            {{ project.description }}
          </p>

          <div class="progress">
            <div class="progress-fill" :style="{ width: project.progress + '%' }"></div>
          </div>

          <p class="deadline"><strong>Deadline:</strong> {{ project.deadline }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import SidebarMenu from '@/components/FreelancerPageMenu/SidebarMenu.vue'

const projects = [
  {
    name: 'Web platform for freelancers',
    client: 'David',
    description: 'Development of frontend part using Vue.js',
    progress: 30,
    deadline: '30 Jul 2026',
    status: 'in-progress',
  },
  {
    name: 'Landing page',
    client: 'Anna',
    description: 'Responsive landing page for startup',
    progress: 70,
    deadline: '15 Jul 2026',
    status: 'in-progress',
  },
  {
    name: 'Dashboard UI',
    client: 'Company XYZ',
    description: 'UI design and implementation',
    progress: 100,
    deadline: 'Completed',
    status: 'completed',
  },
]
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
</style>
