<template>
  <div class="tickets-page">
    <h2>Support Tickets</h2>

    <div class="filters">
      <select v-model="statusFilter" @change="loadTickets">
        <option value="">All</option>
        <option value="new">New</option>
        <option value="in_progress">In Progress</option>
        <option value="resolved">Resolved</option>
      </select>
    </div>

    <div v-if="tickets.length" class="ticket-grid">
      <TicketCard v-for="ticket in tickets" :key="ticket.id" :ticket="ticket" @resolve="answer"
        @in-progress="setInProgress" />
    </div>

    <p v-else>No tickets found.</p>

    <div class="pagination" v-if="pagination.last_page > 1">
      <button :disabled="pagination.current_page === 1" @click="changePage(pagination.current_page - 1)">
        Prev
      </button>

      <span>
        Page {{ pagination.current_page }} of {{ pagination.last_page }}
      </span>

      <button :disabled="pagination.current_page === pagination.last_page"
        @click="changePage(pagination.current_page + 1)">
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/axios'
import TicketCard from '@/components/Manager/ManagerTicketCard.vue'

const tickets = ref([])
const statusFilter = ref('')
const pagination = ref({
  current_page: 1,
  last_page: 1
})

const loadTickets = async (page = 1) => {
  try {
    const res = await api.get('/manager/tickets', {
      params: {
        status: statusFilter.value,
        page: page
      }
    })

    tickets.value = res.data.data
    pagination.value.current_page = res.data.current_page
    pagination.value.last_page = res.data.last_page

  } catch (err) {
    console.error('Failed to load tickets:', err)
  }
}

const answer = async (ticket) => {
  try {
    await api.put(`/tickets/${ticket.id}`, {
      response: ticket.response,
      status: 'resolved'
    })

    await loadTickets(pagination.value.current_page)
  } catch (err) {
    console.error(err)
  }
}

const setInProgress = async (ticket) => {
  try {
    await api.put(`/tickets/${ticket.id}`, {
      status: 'in_progress'
    })

    await loadTickets(pagination.value.current_page)
  } catch (err) {
    console.error(err)
  }
}

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  pagination.value.current_page = page
  loadTickets(page)
}

onMounted(() => {
  loadTickets()
})
</script>

<style scoped>
.tickets-page {
  padding: 40px;
  max-width: 1000px;
  margin: 0 auto;
}

.ticket-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}


@media (max-width: 992px) {
  .ticket-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .ticket-grid {
    grid-template-columns: 1fr;
  }
}

.tickets-page h2 {
  font-size: 28px;
  margin-bottom: 12px;
}

.filters {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
}

.filters select {
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
}

.pagination {
  margin-top: 32px;
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
}

.pagination button {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination button.active {
  background: #5b3df5;
  color: #fff;
  border-color: #5b3df5;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

p.empty {
  text-align: center;
  margin-top: 40px;
  color: #888;
  font-size: 16px;
}
</style>
