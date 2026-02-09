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

        <button class="btn" :class="freeButton.class" :disabled="freeButton.disabled">
          {{ freeButton.label }}
        </button>
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

        <button
          class="btn"
          :class="proButton.class"
          :disabled="proButton.disabled"
          @click="proButton.onClick"
        >
          {{ proButton.label }}
        </button>
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

        <button
          class="btn"
          :class="premiumButton.class"
          :disabled="premiumButton.disabled"
          @click="premiumButton.onClick"
        >
          {{ premiumButton.label }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { computed, onMounted } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useNotificationStore } from '@/stores/notificationStore'

const userStore = useUserStore()
const notifications = useNotificationStore()

onMounted(() => {
  userStore.loadUser()
})

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
      notifications.warning('Please log in to continue.')
      return
    }
    if (error?.response?.data?.error) {
      notifications.error(error.response.data.error)
      return
    }
    console.error('Stripe checkout error:', error)
    notifications.error('Something went wrong with the payment. Please try again.')
  }
}

const currentPlan = computed(() => userStore.user?.plan || 'free')

const planRank = (plan) => {
  if (plan === 'premium') return 2
  if (plan === 'pro') return 1
  return 0
}

const freeButton = computed(() => {
  if (currentPlan.value === 'free') {
    return { label: 'Current Plan', disabled: true, class: 'disabled' }
  }
  return { label: 'Free Plan', disabled: true, class: 'disabled' }
})

const proButton = computed(() => {
  if (currentPlan.value === 'pro') {
    return { label: 'Current Plan', disabled: true, class: 'disabled', onClick: () => {} }
  }
  if (planRank(currentPlan.value) > planRank('pro')) {
    return { label: 'Included in Premium', disabled: true, class: 'disabled', onClick: () => {} }
  }
  return {
    label: 'Upgrade to Pro',
    disabled: false,
    class: 'primary',
    onClick: () => subscribe('pro'),
  }
})

const premiumButton = computed(() => {
  if (currentPlan.value === 'premium') {
    return { label: 'Current Plan', disabled: true, class: 'disabled', onClick: () => {} }
  }
  return {
    label: 'Upgrade to Premium',
    disabled: false,
    class: 'primary',
    onClick: () => subscribe('premium'),
  }
})
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
  background-color: #5D3A9B;
  color: white;
}

.btn.primary:hover {
  background-color: #5D3A9B
}

.btn.disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.highlighted {
  border: 2px solid #5D3A9B;
}
</style>
