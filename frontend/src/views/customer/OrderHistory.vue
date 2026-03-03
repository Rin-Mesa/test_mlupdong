<template>
  <div class="history-view">
    <header class="header">
      <h1>Order History</h1>
      <p>Recent orders from this table</p>
    </header>

    <div v-if="orders.length > 0" class="orders-list">
      <div v-for="order in orders" :key="order.id" class="order-card" @click="viewOrder(order.id)">
        <div class="order-header">
          <div class="date-info">
            <span class="date">{{ formatDate(order.created_at) }}</span>
            <span class="id">#ORD-{{ order.id }}</span>
          </div>
          <div :class="['status-badge', order.status.toLowerCase()]">
            {{ order.status }}
          </div>
        </div>

        <div class="order-items-preview">
          {{ order.items.map(i => `${i.quantity}x ${i.menu_item.name}`).join(', ') }}
        </div>

        <div class="order-footer">
          <span class="total">${{ Number(order.total_amount).toFixed(2) }}</span>
          <button class="reorder-btn">View Details</button>
        </div>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">📜</div>
      <h3>No orders yet</h3>
      <p>Your order history will appear here once you place an order.</p>
      <router-link :to="menuPath" class="start-btn">Browse Menu</router-link>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'OrderHistory',
  data() {
    return {
      orders: []
    };
  },
  computed: {
    menuPath() {
      return `/menu/${this.$route.params.token}`;
    }
  },
  async created() {
    await this.fetchHistory();
  },
  methods: {
    async fetchHistory() {
      const historyIds = JSON.parse(localStorage.getItem('order_history') || '[]');
      if (historyIds.length === 0) return;

      try {
        const res = await axios.post('/api/orders/history', {
          order_ids: historyIds
        });
        this.orders = res.data;
      } catch (err) {
        console.error('Error fetching history', err);
      }
    },
    formatDate(dateStr) {
      return new Date(dateStr).toLocaleDateString('en-GB', {
        day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
      });
    },
    viewOrder(id) {
      this.$router.push({ name: 'order-status', params: { id } });
    }
  }
};
</script>

<style scoped>
.history-view {
  padding: 24px;
}

.header {
  margin-bottom: 24px;
}

.header h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 4px; }
.header p { color: #94a3b8; font-size: 0.9rem; }

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-card {
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.02);
  border: 1px solid #f1f5f9;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.date-info {
  display: flex;
  flex-direction: column;
}

.date { font-weight: 700; color: #1e293b; font-size: 0.95rem; }
.id { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }

.status-badge {
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 0.7rem;
  font-weight: 800;
  text-transform: uppercase;
}

.pending { background: #fef9c3; color: #ca8a04; }
.cooking { background: #ffedd5; color: #ea580c; }
.ready { background: #dcfce7; color: #16a34a; }
.served { background: #f1f5f9; color: #64748b; }

.order-items-preview {
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 16px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #f8fafc;
  padding-top: 16px;
}

.total { font-size: 1.1rem; font-weight: 800; color: #1e293b; }

.reorder-btn {
  background: #f1f5f9;
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #475569;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon { font-size: 4rem; margin-bottom: 20px; }
.empty-state h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; }
.empty-state p { color: #94a3b8; margin-bottom: 24px; }

.start-btn {
  background: var(--primary);
  color: white;
  text-decoration: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  display: inline-block;
}
</style>
