import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useUserStore = defineStore('user', () => {
  const user = ref(window.user || null)

  const isAdmin = computed(() => {
    return user.value?.is_admin === true || user.value?.is_admin === 1
  })

  const fetchUser = async () => {
    try {
      const response = await axios.get('/api/user')
      user.value = response.data
      window.user = response.data

      // Déclencher un événement pour notifier les composants
      window.dispatchEvent(new CustomEvent('user-updated', {
        detail: { user: user.value }
      }))

      return user.value
    } catch (error) {
      console.log('Not authenticated')
      user.value = null
      window.user = null
      return null
    }
  }

  const setUser = (userData) => {
    user.value = userData
    window.user = userData
    window.dispatchEvent(new CustomEvent('user-updated', {
      detail: { user: userData }
    }))
  }

  const logout = async () => {
    try {
      await axios.post('/api/logout')
      user.value = null
      window.user = null
      return true
    } catch (error) {
      console.error('Logout failed:', error)
      return false
    }
  }

  return {
    user,
    isAdmin,
    fetchUser,
    setUser,
    logout
  }
})
