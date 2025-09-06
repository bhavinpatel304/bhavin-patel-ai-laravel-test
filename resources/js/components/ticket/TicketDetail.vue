<template>
  <div class="ticket-form">
      <h1 class="ticket-form__title">Edit New Ticket</h1>
      <form class="ticket-form__form" @submit.prevent="updateTicket">

        <div class="ticket-form__group">
          <label class="ticket-form__label">Subject:</label>
          <b>{{ ticket.subject }}</b>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label">Body:</label>
          <b>{{ ticket.body }}</b>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label">Explanation:</label>
          <b>{{ ticket.explanation }}</b>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label">Confidence:</label>
          <b>{{ ticket.confidence }}</b>
        </div>

        <div class="ticket-form__group">
          <label class="ticket-form__label" for="status">Status:</label>
          <select 
              class="ticket-modal__input" 
              id="status"
              v-model="ticket.status" 
              required
            >
              <option v-for="s in statuses" :key="s" :value="s">
                {{ s }}
              </option>
            </select>
        </div> 

        <div class="ticket-form__group">
          <label class="ticket-form__label" for="status">Category:</label>
          <select 
              class="ticket-modal__input" 
              id="status"
              v-model="ticket.category" 
              required
            >
              <option v-for="c in categories" :key="c" :value="c">
                {{ c }}
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
     
        <div class="ticket-form__actions">
          <button class="ticket-form__button" type="submit">Submit</button>
          <button v-if="!ticket.is_classified" class="ticket-form__button bg-yellow-600" @click="runClassification" type="button">Run Classification</button>
        </div>
        
      </form>
    </div>

</template>

<script>

import axios from 'axios'

export default {
  name: 'TicketDetail',
  data() {
    return {
      ticketId: null,
      ticket: {
        status: '',
        category: '',
        note: ''
      },
      statuses: [],
      categories: [],
      searchTerm: '',
    }
  },
  created() {
    this.fetchTicketDetail()
  },
  methods: {
    async fetchTicketDetail()  {
      this.ticketId = this.$route.params.id;
      try {
        const response = await axios.get(`/api/tickets/${this.ticketId}`)
        this.ticket = response.data.ticket;
        this.statuses = response.data.statuses;
        this.categories = response.data.categories;
      } catch (error) {
        console.error('Error fetching ticket details:', error);
      }
    },
    async updateTicket(){
      await axios.patch(`/api/tickets/${this.ticketId}`, this.ticket)
        .then(response => {
          this.$router.push({
            path: '/tickets',
            state: { flash: { message: 'Ticket updated successfully!', type: 'success' } }
          });
           
        })
        .catch(error => {
          console.error('Error updating ticket:', error)
          alert('Failed to update ticket.');
        });
    },

    async runClassification(){
      await axios.post(`/api/tickets/${this.ticketId}/classify`)
        .then(response => {
          alert('Classification run successfully!');
          this.$router.push({
            path: '/tickets',
            state: { flash: { message: 'Ticket is classified', type: 'success' } }
          });
        })
        .catch(error => {
          console.error('Error running classification:', error)
          alert('Failed to run classification.');
        });
    }
  },
  mounted() {
    this.fetchTicketDetail();
  }
};
</script>

<style>
.ticket-form {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);

  &__title {
    text-align: center;
    color: #35495e;
    margin-bottom: 25px;
  }

  &__form {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  &__group {
    display: flex;
    flex-direction: column;
  }

  &__label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #35495e;
  }

  &__input,
  &__textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box;
  }
  
  &__textarea {
    min-height: 120px;
    resize: vertical;
  }

  &__actions {
    text-align: center;
  }
  
  &__submit-button {
    padding: 12px 30px;
    border-radius: 8px;
    border: none;
    background-color: #42b983;
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.2s;

    &:hover {
      background-color: #35495e;
    }
  }
}
</style>