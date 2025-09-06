<template>
  <div v-if="message" class="p-1"
       :class="typeClass">
    {{ message }}
  </div>
</template>

<script>
export default {
  props: {
    message: String,
    type: {
      type: String,
      default: 'success' // success | error | info
    }
  },
  computed: {
    typeClass() {
      switch (this.type) {
        case 'error': return 'bg-red-600 text-white'
        case 'info': return 'bg-blue-600 text-white'
        default: return 'bg-green-600 text-white'
      }
    }
  }
}
</script>

<style>
.p-1{
    padding: 1rem;
}
  .bg-red-600{
    background-color: #b02a37;
  }
  .bg-blue-600{
    background-color: #0a58ca;
  }
  .bg-green-600{
    background-color: #146c43
  }
  .text-white{
    color:#FFF;
  }
</style>
