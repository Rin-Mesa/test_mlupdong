<template>
  <div class="table-qr">
    <button 
      @click="generateQr" 
      :disabled="loading || qrUrl" 
      :class="['deploy-btn', { 'is-loading': loading, 'is-success': qrUrl }]"
    >
      <div v-if="loading" class="mini-spinner"></div>
      <svg v-else-if="qrUrl" class="status-icon" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <svg v-else class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke-dasharray="2 2"/>
        <path d="M12 8V16M8 12H16" stroke-linecap="round"/>
      </svg>
      <span>{{ loading ? 'Synchronizing...' : (qrUrl ? 'Asset Deployed' : 'Deploy QR') }}</span>
    </button>
    
    <transition name="fade">
      <div v-if="qrUrl" class="deployment-meta">
        <div class="success-pill">
          <div class="pulse-dot"></div>
          <span>Sync Active</span>
        </div>
        <button @click="regenerateQr" class="refresh-link" title="Re-deploy asset">
          <svg viewBox="0 0 20 20" fill="currentColor" width="12"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg>
          Update
        </button>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ['tableId', 'currentQr'],
  emits: ['generated'],
  data() {
    return {
      qrUrl: this.currentQr || null,
      loading: false,
    };
  },
  mounted() {
    if (!this.qrUrl) {
      this.getQr();
    }
  },
  methods: {
    async generateQr() {
      this.loading = true;
      try {
        const response = await axios.post(`/api/tables/${this.tableId}/generate-qr`);
        this.qrUrl = response.data.qr_code_url;
        this.$emit('generated', this.qrUrl);
      } catch (error) {
        console.error('Error generating QR:', error);
        alert('Link protocol failed. Verify backend connection.');
      } finally {
        this.loading = false;
      }
    },
    async regenerateQr() {
      if (!confirm('Re-deploying will overwrite the current asset. Proceed?')) return;
      this.qrUrl = null;
      await this.generateQr();
    },
    async getQr() {
      try {
        const response = await axios.get(`/api/tables/${this.tableId}/qrcode`);
        this.qrUrl = response.data.qr_code_url;
        if (this.qrUrl) this.$emit('generated', this.qrUrl);
      } catch (error) {
        // Silent fail if no QR yet
      }
    }
  },
};
</script>

<style scoped>
.table-qr {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  width: 100%;
}

.deploy-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  background: #f8fafc;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.deploy-btn:hover:not(:disabled) {
  background: #f1f5f9;
  border-color: #cbd5e1;
  color: #1e293b;
}

.is-success {
  background: #ecfdf5;
  border-color: #d1fae5;
  color: #10b981;
}

.is-success:hover {
  background: #d1fae5;
  color: #059669;
}

.mini-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid #f1f5f9;
  border-top-color: #22c55e;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.status-icon { width: 14px; height: 14px; }

.deployment-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 4px;
}

.success-pill {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.65rem;
  font-weight: 700;
  color: #10b981;
  text-transform: uppercase;
}

.pulse-dot {
  width: 6px; height: 6px;
  background: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.refresh-link {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 0.65rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 4px;
  cursor: pointer;
  text-transform: uppercase;
}

.refresh-link:hover { color: #1e293b; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
