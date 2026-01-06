import { defineStore } from 'pinia'
import api from '@/services/axios'

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    token: localStorage.getItem('access_token') || null,
  }),
  getters: {
    isLoggedIn: (state) => !!state.user,
  },
  actions: {
    async loadUser() {
      if (!this.token) return
      try {
        const response = await api.get('/me', {
          headers: { Authorization: `Bearer ${this.token}` },
        })
        this.user = response.data
      } catch (err) {
        console.error(err)
        this.logout()
      }
    },
    setToken(token) {
      this.token = token
      localStorage.setItem('access_token', token)
    },
    setUser(user) {
      this.user = user
    },
    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('access_token')
    },
  },
})
