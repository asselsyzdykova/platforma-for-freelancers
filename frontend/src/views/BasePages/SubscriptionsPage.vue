<template>
  <div class="subscriptions-page">
    <h1 class="title">Choose Your Plan</h1>
    <p class="subtitle">Upgrade your account to get more opportunities as a freelancer.</p>

    <div class="plans">
      <!-- FREE PLAN -->
      <div class="plan-card">
        <h2>Free</h2>
        <p class="price">$0 / month</p>

        <ul>
          <li>✔ Create profile</li>
          <li>✔ Browse projects</li>
          <li>✖ Limited proposals (3/month)</li>
          <li>✖ No priority listing</li>
        </ul>

        <button class="btn disabled" disabled>Current Plan</button>
      </div>

      <!-- PRO PLAN -->
      <div class="plan-card highlighted">
        <h2>Pro</h2>
        <p class="price">$15 / month</p>

        <ul>
          <li>✔ Unlimited proposals</li>
          <li>✔ Priority in search results</li>
          <li>✔ Access to premium projects</li>
          <li>✔ Direct client messaging</li>
        </ul>

        <button class="btn primary" @click="subscribe('pro')">Upgrade to Pro</button>
      </div>

      <!-- PREMIUM PLAN -->
      <div class="plan-card">
        <h2>Premium</h2>
        <p class="price">$30 / month</p>

        <ul>
          <li>✔ All Pro features</li>
          <li>✔ Featured freelancer badge</li>
          <li>✔ Priority support</li>
          <li>✔ Higher visibility</li>
        </ul>

        <button class="btn primary" @click="subscribe('premium')">Upgrade to Premium</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'

const subscribe = async (plan) => {
  try {
    const token = localStorage.getItem('access_token')

    const base =
      (import.meta.env.VITE_API_BASE_URL || '').replace(/\/$/, '') || window.location.origin
    const url = `${base}/api/create-checkout-session`

    const { data } = await axios.post(
      url,
      { plan },
      {
        headers: token ? { Authorization: `Bearer ${token}` } : {},
        withCredentials: true,
      },
    )

    if (data?.url) {
      window.location.href = data.url
      return
    }
    throw new Error('Checkout URL missing from server response.')
  } catch (error) {
    if (error?.response?.status === 401) {
      alert('Please log in to continue.')
      return
    }
    if (error?.response?.data?.error) {
      alert(error.response.data.error)
      return
    }
    console.error('Stripe checkout error:', error)
    alert('Something went wrong with the payment. Please try again.')
  }
}
</script>

<style scoped>
.subscriptions-page {
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 20px;
  text-align: center;
}

.title {
  font-size: 36px;
  margin-bottom: 10px;
}

.subtitle {
  color: #666;
  margin-bottom: 40px;
}

.plans {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 30px;
}

.plan-card {
  border: 1px solid #e5e5e5;
  border-radius: 12px;
  padding: 30px 20px;
  background: #fff;
  transition:
    transform 0.2s,
    box-shadow 0.2s;
}

.plan-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.plan-card h2 {
  font-size: 24px;
  margin-bottom: 10px;
}

.price {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 20px;
}

.plan-card ul {
  list-style: none;
  padding: 0;
  margin-bottom: 25px;
  text-align: left;
}

.plan-card li {
  margin-bottom: 10px;
}

.btn {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: none;
  font-size: 16px;
  cursor: pointer;
}

.btn.primary {
  background-color: #4f46e5;
  color: white;
}

.btn.primary:hover {
  background-color: #4338ca;
}

.btn.disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.highlighted {
  border: 2px solid #4f46e5;
}
</style>
