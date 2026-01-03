<template>
  <section class="hero">
    <h1>Find the perfect freelancer for your project</h1>
    <p>Browse top developers, designers and creators</p>

    <div class="search-box">
      <input type="text" placeholder="Search freelancers..." />
      <button>Search</button>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroSection',
}
</script>

<style scoped>
.hero {
  background: #ece6ff;
  padding: 80px 400px;
  border-radius: 20px;
  text-align: center;
}

.search-box {
  margin-top: 30px;
  display: flex;
  justify-content: center;
  gap: 10px;
}

input {
  padding: 12px;
  width: 280px;
  border-radius: 10px;
  border: none;
}

button {
  padding: 12px 24px;
  background: #5b4b8a;
  color: white;
  border-radius: 10px;
  border: none;
}
</style>
