<template>
  <div class="floor-plan-view fade-in">
    <!-- Floor Plan Header -->
    <div class="floor-header">
      <div class="header-left">
        <h2 class="page-title">Main Dining <span class="highlight">Floor</span></h2>
        <div class="stats-pills">
          <span class="pill available">{{ availableCount }} Available</span>
          <span class="pill occupied">{{ occupiedCount }} Occupied</span>
          <span class="pill ready">{{ readyToServeCount }} Ready to Serve</span>
        </div>
      </div>
      <div class="header-actions">
        <div class="search-box">
          <Search :size="18" />
          <input type="text" placeholder="Search table #..." v-model="searchQuery">
        </div>
        <button class="btn btn-primary">
          <Plus :size="18" />
          New Walk-in
        </button>
      </div>
    </div>

    <div class="main-layout">
      <!-- Areas Sidebar -->
      <aside class="areas-sidebar">
        <h3>AREAS</h3>
        <nav class="areas-nav">
          <button class="area-btn active"><LayoutGrid :size="18" /> Main Dining</button>
          <button class="area-btn"><Home :size="18" /> Terrace</button>
          <button class="area-btn"><User :size="18" /> VIP Room</button>
          <button class="area-btn"><GlassWater :size="18" /> Bar Area</button>
        </nav>

        <div class="goal-card">
          <h4>Today's Goal</h4>
          <div class="progress-container">
            <div class="progress-bar" style="width: 80%"></div>
          </div>
          <p>$1,200 / $1,500 Sales Target</p>
        </div>
      </aside>

      <!-- Tables Grid -->
      <main class="tables-grid-section">
        <div class="tables-grid">
          <div 
            v-for="table in filteredTables" 
            :key="table.id" 
            class="table-card"
            :class="getTableStatusClass(table)"
            @click="selectTable(table)"
          >
            <div class="card-status-badge" v-if="table.active_order?.status === 'Ready'">
              3 ITEMS READY
            </div>
            
            <div class="table-identifier">
              <span class="t-label">T-{{ table.number.padStart(2, '0') }}</span>
              <div class="status-icon">
                <component :is="getStatusIcon(table)" :size="32" />
              </div>
            </div>

            <div class="table-footer">
              <span class="status-text">{{ getStatusText(table) }}</span>
              <span v-if="table.active_order" class="time-elapsed">{{ formatElapsed(table.active_order.created_at) }}</span>
            </div>

            <!-- Hover Actions -->
            <div class="hover-actions" v-if="table.active_order">
              <button 
                v-if="table.active_order.status === 'Ready'" 
                class="action-btn serve" 
                @click.stop="updateOrderStatus(table.active_order, 'Served')"
              >
                <CheckCircle2 :size="14" /> MARK SERVED
              </button>
              <div class="row">
                <button class="action-btn assist" @click.stop="assistTable(table)"><Edit3 :size="12" /> ASSIST</button>
                <button class="action-btn transfer" @click.stop="openTransfer(table)"><Replace :size="12" /> TRANSFER</button>
              </div>
              <button class="action-btn void" @click.stop="voidOrder(table.active_order)"><Lock :size="12" /> VOID/PIN</button>
            </div>
          </div>
        </div>
      </main>

      <!-- Sidebar Queue -->
      <aside class="queue-sidebar">
        <div class="queue-header">
          <h3>Pickup Queue</h3>
          <Bell :size="20" class="notif-icon active" />
        </div>
        <p class="queue-subtitle">{{ readyOrders.length }} orders waiting for you</p>

        <div class="queue-list">
          <div v-for="order in readyOrders" :key="order.id" class="queue-item">
            <div class="item-header">
              <span class="t-num">T-{{ order.table?.number.padStart(2, '0') }}</span>
              <span class="time-ago">2 min ago</span>
            </div>
            <div class="item-content">
              <div v-for="item in order.items" :key="item.id" class="item-line">
                <strong>{{ item.quantity }}x</strong> {{ item.menu_item.name }}
              </div>
            </div>
            <button class="btn btn-primary serve-btn" @click="updateOrderStatus(order, 'Served')">
              SERVED
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="readyOrders.length === 0" class="queue-empty">
            <div class="empty-illu">🍵</div>
            <p>Queue is empty. Enjoy a break!</p>
          </div>
        </div>

        <div class="kitchen-load">
          <div class="load-top">
            <span>Kitchen Load</span>
            <span class="status green">Optimal</span>
          </div>
          <div class="load-bar">
            <div class="load-fill" style="width: 35%"></div>
          </div>
        </div>
      </aside>
    </div>

    <!-- Transfer Modal -->
    <div v-if="transferModal.open" class="modal-overlay" @click="transferModal.open = false">
      <div class="modal-card" @click.stop>
        <h3>Transfer Table</h3>
        <p>Moving <strong>Table {{ transferModal.table.number }}</strong> to:</p>
        <div class="transfer-grid">
          <button 
            v-for="t in availableTables" 
            :key="t.id" 
            class="t-option"
            @click="executeTransfer(t)"
          >
            T-{{ t.number }}
          </button>
        </div>
        <button class="btn-text" @click="transferModal.open = false">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script>
import { 
  Search, Plus, LayoutGrid, Home, User, 
  GlassWater, Armchair, Users, Bell, 
  CheckCircle2, Edit3, Replace, Lock, Clock
} from 'lucide-vue-next';
import axios from 'axios';

export default {
  name: 'FloorPlan',
  components: { 
    Search, Plus, LayoutGrid, Home, User, 
    GlassWater, Armchair, Users, Bell, 
    CheckCircle2, Edit3, Replace, Lock, Clock
  },
  data() {
    return {
      tables: [],
      searchQuery: '',
      loading: true,
      refreshInterval: null,
      transferModal: {
        open: false,
        table: null
      }
    };
  },
  computed: {
    filteredTables() {
      if (!this.searchQuery) return this.tables;
      return this.tables.filter(t => t.number.includes(this.searchQuery));
    },
    availableCount() { return this.tables.filter(t => !t.active_order).length; },
    occupiedCount() { return this.tables.filter(t => t.active_order).length; },
    readyToServeCount() { return this.readyOrders.length; },
    readyOrders() {
      return this.tables
        .filter(t => t.active_order?.status === 'Ready')
        .map(t => t.active_order);
    },
    availableTables() {
      return this.tables.filter(t => !t.active_order);
    }
  },
  async created() {
    await this.fetchTables();
    this.refreshInterval = setInterval(this.fetchTables, 10000); // 10s sync
  },
  beforeUnmount() {
    clearInterval(this.refreshInterval);
  },
  methods: {
    async fetchTables() {
      try {
        const res = await axios.get('/api/tables');
        this.tables = res.data;
        this.loading = false;
      } catch (err) {
        console.error('Table fetch failed', err);
      }
    },
    getTableStatusClass(table) {
      if (!table.active_order) return 'status-available';
      if (table.active_order.status === 'Ready') return 'status-ready';
      return 'status-occupied';
    },
    getStatusIcon(table) {
      if (!table.active_order) return 'Armchair';
      if (table.active_order.status === 'Ready') return 'Bell';
      return 'Users';
    },
    getStatusText(table) {
      if (!table.active_order) return 'AVAILABLE';
      if (table.active_order.status === 'Ready') return 'PICK UP';
      if (table.active_order.status === 'Cooking') return 'COOKING...';
      return 'ORDERING...';
    },
    formatElapsed(startTime) {
      const diff = Math.floor((new Date() - new Date(startTime)) / 1000 / 60);
      return `${diff} Mins Elasped`;
    },
    async updateOrderStatus(order, status) {
      try {
        await axios.patch(`/api/orders/${order.id}/status`, { status });
        await this.fetchTables();
      } catch (err) {
        alert('Status update failed');
      }
    },
    openTransfer(table) {
      this.transferModal.table = table;
      this.transferModal.open = true;
    },
    async executeTransfer(newTable) {
      try {
        await axios.post(`/api/orders/${this.transferModal.table.active_order.id}/transfer`, {
          new_table_id: newTable.id
        });
        this.transferModal.open = false;
        await this.fetchTables();
      } catch (err) {
        alert('Transfer failed');
      }
    },
    async voidOrder(order) {
      if (confirm('Authorize VOID for this order? A manager PIN may be required.')) {
        try {
          await axios.delete(`/api/orders/${order.id}`);
          await this.fetchTables();
        } catch (err) {
          alert('Void sequence failed');
        }
      }
    },
    selectTable(table) {
      // Logic for details view if needed
    },
    assistTable(table) {
      if (table.qr_token) {
        window.open(`/menu/${table.qr_token}`, '_blank');
      } else {
        alert('Table has no QR token. Generate one in Table Manager.');
      }
    }
  }
};
</script>

<style scoped>
.floor-plan-view {
  color: #fff;
  display: flex;
  flex-direction: column;
  gap: 2rem;
  height: calc(100vh - 160px);
}

.highlight { color: #22c55e; }

.floor-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.page-title { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; color: #f8fafc; }
.stats-pills { display: flex; gap: 12px; }
.pill {
  font-size: 0.75rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }

.pill.available { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.pill.available::before { background: #22c55e; }

.pill.occupied { background: rgba(248, 113, 113, 0.1); color: #f87171; }
.pill.occupied::before { background: #f87171; }

.pill.ready { background: rgba(250, 204, 21, 0.1); color: #facc15; }
.pill.ready::before { background: #facc15; }

.header-actions { display: flex; gap: 1.5rem; align-items: center; }

.search-box {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 12px;
  padding: 8px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  width: 250px;
}

.search-box input {
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  font-size: 0.85rem;
  width: 100%;
}

.main-layout {
  display: flex;
  gap: 2rem;
  flex: 1;
  overflow: hidden;
}

/* Areas Sidebar */
.areas-sidebar {
  width: 220px;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.areas-sidebar h3 { font-size: 0.7rem; font-weight: 800; color: #64748b; letter-spacing: 1px; }

.areas-nav { display: flex; flex-direction: column; gap: 8px; }
.area-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 12px 16px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
}

.area-btn.active { background: #22c55e; color: #000; }
.area-btn:hover:not(.active) { background: #1e293b; color: #fff; }

.goal-card {
  margin-top: auto;
  background: rgba(30, 41, 59, 0.5);
  border: 1px solid #334155;
  border-radius: 20px;
  padding: 1.5rem;
}

.goal-card h4 { font-size: 0.85rem; font-weight: 700; margin-bottom: 12px; }
.progress-container { background: #0f172a; height: 8px; border-radius: 4px; margin-bottom: 8px; overflow: hidden; }
.progress-bar { background: #22c55e; height: 100%; border-radius: 4px; }
.goal-card p { font-size: 0.7rem; color: #64748b; font-weight: 600; }

/* Tables Grid */
.tables-grid-section {
  flex: 1;
  background: rgba(15, 23, 42, 0.3);
  border: 1px dashed #334155;
  border-radius: 24px;
  padding: 2rem;
  overflow-y: auto;
}

.tables-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 2rem;
}

.table-card {
  aspect-ratio: 1;
  border-radius: 24px;
  border: 2px solid #1e293b;
  background: #0f172a;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  position: relative;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.table-card:hover { transform: translateY(-5px); }

.status-available { border-color: #1e293b; color: #22c55e; }
.status-occupied { border-color: #f87171; color: #f87171; }
.status-ready { border-color: #facc15; color: #facc15; border-width: 3px; box-shadow: 0 0 20px rgba(250, 204, 21, 0.1); }

.card-status-badge {
  position: absolute;
  top: -12px;
  background: #f87171;
  color: #fff;
  font-size: 0.6rem;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 8px;
  z-index: 10;
}

.status-ready .card-status-badge { background: #f87171; }

.table-identifier { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
.t-label { font-size: 1.1rem; font-weight: 800; color: #fff; opacity: 0.5; }
.status-available .t-label { color: #22c55e; }

.table-footer { text-align: center; }
.status-text { display: block; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px; }
.time-elapsed { font-size: 0.65rem; color: #64748b; font-weight: 600; }

/* Hover Actions */
.hover-actions {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(15, 23, 42, 0.95);
  border-radius: 22px;
  display: flex;
  flex-direction: column;
  padding: 1rem;
  gap: 8px;
  opacity: 0;
  transition: 0.2s;
  z-index: 20;
}

.table-card:hover .hover-actions { opacity: 1; }

.action-btn {
  border: none;
  border-radius: 10px;
  font-weight: 800;
  font-size: 0.7rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: 0.2s;
}

.action-btn.serve { background: #22c55e; color: #000; padding: 12px; }
.hover-actions .row { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
.action-btn.assist { background: #1e293b; color: #22c55e; padding: 10px; }
.action-btn.transfer { background: #1e293b; color: #facc15; padding: 10px; }
.action-btn.void { margin-top: auto; background: rgba(248, 113, 113, 0.1); color: #f87171; padding: 8px; border: 1px solid rgba(248, 113, 113, 0.2); }

/* Queue Sidebar */
.queue-sidebar {
  width: 300px;
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid #1e293b;
  border-radius: 24px;
  display: flex;
  flex-direction: column;
  padding: 2rem;
}

.queue-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.notif-icon.active { color: #22c55e; position: relative; }
.notif-icon.active::after {
  content: ''; position: absolute; top: 0; right: 0; width: 6px; height: 6px; background: #ef4444; border-radius: 50%;
}

.queue-subtitle { font-size: 0.75rem; color: #64748b; font-weight: 600; margin-bottom: 2rem; }

.queue-list { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 1.5rem; }

.queue-item {
  background: #0f172a;
  border-left: 4px solid #facc15;
  border-radius: 12px;
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.item-header { display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 800; }
.t-num { color: #facc15; }
.time-ago { font-size: 0.7rem; color: #64748b; }

.item-content { font-size: 0.8rem; color: #cbd5e1; display: flex; flex-direction: column; gap: 4px; }

.serve-btn { width: 100%; border-radius: 10px; font-weight: 800; font-size: 0.8rem; padding: 10px; }

.queue-empty { text-align: center; padding: 3rem 0; color: #64748b; }
.empty-illu { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.5; }

.kitchen-load { margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #1e293b; }
.load-top { display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; margin-bottom: 8px; }
.load-top .status { text-transform: uppercase; }
.load-bar { height: 6px; background: #0f172a; border-radius: 3px; overflow: hidden; }
.load-fill { background: #22c55e; height: 100%; border-radius: 3px; }

/* Modal */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.8); backdrop-filter: blur(4px);
  z-index: 3000; display: flex; align-items: center; justify-content: center;
}

.modal-card {
  background: #1e293b; width: 400px; padding: 2rem; border-radius: 24px; text-align: center;
}

.transfer-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin: 2rem 0;
}

.t-option {
  background: #0f172a; border: 1px solid #334155; color: #fff; padding: 12px; border-radius: 12px; font-weight: 800; cursor: pointer;
}
.t-option:hover { border-color: #22c55e; color: #22c55e; }

.btn {
  display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 20px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; transition: 0.2s;
}
.btn-primary { background: #22c55e; color: #000; }
.btn-primary:hover { background: #16a34a; transform: translateY(-2px); }
.btn-text { background: transparent; border: none; color: #64748b; font-weight: 700; cursor: pointer; margin-top: 1rem; }

.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
