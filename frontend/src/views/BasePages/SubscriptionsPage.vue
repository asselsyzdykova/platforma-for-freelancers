<template>
  <div class="subscriptions-page">
    <h1 class="title">Choose Your Plan</h1>
    <p class="subtitle">Upgrade your account to get more opportunities as a freelancer.</p>

    <div class="plans-grid">
      <SubscriptionCard
        name="Free"
        price="0"
        :features="[
          { text: 'Create profile' },
          { text: 'Browse projects' },
          { text: '3 Proposals / month', disabled: true },
          { text: 'No priority listing', disabled: true }
        ]"
        :buttonLabel="freeButton.label"
        :buttonClass="freeButton.class"
        :disabled="freeButton.disabled"
      />

      <SubscriptionCard
        name="Pro"
        price="15"
        isHighlighted
        :features="[
          { text: 'Unlimited proposals' },
          { text: 'Pro Badge' },
          { text: 'Search Boost' },
          { text: 'Email Job Alerts' }
        ]"
        :buttonLabel="proButton.label"
        :buttonClass="proButton.class"
        :disabled="proButton.disabled"
        :loading="loading"
        @subscribe="proButton.onClick"
      />

      <SubscriptionCard
        name="Premium"
        price="30"
        :features="[
          { text: '15 min Early Access' },
          { text: 'Verified Status' },
          { text: 'Featured Profile' },
          { text: 'Priority Support' },
          { text: 'All Pro Features' }
        ]"
        :buttonLabel="premiumButton.label"
        :buttonClass="premiumButton.class"
        :disabled="premiumButton.disabled"
        :loading="loading"
        @subscribe="premiumButton.onClick"
      />
    </div>
  </div>
</template>

<script setup>
import api from '@/services/axios'
import { computed, onMounted, ref } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useNotificationStore } from '@/stores/notificationStore'
import SubscriptionCard from '@/components/UI/SubscriptionCard.vue'

const userStore = useUserStore()
const notifications = useNotificationStore()
const loading = ref(false)
onMounted(() => {
  userStore.loadUser()
})

const subscribe = async (plan) => {
  loading.value = true
  try {
    const { data } = await api.post('/subscriptions/create-checkout', { plan })
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
  } finally {
    loading.value = false
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
    return { label: 'Current Plan', disabled: true, class: 'disabled', onClick: () => { } }
  }
  if (planRank(currentPlan.value) > planRank('pro')) {
    return { label: 'Included in Premium', disabled: true, class: 'disabled', onClick: () => { } }
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
    return { label: 'Current Plan', disabled: true, class: 'disabled', onClick: () => { } }
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
.title{
  text-align: center;
}
.subtitle{
  text-align: center;
}
.plans-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  align-items: stretch;
  margin: 50px auto 0;
  max-width: 1000px;
}

@media (max-width: 980px) {
  .plans-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    justify-content: center;
  }
}

@media (max-width: 640px) {
  .plans {
    grid-template-columns: 1fr;
  }
}

</style>
