<template>
  <div class="kds-view">
    <!-- KDS Header -->
    <header class="kds-header">
      <div class="kds-logo">
        <div class="logo-icon">
          <ChefHat :size="24" />
        </div>
        <div class="logo-text">
          <h1>MLUP DONG</h1>
          <span>KDS</span>
        </div>
      </div>

      <nav class="kds-nav">
        <button class="nav-item active">Kitchen View</button>
        <button class="nav-item">History</button>
        <button class="nav-item">Inventory</button>
      </nav>

      <div class="header-right">
        <div class="system-time">
          <span>SYSTEM TIME</span>
          <strong>{{ currentTime }}</strong>
        </div>
        <button class="icon-btn">
          <Bell :size="20" />
          <span class="badge"></span>
        </button>
        <button class="icon-btn"><Settings :size="20" /></button>
        <div class="chef-profile">
          <img src="https://ui-avatars.com/api/?name=Chef+Roth&background=22c55e&color=fff" alt="Chef">
        </div>
      </div>
    </header>

    <!-- Stats Bar -->
    <div class="stats-bar">
      <div class="stat-item active">
        <span class="count">{{ activeOrders.length }}</span>
        <span class="label">ACTIVE ORDERS</span>
      </div>
      <div class="stat-item in-progress">
        <span class="count">{{ inProgressCount }}</span>
        <span class="label">IN PROGRESS</span>
      </div>
      <div class="stat-item overdue">
        <span class="count">{{ overdueCount }}</span>
        <span class="label">OVERDUE</span>
      </div>
      <div class="spacer"></div>
      <div class="avg-prep">
        <Clock :size="16" />
        <span>Avg. Prep: 14m</span>
      </div>
    </div>

    <!-- Kitchen Board -->
    <main class="kds-board" ref="board">
      <div 
        v-for="order in activeOrders" 
        :key="order.id" 
        class="order-card"
        :class="[order.status.toLowerCase(), { 'is-overdue': isOverdue(order) }]"
      >
        <div class="card-header">
          <div class="table-info">
            <h2>TABLE {{ order.table?.number || '??' }}</h2>
            <span class="order-id">ORDER #{{ order.id }} • {{ order.customer_name || 'DINE IN' }}</span>
          </div>
          <div class="order-timer">
            {{ formatTime(order.elapsed) }}
          </div>
        </div>

        <div class="card-body">
          <ul class="items-list">
            <li v-for="item in order.items" :key="item.id">
              <div class="item-row">
                <span class="qty">{{ item.quantity }}x</span>
                <span class="name">{{ item.menu_item.name }}</span>
              </div>
              <p v-if="item.notes" class="notes">{{ item.notes }}</p>
            </li>
          </ul>
        </div>

        <div class="card-footer">
          <button 
            v-if="order.status === 'Pending'" 
            class="action-btn start-btn" 
            @click="updateStatus(order, 'Cooking')"
          >
            START COOKING
          </button>
          <div v-if="order.status === 'Cooking'" class="status-indicator">
            <div class="loading-bar"></div>
            <span>COOKING IN PROGRESS</span>
          </div>
          <button 
            class="action-btn ready-btn" 
            :disabled="order.status === 'Pending'"
            @click="updateStatus(order, 'Ready')"
          >
            READY
          </button>
        </div>
      </div>
    </main>

    <!-- Footer Controls -->
    <footer class="kds-footer">
      <div class="footer-left">
        <button class="filter-btn"><SortAsc :size="18" /> SORT BY TIME</button>
        <button class="filter-btn"><LayoutGrid :size="18" /> BY TABLE</button>
      </div>
      <div class="footer-right">
        <div class="alert-threshold">
          <div class="dot red"></div>
          <span>Alert Threshold: 15m</span>
        </div>
        <button class="mass-action-btn" @click="markAllReady">
          <CheckCircle2 :size="18" />
          MARK ALL READY
        </button>
      </div>
    </footer>
  </div>
</template>

<script>
import { 
  ChefHat, Bell, Settings, Clock, 
  SortAsc, LayoutGrid, CheckCircle2 
} from 'lucide-vue-next';
import axios from 'axios';

export default {
  name: 'KitchenView',
  components: { 
    ChefHat, Bell, Settings, Clock, 
    SortAsc, LayoutGrid, CheckCircle2 
  },
  data() {
    return {
      orders: [],
      currentTime: '00:00:00',
      refreshInterval: null,
      timerInterval: null,
      alertThresholdMinutes: 15
    };
  },
  computed: {
    activeOrders() {
      // Show Pending, Cooking, Ready (until served/cleared)
      return this.orders.filter(o => o.status !== 'Served');
    },
    inProgressCount() {
      return this.orders.filter(o => o.status === 'Cooking').length;
    },
    overdueCount() {
      return this.orders.filter(o => this.isOverdue(o)).length;
    }
  },
  async created() {
    await this.fetchOrders();
    this.startIntervals();
  },
  beforeUnmount() {
    clearInterval(this.refreshInterval);
    clearInterval(this.timerInterval);
  },
  methods: {
    async fetchOrders() {
      try {
        const res = await axios.get('/api/kitchen/orders');
        // Initial elapsed calculation
        this.orders = res.data.map(o => ({
          ...o,
          elapsed: Math.floor((new Date() - new Date(o.created_at)) / 1000)
        }));
      } catch (err) {
        console.error('KDS Fetch Error:', err);
      }
    },
    async updateStatus(order, status) {
      try {
        await axios.patch(`/api/orders/${order.id}/status`, { status });
        order.status = status;
        // If it's Ready, maybe we keep it for a bit or fetch again
        if (status === 'Ready') {
          // In real KDS, it might stay until clicked or timeout
          // For now, let's just refresh
          setTimeout(this.fetchOrders, 1000);
        }
      } catch (err) {
        alert('Update Protocol Failed');
      }
    },
    async markAllReady() {
      const pending = this.activeOrders.filter(o => o.status !== 'Ready');
      for (const order of pending) {
        await this.updateStatus(order, 'Ready');
      }
    },
    startIntervals() {
      // Poll for new orders every 5 seconds
      this.refreshInterval = setInterval(this.fetchOrders, 5000);
      
      // Update timers and system clock every second
      this.timerInterval = setInterval(() => {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('en-GB', { hour12: false });
        
        this.orders.forEach(o => {
          o.elapsed = Math.floor((now - new Date(o.created_at)) / 1000);
        });
      }, 1000);
    },
    formatTime(seconds) {
      const m = Math.floor(seconds / 60).toString().padStart(2, '0');
      const s = (seconds % 60).toString().padStart(2, '0');
      return `${m}:${s}`;
    },
    isOverdue(order) {
      return order.elapsed > this.alertThresholdMinutes * 60;
    }
  }
};
</script>

<style scoped>
.kds-view {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: #0d1a0d; /* Deep dark forest green */
  color: #fff;
  z-index: 1000;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  font-family: 'Outfit', sans-serif;
}

/* Header */
.kds-header {
  height: 70px;
  background: #081008;
  border-bottom: 1px solid #1a2e1a;
  display: flex;
  align-items: center;
  padding: 0 24px;
  gap: 40px;
}

.kds-logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  background: #22c55e;
  padding: 6px;
  border-radius: 8px;
}

.logo-text h1 { font-size: 1rem; font-weight: 800; line-height: 1; }
.logo-text span { font-size: 0.7rem; font-weight: 600; color: #22c55e; letter-spacing: 2px; }

.kds-nav { display: flex; gap: 24px; }
.nav-item {
  background: transparent;
  border: none;
  color: #64748b;
  font-weight: 700;
  font-size: 0.85rem;
  cursor: pointer;
  padding: 8px 0;
  position: relative;
}

.nav-item.active { color: #22c55e; }
.nav-item.active::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0;
  width: 100%; height: 2px;
  background: #22c55e;
}

.header-right { 
  margin-left: auto;
  display: flex; 
  align-items: center; 
  gap: 20px; 
}

.system-time { display: flex; flex-direction: column; align-items: flex-end; }
.system-time span { font-size: 0.55rem; font-weight: 800; color: #64748b; letter-spacing: 1px; }
.system-time strong { font-size: 1.25rem; font-weight: 700; }

.icon-btn {
  background: #1a2e1a;
  border: none;
  color: #94a3b8;
  width: 36px; height: 36px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  position: relative;
}

.badge {
  position: absolute; top: 8px; right: 8px;
  width: 8px; height: 8px;
  background: #22c55e;
  border-radius: 50%;
  border: 2px solid #1a2e1a;
}

.chef-profile img {
  width: 40px; height: 40px;
  border-radius: 12px;
  border: 2px solid #1a2e1a;
}

/* Stats Bar */
.stats-bar {
  background: #0a130a;
  height: 50px;
  padding: 0 24px;
  display: flex;
  align-items: center;
  gap: 32px;
  border-bottom: 1px solid #1a2e1a;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.7rem;
  font-weight: 800;
  letter-spacing: 1px;
}

.stat-item .count { font-size: 1.1rem; }
.stat-item.active { color: #22c55e; }
.stat-item.in-progress { color: #facc15; }
.stat-item.overdue { color: #f87171; }

.avg-prep {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #94a3b8;
  font-size: 0.75rem;
  font-weight: 600;
}

.spacer { flex: 1; }

/* Board */
.kds-board {
  flex: 1;
  padding: 24px;
  display: flex;
  gap: 24px;
  overflow-x: auto;
  align-items: flex-start;
}

.kds-board::-webkit-scrollbar {
  height: 10px;
}
.kds-board::-webkit-scrollbar-thumb {
  background: #1a2e1a;
  border-radius: 5px;
}

.order-card {
  width: 320px;
  background: #112211;
  border-radius: 16px;
  border: 2px solid #1a2e1a;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  max-height: 100%;
}

.order-card.pending { border-color: #facc15; }
.order-card.cooking { border-color: #facc15; }
.order-card.ready { border-color: #22c55e; }
.order-card.is-overdue { border-color: #ef4444; }

.card-header {
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.table-info h2 { font-size: 1.5rem; font-weight: 800; }
.order-id { font-size: 0.65rem; color: #facc15; font-weight: 700; text-transform: uppercase; }

.order-timer {
  background: #1a2e1a;
  padding: 4px 8px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 800;
}

.is-overdue .order-timer { background: #fee2e2; color: #ef4444; }

.card-body {
  flex: 1;
  padding: 0 16px 16px;
  overflow-y: auto;
}

.items-list { list-style: none; }
.items-list li { margin-bottom: 12px; }

.item-row {
  display: flex;
  gap: 12px;
  font-size: 1.1rem;
  font-weight: 700;
}

.qty { color: #22c55e; }
.notes {
  margin-left: 36px;
  color: #f87171;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  font-style: italic;
  background: rgba(248, 113, 113, 0.1);
  padding: 4px 8px;
  border-radius: 4px;
  margin-top: 4px;
}

.card-footer {
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.action-btn {
  width: 100%;
  padding: 14px;
  border-radius: 10px;
  border: none;
  font-weight: 800;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.start-btn { background: #facc15; color: #000; }
.ready-btn { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 2px solid rgba(34, 197, 94, 0.2); }
.ready-btn:not(:disabled) { background: #22c55e; color: #fff; }

.status-indicator {
  padding: 12px;
  text-align: center;
  font-size: 0.7rem;
  font-weight: 800;
  color: #facc15;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.loading-bar {
  height: 4px;
  background: rgba(250, 204, 21, 0.2);
  border-radius: 2px;
  position: relative;
  overflow: hidden;
}

.loading-bar::after {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 50%; height: 100%;
  background: #facc15;
  animation: slide 1.5s infinite;
}

@keyframes slide {
  0% { left: -50%; }
  100% { left: 100%; }
}

/* Footer */
.kds-footer {
  height: 80px;
  background: #0a130a;
  border-top: 1px solid #1a2e1a;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.footer-left, .footer-right { display: flex; gap: 16px; align-items: center; }

.filter-btn {
  background: #1a2e1a;
  border: none;
  color: #fff;
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 10px;
}

.alert-threshold {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  color: #94a3b8;
  font-weight: 600;
}

.dot { width: 10px; height: 10px; border-radius: 50%; }
.dot.red { background: #ef4444; }

.mass-action-btn {
  background: #22c55e;
  border: none;
  color: #fff;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 800;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
}
</style>
