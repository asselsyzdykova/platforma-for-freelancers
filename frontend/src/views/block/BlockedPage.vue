<template>
  <div class="blocked-page">
    <div class="blocked-card">
      <h1>🚫 Your account is blocked</h1>
      <p>
        Your account has been blocked by the administrator. You cannot access
        your profile or perform actions until the block is lifted.
      </p>
    </div>
  </div>
</template>

<style scoped>
.blocked-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: linear-gradient(135deg, #f8d7da, #f5c2c7);
  font-family: 'Arial', sans-serif;
}

.blocked-card {
  background: white;
  padding: 40px 60px;
  border-radius: 20px;
  text-align: center;
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
}

h1 {
  font-size: 28px;
  color: #b91c1c;
  margin-bottom: 16px;
}

p {
  font-size: 16px;
  color: #333;
  margin-bottom: 24px;
}

.login-btn {
  padding: 10px 24px;
  font-size: 14px;
  border: none;
  border-radius: 12px;
  background: #b91c1c;
  color: white;
  cursor: pointer;
  transition: 0.2s;
}

.login-btn:hover {
  background: #991b1b;
}
</style>
