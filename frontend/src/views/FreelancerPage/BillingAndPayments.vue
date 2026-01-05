<template>
  <div class="page-layout">

    <SidebarMenu />
    <div class="billing-page">
      <h1>Billing and payments</h1>

      <div class="billing-card">
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
                  {{ item.amount }}
                  <span v-if="item.status" class="status">
                    {{ item.status }}
                  </span>
                </td>
                <td>{{ item.id }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import SidebarMenu from '../../components/FreelancerPageMenu/SidebarMenu.vue'
const transactions = [
  {
    date: 'Jun 25, 2026',
    type: 'Subscription payment',
    description: 'Fixed price',
    party: 'freelancer',
    amount: '$25.00',
    id: '152190432',
  },
  {
    date: 'Jun 25, 2026',
    type: 'Top-up',
    description: 'balance',
    party: 'freelancer',
    amount: '$25.00',
    id: '152190432',
  },
  {
    date: 'Jun 25, 2026',
    type: 'Payment',
    description: 'client',
    party: 'David',
    amount: '$100.00',
    status: 'Pending',
    id: '152190432',
  },
  {
    date: 'Jun 25, 2026',
    type: 'Refund',
    description: 'to client',
    party: 'freelancer',
    amount: '$25.00',
    id: '152190432',
  },
  {
    date: 'Jun 25, 2026',
    type: 'Payout',
    description: 'cash',
    party: 'freelancer',
    amount: '$25.00',
    id: '152190432',
  },
]
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
</style>
