<template>
  <footer class="footer">
    <div class="container">
      <p>© 2026 BezRab. All rights reserved.</p>
      <div class="links">
        <RouterLink :to="{ name: 'PrivacyPolicy' }" exact-active-class="active">Privacy Policy</RouterLink>
        <RouterLink :to="{ name: 'TermsAndConditions' }" exact-active-class="active">Terms and Conditions</RouterLink>
        <a href="#">Contact</a>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "FooterComponent"
}
</script>

<style scoped>
.footer {
  background: #e6e0ff;
  padding: 24px 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.footer p {
  color: #555;
  font-size: 14px;
}

.links {
  display: flex;
  gap: 16px;
}

.links a {
  text-decoration: none;
  font-size: 14px;
  color: #5D3A9B;
}
</style>
