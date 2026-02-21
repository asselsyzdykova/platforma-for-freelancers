<template>
  <div class="intern-card">
    <div class="card-in" v-for="intern in interns" :key="intern.id">
      <div class="header">
      <img :src="intern.avatar_url" class="avatar-img" alt="Avatar" />
      <h3 class="client-name">{{ intern.name }}</h3>
      </div>
      <h2>{{ intern.company }}</h2>
      <h3>{{ intern.title }}</h3>
      <p class="description">{{ intern.description }}</p>
      <p class="cena">{{ intern.price }}</p>
      <p class="cas">The internship duration: {{ intern.time }}</p>
      <p class="cas">The allowed number of students: {{ intern.number }}</p>
      <button @click="toApply" class="btn-apply">To Apply</button>
    </div>
  </div>
</template>

<script>
export default{
  name : 'InternCard',
  data() {
    return {
      interns: [
        {
          id: 1,
          name:'Assel Syzdykova',
          company: 'Power Play Studio',
          avatar_url: 'img.png',
          title: 'Frontend Developer Intern',
          description: 'Learn Vue.js and modern web development Learn Vue.js and modern web development Learn Vue.js and modern web development Learn Vue.js and modern web development',
          price: 3000,
          time: '3 months',
          number: '10'
        }
      ]
    }
  }
}
</script>



<style scoped>
.intern-card{
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 40px 24px;
  border-radius: 10px;
  margin: 30px;
  box-shadow: 0 2px 6px rgba(93, 58, 155, 0.6);
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(10px);
}
.header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
}
.client-name{
  margin: 0;
  font-size: 1.5em;
  font-weight: bold;
}
.avatar-img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  display: block;
  background: #ddd;
}
.card-in {
  margin-bottom: 20px;
}
.card-in h3{
  padding-left: 30px;
}
.card-in p{
  padding-left: 30px;
}
.description{
  max-width: 700px;
}
.cena{
  text-align: right;
  padding-right: 30px;
}
.cas{
  text-align: right;
  padding-right: 30px;
}

.btn-apply{
  border-radius: 15px;
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: white;
  padding: 10px;
  box-shadow: 0 2px 6px rgba(93, 58, 155, 0.6);
  cursor: pointer;
  border: none;
}
.btn-apply:hover{
  background: white;
  color: #5D3A9B;
}
</style>
