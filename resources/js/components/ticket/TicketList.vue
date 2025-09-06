<template>
  <div class="ticket-list">
    <FlashMessage v-if="flash.message" 
                  :message="flash.message" 
                  :type="flash.type" />

    <h1>Tickets List</h1>

    <div class="ticket-list__header">
      <input
        type="text"
        v-model="searchTerm"
        placeholder="Search tickets..."
        class="ticket-list__search"
      />
      <button @click="toggleModal" class="btn-primary">
        New Ticket
      </button>
    </div>
    <div class="ticket-list__body">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Manual Category Update</th>
            <th>Confidence</th>
            <th>Explanation</th>
            <th>Note</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(ticket, index) in tickets" :key="index">
            <td>{{ ticket.id }}</td>
            <td>{{ ticket.category }}</td>
            <td>
              <select name="category" @change="updateCategory(ticket.id,$event)">
                <option value="" disabled selected>Select category</option>
                <option 
                  v-for="(cat, idx) in categories" 
                  :key="idx" 
                  :value="cat"
                  :selected="cat === ticket.category"
                >
                  {{ cat }}
                </option>
              </select>
            </td>
            <td>{{ ticket.confidence }}</td>
            <td>
              <span v-if="ticket.explanation" class="tooltip">
                <svg width="25px" height="25px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM288 224C288 206.3 302.3 192 320 192C337.7 192 352 206.3 352 224C352 241.7 337.7 256 320 256C302.3 256 288 241.7 288 224zM280 288L328 288C341.3 288 352 298.7 352 312L352 400L360 400C373.3 400 384 410.7 384 424C384 437.3 373.3 448 360 448L280 448C266.7 448 256 437.3 256 424C256 410.7 266.7 400 280 400L304 400L304 336L280 336C266.7 336 256 325.3 256 312C256 298.7 266.7 288 280 288z"/></svg>          
                <span class="tooltip-text">{{ ticket.explanation }}</span>
              </span>
              <span v-else>-</span>
            </td>
            <td>
              <span v-if="ticket.note" class="badge">Note</span>
              <span v-else>-</span>
            </td>
            <td>
              <button class="btn-primary" @click="goToEdit(ticket.id)">Edit</button>
              &nbsp; | &nbsp;

              <span v-if="ticket.is_classified" class="badge">Ticket is already classified</span>
              <span v-else><button class="btn-primary"
                @click="classifyTicket(ticket.id)" 
                :disabled="isLoading"
              >
                Classify
              </button></span>
              
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="ticket-list__pagination"v-if="pagination.lastPage > 1">    
      <button class=""
        @click="fetchTickets(pagination.currentPage - 1)" 
        :disabled="pagination.currentPage === 1"
      >
        Previous
      </button>

      <span>Page {{ pagination.currentPage }} of {{ pagination.lastPage }}</span>

      <button 
        @click="fetchTickets(pagination.currentPage + 1)" 
        :disabled="pagination.currentPage === pagination.lastPage"
      >
        Next
      </button>
    </div>


    <!-- Modal box-->
    <TicketModal
      :show="showModal"
      :statuses="statuses"
      :ticket="newTicket"
      @close="showModal = false"
      @submit.once="createTicket"
    />
    <div v-if="isLoading" class="spinner-overlay">
      <div class="spinner"></div>
    </div>
  </div>
</template>

<script>
import TicketModal from './TicketModal.vue'
import axios from 'axios';
import FlashMessage from './FlashMessage.vue'

export default {
  name: 'TicketList',
  components: {
    TicketModal,
    FlashMessage 
  },
  data() {
    return {
      tickets: [],
      categories: [],
      searchTerm: "",
      error: null,
      isLoading: false,
      pagination: { currentPage: 1, lastPage: 1, perPage: 10 },
      showModal: false,
      newTicket: {
        subject: '',
        body: '',
        status: 'open',
        note: '',
        category: '',
        explanation: '',
        confidence: '',
      },
      statuses: [],
      flash: {
        message: null,
        type: 'success'
      }
    }
  },
  methods: {
    async updateCategory(id,$event)
    {
      const newCategory = $event.target.value;
      try {
        axios.patch(`/api/tickets/${id}/update-category`, { category: newCategory })
        this.showFlash('Ticket saved successfully!', 'success')
      } catch (err) {
        console.error('Failed to classify:', err)
        this.showFlash('Failed to save ticket.', 'error')
      } finally {
         this.fetchTickets(this.pagination.currentPage, this.searchTerm);
         this.isLoading = false;
      }
    
    },

    async fetchTickets(page = 1, searchQuery = "") {
     
      try {
        const response = await axios.get("/api/tickets/list", {
          params: { search: searchQuery, page },
        });

        this.tickets = response.data.tickets.data ?? [];
        this.categories = response.data.categories ?? [];
        this.statuses = response.data.statuses ?? [];
        this.pagination = {
          currentPage: response.data.tickets.current_page ?? 1,
          lastPage: response.data.tickets.last_page ?? 1,
          perPage: response.data.tickets.per_page ?? 10,
        };
      } catch (err) {
        console.error("Failed to fetch tickets:", err);
        this.error = "Failed to load tickets. Please try again later."
        this.showFlash('Failed to load tickets.', 'error')
      } finally {
        
      }
    },

    showFlash(message, type = 'success') {
      this.flash.message = message
      this.flash.type = type

      // Auto-clear after 3 seconds
      setTimeout(() => {
        this.flash.message = null
      }, 3000)
    },

    goToEdit(id) {
      this.$router.push(`/tickets/${id}`);
    },

    toggleModal() {
      this.showModal = !this.showModal
    },

    async createTicket() {
      try {
        await axios.post('/api/tickets', this.newTicket)       
        this.newTicket = { status: 'open', category: '', note: '' }
        this.toggleModal()
        this.fetchTickets()
        this.showFlash('Ticket created successfully!', 'success')
      } catch (error) {
        console.error(error)
        this.showFlash('Failed to create ticket', 'error')     
      }
    },

    async classifyTicket(id) {
      // mark this row as loading
      this.isLoading = true;

      try {
        await axios.post(`/api/tickets/${id}/classify`)
        this.fetchTickets()
        this.showFlash('Ticket classify successfully!', 'success')
      } catch (err) {
        console.error('Failed to classify:', err)
        this.showFlash('Failed to classify ticket.', 'error')
      } finally {
         this.isLoading = false;
      }
    }
  },
  watch: {
    searchTerm(newVal) {
      if (newVal.length === 0) {
        this.fetchTickets(1, "");
      } else if (newVal.length >= 3) {
        this.fetchTickets(1, newVal);
      }
    },
  },
  mounted() {
    this.fetchTickets()
  }
}

</script>

<style lang="scss" scoped>
  .ticket-list {
    padding: 20px;
    font-family: sans-serif;
    position: relative;

    &__header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }



    &__search {
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      width: 300px;
    }


    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 1rem;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);

      th,
      td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
      }

      th {
        background-color: #f4f7f9;
        font-weight: 600;
        color: #333;
      }
    }

    &__pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      button{
        position: relative;
        display: block;
        padding: .5rem .75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
      }
      button:disabled{
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
      }
      
    }
  }

.tooltip {
  position: relative;
  cursor: pointer;
  font-size: 1.2rem;
}

.tooltip-text {
  visibility: hidden;
  background-color: #333;
  color: #fff;
  padding: 6px 10px;
  border-radius: 4px;
  position: absolute;
  z-index: 10;
  bottom: 125%;
  left: 50%;
  transform: translateX(-50%);   
  width: 300px;
  white-space: normal;       
  word-break: break-word;    
  overflow-wrap: break-word;
}

.tooltip:hover .tooltip-text {
  visibility: visible;
}

.badge {
  display: inline-block;
  background: #42b983;
  color: white;
  padding: 2px 8px;
  font-size: 0.8rem;
  border-radius: 6px;
}

.spinner-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10; 
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-left-color: #333;
  border-radius: 50%;
  width: 1em;
  height: 1em;
  animation: spin 1s linear infinite;
  display: inline-block;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}


</style>