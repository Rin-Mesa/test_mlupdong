<template>
  <div class="status-view">
    <header class="header">
      <router-link :to="menuPath" class="back-btn">
        <ArrowLeft :size="24" />
      </router-link>
      <h1>Order Status</h1>
    </header>

    <div v-if="order" class="status-content">
      <div class="status-card">
        <div class="status-icon-container" :class="order.status.toLowerCase()">
          <div class="pulse-ring"></div>
          <component :is="statusIcon" :size="48" class="status-icon" />
        </div>
        
        <h2 class="status-text">{{ order.status }}</h2>
        <p class="status-desc">{{ statusMessage }}</p>

        <div class="waiting-counter">
          <span class="label">ESTIMATED WAITING TIME</span>
          <div class="timer">
            <span class="minutes">{{ displayMinutes }}</span>
            <span class="separator">:</span>
            <span class="seconds">{{ displaySeconds }}</span>
          </div>
          <p class="timer-sub">Your food is being prepared with love</p>
        </div>
      </div>

      <div class="order-details-card">
        <h3>Order Items</h3>
        <div class="items-list">
          <div v-for="item in order.items" :key="item.id" class="item">
            <span class="qty">{{ item.quantity }}x</span>
            <span class="name">{{ item.menu_item.name }}</span>
            <span class="price">${{ (item.price * item.quantity).toFixed(2) }}</span>
          </div>
        </div>
        <div class="total-row">
          <span>Total</span>
          <span>${{ Number(order.total_amount).toFixed(2) }}</span>
        </div>
      </div>
    </div>
    
    <div v-else class="loading">
      <p>Loading your order details...</p>
    </div>
  </div>
</template>

<script>
import { 
  ArrowLeft, Clock, Flame, 
  CheckCircle, Utensils 
} from 'lucide-vue-next';
import axios from 'axios';

export default {
  name: 'OrderStatus',
  components: { ArrowLeft },
  data() {
    return {
      order: null,
      timeLeft: 0,
      timer: null,
      statusInterval: null
    };
  },
  computed: {
    menuPath() {
      return `/menu/${this.$route.params.token || localStorage.getItem('last_token')}`;
    },
    statusIcon() {
      switch(this.order?.status) {
        case 'Pending': return Clock;
        case 'Cooking': return Flame;
        case 'Ready': return CheckCircle;
        case 'Served': return Utensils;
        default: return Clock;
      }
    },
    statusMessage() {
      switch(this.order?.status) {
        case 'Pending': return 'We have received your order.';
        case 'Cooking': return 'Our chef is currently preparing your meal.';
        case 'Ready': return 'Your food is ready and will be served shortly!';
        case 'Served': return 'Enjoy your meal!';
        default: return '';
      }
    },
    displayMinutes() {
      return Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
    },
    displaySeconds() {
      return (this.timeLeft % 60).toString().padStart(2, '0');
    }
  },
  async created() {
    await this.fetchOrder();
    this.startTimer();
    // Poll for status updates every 10 seconds
    this.statusInterval = setInterval(this.fetchOrder, 10000);
  },
  beforeUnmount() {
    if (this.timer) clearInterval(this.timer);
    if (this.statusInterval) clearInterval(this.statusInterval);
  },
  methods: {
    async fetchOrder() {
      try {
        const res = await axios.get(`/api/orders/${this.$route.params.id}`);
        this.order = res.data;
        
        // Calulate time left based on created_at + estimated_waiting_time
        const created = new Date(this.order.created_at);
        const now = new Date();
        const diffSeconds = Math.floor((now - created) / 1000);
        const totalWaitSeconds = this.order.estimated_waiting_time * 60;
        
        this.timeLeft = Math.max(0, totalWaitSeconds - diffSeconds);
        
        if (this.order.status === 'Served' || this.order.status === 'Ready') {
          this.timeLeft = 0;
        }
      } catch (err) {
        console.error('Error fetching order', err);
      }
    },
    startTimer() {
      this.timer = setInterval(() => {
        if (this.timeLeft > 0) {
          this.timeLeft--;
        }
      }, 1000);
    }
  }
};
</script>

<style scoped>
.status-view {
  padding: 24px;
}

.header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 30px;
}

.header h1 { font-size: 1.25rem; font-weight: 800; }

.back-btn {
  color: #1e293b;
  display: flex;
  align-items: center;
}

.status-card {
  background: white;
  border-radius: 24px;
  padding: 40px 24px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0,0,0,0.03);
  margin-bottom: 24px;
}

.status-icon-container {
  width: 100px;
  height: 100px;
  margin: 0 auto 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.status-icon {
  z-index: 2;
}

.pulse-ring {
  position: absolute;
  width: 100%; height: 100%;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.5; }
  100% { transform: scale(1.5); opacity: 0; }
}

.pending { background: #fef9c3; color: #ca8a04; }
.pending .pulse-ring { border: 4px solid #fef9c3; }

.cooking { background: #ffedd5; color: #ea580c; }
.cooking .pulse-ring { border: 4px solid #ffedd5; }

.ready { background: #dcfce7; color: #16a34a; }
.ready .pulse-ring { border: 4px solid #dcfce7; }

.served { background: #f1f5f9; color: #1e293b; }

.status-text { font-size: 1.5rem; font-weight: 800; margin-bottom: 8px; }
.status-desc { color: #64748b; font-size: 0.9rem; margin-bottom: 32px; }

.waiting-counter {
  border-top: 1px solid #f1f5f9;
  padding-top: 32px;
}

.waiting-counter .label {
  font-size: 0.7rem;
  color: #94a3b8;
  font-weight: 800;
  letter-spacing: 1px;
}

.timer {
  font-size: 3rem;
  font-weight: 800;
  color: #1e293b;
  margin: 10px 0;
  display: flex;
  justify-content: center;
  align-items: center;
}

.timer-sub { font-size: 0.8rem; color: #94a3b8; }

.order-details-card {
  background: white;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

.order-details-card h3 { font-size: 1rem; font-weight: 800; margin-bottom: 16px; }

.items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.item {
  display: flex;
  gap: 10px;
  font-size: 0.9rem;
}

.qty { font-weight: 800; color: var(--primary); }
.name { flex: 1; color: #475569; }
.price { font-weight: 700; color: #1e293b; }

.total-row {
  border-top: 1px dashed #e2e8f0;
  padding-top: 16px;
  display: flex;
  justify-content: space-between;
  font-weight: 800;
  font-size: 1.1rem;
}
</style>
