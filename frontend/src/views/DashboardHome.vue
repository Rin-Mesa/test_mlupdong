<template>
  <div class="dashboard-home fade-in">
    <!-- Top Stats Row -->
    <div class="stats-grid">
      <div v-for="stat in topStats" :key="stat.label" class="stat-card">
        <div class="stat-header">
          <div :style="{ backgroundColor: stat.bgColor, color: stat.iconColor }" class="stat-icon-box">
            <component :is="stat.icon" :size="20" />
          </div>
          <div v-if="stat.trend" :class="['trend-badge', stat.trendType]">
            <span class="trend-icon">{{ stat.trendType === 'up' ? '↗' : '↘' }}</span>
            {{ stat.trend }}
          </div>
        </div>
        <div class="stat-content">
          <span class="stat-label">{{ stat.label }}</span>
          <h3 class="stat-value">{{ stat.value }}</h3>
        </div>
      </div>
    </div>

    <!-- Main Content Area: Chart + Recent Activity -->
    <div class="main-stats-layout">
      <!-- Sales Overview Card -->
      <div class="chart-card white-card">
        <div class="card-header">
          <div class="header-text">
            <h3>Sales Overview</h3>
            <p>Revenue performance over the last 7 days</p>
          </div>
          <select class="time-filter">
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
          </select>
        </div>
        
        <div class="chart-container">
          <Line :data="chartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Recent Activity Card -->
      <div class="activity-card white-card">
        <div class="card-header">
          <div class="header-text">
            <h3>Recent Activity</h3>
            <p>Live operational updates</p>
          </div>
        </div>
        
        <div class="activity-timeline">
          <div v-for="act in activity" :key="act.id" class="activity-item">
            <div class="timeline-line"></div>
            <div :class="['activity-dot', act.type]"></div>
            <div class="activity-info">
              <p class="activity-msg" v-html="act.message"></p>
              <span class="activity-time">{{ act.time }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Top Selling Dishes -->
    <div class="bottom-card white-card">
      <div class="card-header">
        <div class="header-text">
          <h3>Top 5 Selling Dishes</h3>
          <p>Volume by unit sales this month</p>
        </div>
        <button class="view-all-link">View All</button>
      </div>
      
      <div class="dishes-list">
        <div v-for="dish in topDishes" :key="dish.name" class="dish-item">
          <div class="dish-info">
            <span class="dish-name">{{ dish.name }}</span>
            <span class="dish-units">{{ dish.units }} units</span>
          </div>
          <div class="progress-bar-bg">
            <div class="progress-bar-fill" :style="{ width: dish.percentage + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { 
  DollarSign, 
  ShoppingBag, 
  Star, 
  Armchair 
} from 'lucide-vue-next';

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
);

export default {
  name: 'DashboardHome',
  components: {
    DollarSign, ShoppingBag, Star, Armchair, Line
  },
  data() {
    return {
      stats: null,
      salesData: [],
      bestSellers: [],
      activity: [
        { id: 1, type: 'order', message: '<strong>Table 5</strong> just placed a new order', time: '2 mins ago' },
        { id: 2, type: 'service', message: '<strong>Order #102</strong> has been marked as served', time: '15 mins ago' },
        { id: 3, type: 'alert', message: '<strong>Inventory Alert:</strong> Fresh Coconuts are running low', time: '45 mins ago' }
      ]
    };
  },
  computed: {
    topStats() {
      if (!this.stats) return [];
      return [
        { label: "Today's Revenue", value: `$${this.stats.daily_revenue || 0}`, trend: "+12.5%", trendType: "up", icon: 'DollarSign', bgColor: '#ecfdf5', iconColor: '#10b981' },
        { label: "Total Orders", value: this.stats.total_orders || 0, trend: "+5.2%", trendType: "up", icon: 'ShoppingBag', bgColor: '#fff7ed', iconColor: '#f97316' },
        { label: "Active Tables", value: this.stats.active_tables || 0, icon: 'Armchair', bgColor: '#f5f3ff', iconColor: '#8b5cf6' },
        { label: "Pending", value: this.stats.pending_orders || 0, icon: 'Star', bgColor: '#eff6ff', iconColor: '#3b82f6' }
      ];
    },
    topDishes() {
      return this.bestSellers.map(b => ({
        name: b.menu_item?.name || 'Unknown',
        units: b.total_sold,
        percentage: Math.min((b.total_sold / 100) * 100, 100) // Mocked scale
      }));
    },
    chartData() {
      return {
        labels: this.salesData.map(d => d.date),
        datasets: [
          {
            label: 'Sales ($)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderColor: '#22c55e',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            data: this.salesData.map(d => d.revenue)
          }
        ]
      };
    },
    chartOptions() {
       return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1e293b',
            titleFont: { family: 'Outfit', size: 14, weight: 'bold' },
            bodyFont: { family: 'Outfit', size: 13 },
            padding: 12,
            cornerRadius: 8,
            displayColors: false
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: '#94a3b8', font: { family: 'Outfit', weight: '600' } }
          },
          y: {
            grid: { color: '#f1f5f9' },
            ticks: { color: '#94a3b8', font: { family: 'Outfit', weight: '600' } }
          }
        }
      };
    }
  },
  async created() {
    await this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        const [statsRes, salesRes, bestRes] = await Promise.all([
          axios.get('/api/reports/stats'),
          axios.get('/api/reports/sales?period=daily'),
          axios.get('/api/reports/best-sellers')
        ]);
        this.stats = statsRes.data;
        this.salesData = salesRes.data;
        this.bestSellers = bestRes.data;
      } catch (err) {
        console.error('Stats fetch failed', err);
      }
    }
  }
};
</script>

<style scoped>
.dashboard-home {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Stats Row */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  display: flex;
  flex-direction: column;
  gap: 1rem;
  border: 1px solid #f1f5f9;
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.stat-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.trend-badge {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 8px;
}

.trend-badge.up { color: #10b981; background: #ecfdf5; }

.stat-label {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 500;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 800;
  color: #1e293b;
  margin-top: 4px;
}

/* Main Layout */
.main-stats-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}

.white-card {
  background: white;
  border-radius: 20px;
  padding: 1.5rem;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.header-text h3 {
  font-size: 1.15rem;
  font-weight: 700;
  color: #1e293b;
}

.header-text p {
  font-size: 0.85rem;
  color: #64748b;
  margin-top: 2px;
}

.time-filter {
  padding: 8px 12px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  background: #f8fafc;
  outline: none;
}

/* Chart Container */
.chart-container {
  height: 300px;
  width: 100%;
}

/* Activity Styles */
.activity-timeline {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding-left: 0.5rem;
}

.activity-item {
  position: relative;
  display: flex;
  gap: 1.25rem;
}

.timeline-line {
  position: absolute;
  left: 5px;
  top: 15px;
  bottom: -30px;
  width: 2px;
  background: #f1f5f9;
}

.activity-item:last-child .timeline-line {
  display: none;
}

.activity-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 0 0 1px #f1f5f9;
  position: relative;
  z-index: 1;
  margin-top: 4px;
}

.activity-dot.order { background: #22c55e; }
.activity-dot.service { background: #3b82f6; }
.activity-dot.alert { background: #f97316; }
.activity-dot.request { background: #22c55e; }
.activity-dot.system { background: #64748b; }

.activity-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.activity-msg {
  font-size: 0.9rem;
  color: #334155;
  line-height: 1.4;
}

.activity-time {
  font-size: 0.75rem;
  color: #94a3b8;
}

/* Bottom Dishes Row */
.dishes-list {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.dish-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dish-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.95rem;
  font-weight: 600;
}

.dish-name { color: #334155; }
.dish-units { color: #64748b; }

.progress-bar-bg {
  height: 10px;
  background: #f1f5f9;
  border-radius: 100px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: #22c55e;
  border-radius: 100px;
}

.view-all-link {
  color: #22c55e;
  background: transparent;
  border: none;
  font-weight: 700;
  font-size: 0.85rem;
  cursor: pointer;
}

@media (max-width: 1024px) {
  .main-stats-layout {
    grid-template-columns: 1fr;
  }
}

.fade-in {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
