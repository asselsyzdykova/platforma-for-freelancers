import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

let echo = null

export function initEcho() {
  if (typeof window === 'undefined') return null
  if (echo) return echo

  window.Pusher = Pusher

  const key = import.meta.env.VITE_PUSHER_APP_KEY
  if (!key) {
    console.warn('Pusher app key not set (VITE_PUSHER_APP_KEY). Live chat disabled.')
    return null
  }

  try {
    echo = new Echo({
      broadcaster: 'pusher',
      key: key,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
      forceTLS: true,
      auth: {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('access_token')}`,
        },
      },
    })
  } catch (err) {
    console.warn('Failed to initialize Echo (live chat disabled):', err)
    return null
  }

  return echo
}

export default function getEcho() {
  return echo
}
