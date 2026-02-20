<template>
  <div class="verify-success-page">
    <div class="success-card">
      <h1>🎉 Email Verified!</h1>
      <p>Your email has been successfully verified. You can now log in and start using the platform.</p>
      <router-link :to="{ name: 'login' }" class="btn-primary">
        Go to Login
      </router-link>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.verify-success-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.success-card {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  text-align: center;
  max-width: 400px;
}

h1 {
  color: #4caf50;
  margin-bottom: 1rem;
}

p {
  color: #666;
  margin-bottom: 2rem;
}

.btn-primary {
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 5px;
  text-decoration: none;
  display: inline-block;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5D3A9B, #7c3aed);
}
</style>
