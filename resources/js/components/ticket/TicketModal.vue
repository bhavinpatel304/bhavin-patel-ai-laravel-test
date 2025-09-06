<template>
  <div v-if="show" class="modal-overlay">
    <div class="modal-content">
      <h2>New Ticket</h2>
      <form>
      
        <div class="ticket-form__group">
          <label class="ticket-form__label" for="subject">subject:</label>
          <input class="ticket-form__input" type="text" id="subject" v-model="ticket.subject" required/>
          <span v-if="errors.subject" style="color:red">{{ errors.subject }}</span>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label" for="body">Body:</label>
          <textarea 
            class="ticket-form__textarea" 
            id="body" 
            v-model="ticket.body" 
            required
          ></textarea>
        </div> 

        <div class="ticket-form__group">
          <label class="ticket-form__label" >Status:</label>
          <select class="ticket-form__select" v-model="ticket.status" required>
            <option v-for="s in statuses" :key="s" :value="s">
              {{ s }}
            </option>
          </select>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label" for="note">Note:</label>
          <textarea 
            class="ticket-form__textarea" 
            id="note" 
            v-model="ticket.note" 
            required
          ></textarea>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label" for=""category>Category:</label>
          <input class="ticket-form__input" type="text" v-model="ticket.category" required />
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label" for="explanation">explanation:</label>
          <textarea 
            class="ticket-form__textarea" 
            id="explanation" 
            v-model="ticket.explanation" 
            required
          ></textarea>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label">confidence:</label>
          <select class="ticket-form__select" v-model="ticket.confidence" required>
            <option key="0" value="0">0</option>
            <option key="1" value="1">1</option>
          </select>
        </div>

        <div style="margin-top: 15px;">
          <button type="button" @click="submitTicket" :disabled="isAddingTicket" class="ticket-form__button">Create</button>
          <button type="button" class="ticket-form__button" @click="$emit('close')">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketModal',
  data() {
    return {
      errors: {},
      isAddingTicket: false,
    }
  },
  props: {
    show: { type: Boolean, required: true },
    statuses: { type: Array, required: true },  
    ticket: { type: Object, required: true }, 
  },
  methods: {
    validateForm() {
      this.errors = {}
      if (!this.ticket.subject) this.errors.subject = 'Subject is required.'
      return Object.keys(this.errors).length === 0
    },
    submitTicket() {
        console.log("in child",this.ticket)
        this.isAddingTicket = true
        // if (this.validateForm()) {
            this.$emit('submit', this.ticket)
            this.show = false
        // }      
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
