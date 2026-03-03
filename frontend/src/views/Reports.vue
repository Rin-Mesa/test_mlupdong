<template>
  <div class="reports-view fade-in">
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">Executive <span class="highlight">Insights</span></h2>
        <p class="page-subtitle">Analyze restaurant performance and financial health</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-secondary"><Download :size="18" /> Export CSV</button>
        <button class="btn btn-primary"><Printer :size="18" /> Print Report</button>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
      <div v-for="stat in quickStats" :key="stat.label" class="stat-box white-card">
        <span class="label">{{ stat.label }}</span>
        <div class="value-row">
          <h3>{{ stat.value }}</h3>
          <span :class="['trend', stat.trendType]">
            <component :is="stat.trendType === 'up' ? 'TrendingUp' : 'TrendingDown'" :size="14" />
            {{ stat.trend }}
          </span>
        </div>
      </div>
    </div>

    <!-- Main Analytics Grid -->
    <div class="analytics-grid">
      <!-- Revenue Chart -->
      <div class="chart-card white-card full-width">
        <div class="card-header">
          <div class="header-text">
            <h3>Revenue Projection</h3>
            <p>Financial performance across selected time interval</p>
          </div>
          <div class="period-tabs">
            <button v-for="p in periods" :key="p" @click="selectedPeriod = p" :class="{ active: selectedPeriod === p }">
              {{ p.toUpperCase() }}
            </button>
          </div>
        </div>
        <div class="chart-wrap">
          <Line :data="salesChartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Best Selling & Peak Hours -->
      <div class="chart-card white-card">
        <div class="card-header">
          <div class="header-text">
            <h3>Best Selling Units</h3>
            <p>Most popular items by volume</p>
          </div>
        </div>
        <div class="chart-wrap">
          <Bar :data="bestSellersData" :options="barOptions" />
        </div>
      </div>

      <div class="chart-card white-card">
        <div class="card-header">
          <div class="header-text">
            <h3>Traffic Analysis</h3>
            <p>Peak operational hours (24h format)</p>
          </div>
        </div>
        <div class="chart-wrap">
          <Line :data="peakHoursData" :options="lineOptions" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Download, Printer, TrendingUp, TrendingDown } from 'lucide-vue-next';
import { Line, Bar } from 'vue-chartjs';
import axios from 'axios';
import {
  Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, 
  BarElement, Title, Tooltip, Legend, Filler
} from 'chart.js';

ChartJS.register(
  CategoryScale, LinearScale, PointElement, LineElement, 
  BarElement, Title, Tooltip, Legend, Filler
);

export default {
  name: 'ReportsView',
  components: { Download, Printer, TrendingUp, TrendingDown, Line, Bar },
  data() {
    return {
      selectedPeriod: 'daily',
      periods: ['daily', 'monthly', 'yearly'],
      stats: null,
      salesData: [],
      bestSellers: [],
      peakHours: [],
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      },
      barOptions: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
      },
      lineOptions: {
        responsive: true,
        maintainAspectRatio: false,
        elements: { line: { tension: 0.4 } },
        plugins: { legend: { display: false } }
      }
    }
  },
  computed: {
    quickStats() {
      if (!this.stats) return [];
      return [
        { label: 'Total Revenue', value: `$${this.stats.daily_revenue.toLocaleString()}`, trend: '+14%', trendType: 'up' },
        { label: 'Avg Order Value', value: '$24.50', trend: '+5%', trendType: 'up' },
        { label: 'Completion Rate', value: '98.2%', trend: '-1%', trendType: 'down' },
        { label: 'Staff Efficiency', value: '88%', trend: '+8%', trendType: 'up' }
      ];
    },
    salesChartData() {
      return {
        labels: this.salesData.map(d => d.date),
        datasets: [{
          label: 'Revenue',
          data: this.salesData.map(d => d.revenue),
          borderColor: '#22c55e',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          fill: true
        }]
      };
    },
    bestSellersData() {
      return {
        labels: this.bestSellers.map(b => b.menu_item?.name || 'Unknown'),
        datasets: [{
          data: this.bestSellers.map(b => b.total_sold),
          backgroundColor: ['#22c55e', '#3b82f6', '#facc15', '#ef4444', '#8b5cf6']
        }]
      };
    },
    peakHoursData() {
      return {
        labels: Array.from({length: 24}, (_, i) => `${i}:00`),
        datasets: [{
          label: 'Orders',
          data: Array.from({length: 24}, (_, h) => {
            const found = this.peakHours.find(p => p.hour === h);
            return found ? found.count : 0;
          }),
          borderColor: '#3b82f6',
          tension: 0.4
        }]
      };
    }
  },
  watch: {
    selectedPeriod() { this.fetchSales(); }
  },
  async created() {
    await Promise.all([
      this.fetchStats(),
      this.fetchSales(),
      this.fetchBestSellers(),
      this.fetchPeakHours()
    ]);
  },
  methods: {
    async fetchStats() {
      const res = await axios.get('/api/reports/stats');
      this.stats = res.data;
    },
    async fetchSales() {
      const res = await axios.get(`/api/reports/sales?period=${this.selectedPeriod}`);
      this.salesData = res.data;
    },
    async fetchBestSellers() {
      const res = await axios.get('/api/reports/best-sellers');
      this.bestSellers = res.data;
    },
    async fetchPeakHours() {
      const res = await axios.get('/api/reports/peak-hours');
      this.peakHours = res.data;
    }
  }
};
</script>

<style scoped>
.reports-view { color: #1e293b; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
.page-title { font-size: 2.25rem; font-weight: 800; color: #1e293b; }
.highlight { color: #22c55e; }
.btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; font-weight: 700; cursor: pointer; border: none; }
.btn-primary { background: #22c55e; color: #000; }
.btn-secondary { background: #f1f5f9; color: #64748b; }

.stats-overview { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
.white-card { background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }

.stat-box .label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; }
.value-row { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
.value-row h3 { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
.trend { font-size: 0.75rem; font-weight: 800; display: flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 8px; }
.trend.up { background: #ecfdf5; color: #10b981; }
.trend.down { background: #fef2f2; color: #ef4444; }

.analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.full-width { grid-column: span 2; }

.card-header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
.header-text h3 { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.header-text p { font-size: 0.85rem; color: #64748b; margin-top: 2px; }

.period-tabs { display: flex; background: #f1f5f9; padding: 4px; border-radius: 12px; }
.period-tabs button { padding: 6px 16px; border-radius: 10px; border: none; background: transparent; color: #64748b; font-size: 0.7rem; font-weight: 800; cursor: pointer; }
.period-tabs button.active { background: white; color: #1e293b; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

.chart-wrap { height: 320px; }

.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
