<template>
  <div class="page-layout">
    <SidebarMenu />
    <div class="billing-page">
      <h1>Billing and payments</h1>

      <div class="billing-card">
        <div class="billing-actions" v-if="showDeactivate">
          <button class="danger" @click="cancelSubscription" :disabled="isCanceling">
            {{ isCanceling ? 'Canceling...' : 'Deactivate subscription' }}
          </button>
        </div>
        <div class="filters">
          <div class="filter">
            <label>Date range</label>
            <select>
              <option>All time</option>
              <option>Last month</option>
              <option>Last year</option>
            </select>
          </div>

          <div class="filter">
            <label>Transaction type</label>
            <select>
              <option>All types</option>
              <option>Payment</option>
              <option>Refund</option>
              <option>Payout</option>
            </select>
          </div>

          <div class="filter">
            <label>Client</label>
            <select>
              <option>All clients / freelancer</option>
            </select>
          </div>

          <div class="filter">
            <label>Contract</label>
            <select>
              <option>All contracts</option>
            </select>
          </div>

          <div class="balance">Balance: <strong>0.00 $</strong></div>
        </div>

        <!-- TABLE -->
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Client/Freelancer</th>
                <th>Amount / Status</th>
                <th>ID</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(item, index) in transactions" :key="index">
                <td>{{ item.date }}</td>
                <td>{{ item.type }}</td>
                <td>{{ item.description }}</td>
                <td>{{ item.party }}</td>
                <td>
                  {{ item.amount || '-' }}
                  <span v-if="item.status" class="status">
                    {{ item.status }}
                  </span>
                </td>
                <td>
                  <span class="tx-id" :title="item.id">{{ formatTxId(item, index) }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <p v-if="!transactions.length" class="empty">No transactions yet</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/axios'
import SidebarMenu from '../../components/FreelancerPageMenu/SidebarMenu.vue'
import { useNotificationStore } from '@/stores/notificationStore'

const notifications = useNotificationStore()
const transactions = ref([])
const isCanceling = ref(false)
const user = ref(null)

const showDeactivate = computed(() => {
  const plan = user.value?.plan
  if (!plan) return false
  return String(plan).toLowerCase() !== 'free'
})

onMounted(async () => {
  try {
    const meRes = await api.get('/me')
    user.value = meRes.data
    const res = await api.get('/billing/transactions')
    transactions.value = res.data || []
  } catch (e) {
    console.error('Failed to load transactions', e)
    notifications.error('Failed to load billing transactions')
  }
})

const cancelSubscription = async () => {
  isCanceling.value = true
  try {
    await api.post('/subscriptions/cancel')
    notifications.success('Subscription deactivated')
    const res = await api.get('/billing/transactions')
    transactions.value = res.data || []
  } catch (e) {
    console.error('Failed to cancel subscription', e)
    notifications.error('Failed to deactivate subscription')
  } finally {
    isCanceling.value = false
  }
}

const formatTxId = (item, index) => {
  if (!item?.id) {
    return `TX-${String(index + 1).padStart(4, '0')}`
  }
  const raw = String(item.id)
  return `TX-${raw.slice(-6)}`
}
</script>

<style scoped>
.page-layout {
  display: flex;
  min-height: 100vh;
}

.billing-page {
  padding: 30px 20px;
  flex: 1;
}

h1 {
  font-size: 32px;
  font-weight: bold;
  margin-bottom: 24px;
}

.billing-card {
  background: #e9e3ff;
  border-radius: 30px;
  padding: 40px;
}

.billing-actions {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 16px;
}

.billing-actions .danger {
  background: #ef4444;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 10px;
  cursor: pointer;
}

.billing-actions .danger:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.filters {
  background: white;
  border-radius: 20px;
  padding: 24px;
  display: grid;
  grid-template-columns: repeat(4, 1fr) auto;
  gap: 20px;
  align-items: end;
  margin-bottom: 30px;
}

.filter label {
  display: block;
  font-size: 14px;
  margin-bottom: 6px;
}

.filter select {
  width: 100%;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
}

.balance {
  background: white;
  padding: 12px 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  font-size: 14px;
}

.table-wrapper {
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  overflow-x: auto;
}

.empty {
  margin-top: 16px;
  color: #888;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

th,
td {
  padding: 10px 14px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  font-weight: 600;
}

.status {
  color: orange;
  margin-left: 6px;
}

.tx-id {
  font-weight: 600;
  letter-spacing: 0.5px;
  color: #4f46e5;
}
</style>
