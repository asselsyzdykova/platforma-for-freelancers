import { defineStore } from 'pinia'

let toastId = 1

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    toasts: [],
  }),
  actions: {
    push(type, message, timeout = 4000) {
      const id = toastId++
      this.toasts.push({ id, type, message })
      if (timeout) {
        setTimeout(() => this.remove(id), timeout)
      }
    },
    success(message, timeout) {
      this.push('success', message, timeout)
    },
    error(message, timeout) {
      this.push('error', message, timeout)
    },
    info(message, timeout) {
      this.push('info', message, timeout)
    },
    warning(message, timeout) {
      this.push('warning', message, timeout)
    },
    remove(id) {
      this.toasts = this.toasts.filter((toast) => toast.id !== id)
    },
  },
})
