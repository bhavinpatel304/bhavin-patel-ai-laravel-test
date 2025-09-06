<template>
  <!-- Theme Toggle -->
  <div>
    <button @click="toggleTheme">
      Toggle {{ theme === 'light' ? 'Dark' : 'Light' }} Mode
    </button>
  </div>
  <div :class="['dashboard',theme]">  

    <!-- Cards: Status Counts --> 
    <h2>Count By Status</h2>
    <div class="card" v-for="(count, status) in statusCounts" :key="status">
      <div class="card__header">
        <h3 class="card__title">{{ status }}</h3>
      </div>      
      <div class="card__counter">{{ count }}</div>
    </div>

    <!-- Cards: Status Counts --> 
    <h2>Count By Category</h2>
    <div class="card" v-for="(count, category) in categoryCounts" :key="category">
      <div class="card__header">
        <h3 class="card__title">{{ category }}</h3>
      </div>      
      <div class="card__counter">{{ count }}</div>
    </div>

    <div class="gap" style="background-color: #FFF;"></div>
  
    <!-- Chart -->
    <div style="width: 600px;margin:0 auto;">
      <canvas ref="ticketChart" width=""></canvas>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Chart, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PieController, BarController, BarElement } from "chart.js";
Chart.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PieController, BarController, BarElement);


export default {
  name: "Dashboard",

  data() {
    return {
      theme: "light",
      statusCounts: {},
      categoryCounts: {},
      ticketChart: null, // will hold <canvas> ref
    };
  },

  methods: {
    toggleTheme() {
      this.theme = this.theme === "light" ? "dark" : "light";
      localStorage.setItem('dashboardTheme', this.theme);
    },

    async fetchDashboardData() {
      try {
        const response = await axios.get("/api/dashboard/summary");

        this.statusCounts = response.data.statusCounts ?? {};
        this.categoryCounts = response.data.categoryCounts ?? {};
        this.$nextTick(() => {
          this.renderChart();
        });

       

      } catch (err) {
        console.error("Error fetching dashboard summary:", err)
      }
    },

     renderChart() {
      if (this.chart) {
        this.chart.destroy();
      }
      

      const ctx = this.$refs.ticketChart?.getContext("2d");

      if (!ctx) {
        alert("Canvas not found!");
        return;
      }

      // Get colors based on theme
      const backgroundColors = this.theme === 'dark' 
        ? ['#4F46E5', '#6366F1', '#818CF8', '#93C5FD', '#A5B4FC', '#C7D2FE']
        : ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
      
      const textColor = this.theme === 'dark' ? '#E5E7EB' : '#374151';
      

      this.chart =  new Chart(ctx, {
          type: "pie", // 'bar' or 'pie'
          data: {
            labels: Object.keys(this.statusCounts),
            datasets: [
              {
                label: "Tickets by Status",
                data: Object.values(this.statusCounts),
                backgroundColor: backgroundColors,
                textColor
              },
            ],
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: "bottom" },
            },
          },
        });
    }
  },

  watch: {
    theme() {
      if (this.categoryCounts) {
        this.renderChart();
      }
    }
  },

  mounted() {
    const savedTheme = localStorage.getItem('dashboardTheme')
    if (savedTheme) {
      this.theme = savedTheme;
    }
    this.fetchDashboardData()
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy();
    }
  }
};
</script>


<style lang="scss" scoped>
.dashboard 
{
    padding: 20px;
    font-family: sans-serif;
}

</style>