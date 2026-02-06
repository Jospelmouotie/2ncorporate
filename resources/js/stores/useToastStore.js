import { defineStore } from 'pinia'

export const useToastStore = defineStore('toast', {
  state: () => ({
    message: '',
    type: 'success',
    show: false
  }),
  actions: {
    add(message, type = 'success') {
      // Si un toast est déjà affiché, on le ferme d'abord pour réinitialiser l'animation
      this.show = false;

      setTimeout(() => {
        this.message = message
        this.type = type
        this.show = true
      }, 10);

      // Cache le toast après 3 secondes
      setTimeout(() => {
        this.show = false
      }, 3000)
    }
  }
})
