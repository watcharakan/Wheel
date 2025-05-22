import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useCookie } from '@vueuse/integrations/useCookie'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const role = useCookie('role', '')

  function setUser(value) {
    user.value = value
    role.value = value?.role || ''
  }

  return { user, role, setUser }
})
