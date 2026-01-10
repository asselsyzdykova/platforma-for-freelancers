<template>
  <div class="categories">
    <span v-for="cat in categories" :key="cat">
      {{ cat }}
    </span>
  </div>
</template>

<script>
export default {
  name: 'CategoryList',
  data() {
    return {
      categories: ['Frontend', 'Backend', 'Design', 'Mobile Apps', 'UI/UX', 'Fullstack', 'DevOps', 'Data Science', 'Digital Marketing', 'Writing', 'Translation', 'Video Editing', 'Audio Editing', 'Graphics', 'Animation', 'Video Production', 'Photography', 'SEO', 'Content Creation', 'Project Management'],
    }
  },
}
</script>

<style scoped>
.categories {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.categories span {
  background: #ece6ff;
  padding: 10px 16px;
  border-radius: 14px;
}
</style>
