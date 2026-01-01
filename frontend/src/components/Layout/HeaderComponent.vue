<template>
  <header class="site-header">
    <div class="header-inner">
      <img src="@/assets/BEZRAB.png" class="logo" />

      <nav class="nav">
        <RouterLink to="/" exact-active-class="active">Home</RouterLink>
        <RouterLink to="/freelancers" exact-active-class="active">Freelancers</RouterLink>
        <RouterLink to="/projects" exact-active-class="active">Projects</RouterLink>
        <RouterLink to="/login" exact-active-class="active">Login</RouterLink>
        <RouterLink to="/register" exact-active-class="active">Register</RouterLink>
      </nav>
    </div>
  </header>
</template>

<script>
export default {
  name: 'HeaderComponent',
}
</script>

<style scoped>
.site-header {
  width: 100%;
  background: #ffffff;
  border-bottom: 1px solid #eee;
}

.header-inner {
  margin: 0 auto;
  padding: 16px 24px;

  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  width: 150px;
  height: auto;
}

.nav {
  display: flex;
  gap: 24px;
}

.nav a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  padding-bottom: 4px;
  border-bottom: 2px solid transparent;
}

.nav a.active {
  color: #5b3df5;
  border-color: #5b3df5;
}

.nav a:hover {
  opacity: 0.8;
}
</style>
