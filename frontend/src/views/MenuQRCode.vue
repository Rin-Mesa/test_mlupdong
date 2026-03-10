<template>
  <div class="qr-page">

    <!-- ── Hero Header ─────────────────────────────────────────── -->
    <div class="hero-header">
      <div class="hero-bg-dots"></div>
      <div class="hero-content">
        <div class="hero-left">
          <div class="hero-badge">
            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 4.5A2.5 2.5 0 014.5 2h2A2.5 2.5 0 019 4.5v2A2.5 2.5 0 016.5 9h-2A2.5 2.5 0 012 6.5v-2zm9 0A2.5 2.5 0 0113.5 2h2A2.5 2.5 0 0118 4.5v2A2.5 2.5 0 0115.5 9h-2A2.5 2.5 0 0111 6.5v-2zm-9 9A2.5 2.5 0 014.5 11h2A2.5 2.5 0 019 13.5v2A2.5 2.5 0 016.5 18h-2A2.5 2.5 0 012 15.5v-2zm9.22-.53a.75.75 0 011.06 0l.97.97.97-.97a.75.75 0 011.06 0l.97.97.97-.97a.75.75 0 011.06 1.06l-.97.97.97.97a.75.75 0 01-1.06 1.06l-.97-.97-.97.97a.75.75 0 01-1.06-1.06l.97-.97-.97-.97a.75.75 0 010-1.06z"/></svg>
            QR Management
          </div>
          <h1 class="hero-title">Table <span class="hero-accent">QR Generator</span></h1>
          <p class="hero-sub">Generate scannable codes for every table. Customers scan → menu opens instantly on their phone.</p>

          <!-- Stats -->
          <div class="hero-stats">
            <div class="stat-pill">
              <span class="stat-val">{{ tables.length }}</span>
              <span class="stat-label">Total Tables</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-pill">
              <span class="stat-val green">{{ readyCount }}</span>
              <span class="stat-label">QR Ready</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-pill">
              <span class="stat-val orange">{{ pendingCount }}</span>
              <span class="stat-label">Pending</span>
            </div>
          </div>
        </div>

        <div class="hero-right">
          <!-- Phone mockup -->
          <div class="phone-mockup">
            <div class="phone-screen">
              <div class="mock-topbar">
                <div class="mock-dot"></div>
                <span>MLup Dong Menu</span>
              </div>
              <div class="mock-qr-area">
                <svg viewBox="0 0 80 80" fill="none" stroke="white" stroke-width="2" opacity="0.9">
                  <rect x="4" y="4" width="28" height="28" rx="3"/>
                  <rect x="10" y="10" width="16" height="16" rx="1" fill="white" stroke="none" opacity="0.3"/>
                  <rect x="48" y="4" width="28" height="28" rx="3"/>
                  <rect x="54" y="10" width="16" height="16" rx="1" fill="white" stroke="none" opacity="0.3"/>
                  <rect x="4" y="48" width="28" height="28" rx="3"/>
                  <rect x="10" y="54" width="16" height="16" rx="1" fill="white" stroke="none" opacity="0.3"/>
                  <path d="M48 48h8M60 48h8M72 48h4M48 56h4M56 56h4M64 56h4M72 56h4M48 64h8M60 64h4M48 72h4M56 72h4M64 72h8" stroke-linecap="round"/>
                </svg>
                <p class="mock-hint">Scan to order</p>
              </div>
              <div class="mock-menu-items">
                <div class="mock-item" v-for="n in 3" :key="n"></div>
              </div>
            </div>
            <div class="phone-notch"></div>
            <div class="phone-btn phone-btn-right"></div>
            <div class="phone-btn phone-btn-left1"></div>
            <div class="phone-btn phone-btn-left2"></div>
          </div>
        </div>
      </div>

      <!-- Action bar inside hero -->
      <div class="hero-actions">
        <div class="search-wrap">
          <svg viewBox="0 0 20 20" fill="currentColor" class="search-icon"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
          <input v-model="search" type="text" placeholder="Search tables..." class="search-input">
        </div>
        <button class="hero-btn" @click="generateAll" :disabled="generatingAll || tables.length === 0">
          <span class="spin-icon-dark" v-if="generatingAll"></span>
          <svg v-else viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;">
            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
          </svg>
          {{ generatingAll ? 'Generating...' : 'Generate All QR Codes' }}
        </button>
      </div>
    </div>

    <!-- ── Content ──────────────────────────────────────────────── -->
    <div class="page-body">

      <!-- How it works -->
      <div class="steps-row">
        <div class="step-card" v-for="step in steps" :key="step.n">
          <div class="step-icon-wrap" :style="{ background: step.bg }">
            <span v-html="step.icon"></span>
          </div>
          <div class="step-n">Step {{ step.n }}</div>
          <p class="step-text">{{ step.text }}</p>
        </div>
        <div class="step-connector" v-for="i in 3" :key="'c'+i"></div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="center-state">
        <div class="pulse-rings">
          <div class="ring"></div><div class="ring"></div><div class="ring"></div>
        </div>
        <p>Loading tables from database...</p>
      </div>

      <!-- Error -->
      <div v-else-if="fetchError" class="center-state">
        <div class="error-icon-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:32px;color:#ef4444">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <p class="error-text">{{ fetchError }}</p>
        <button class="btn btn-primary" @click="fetchTables">Retry Connection</button>
      </div>

      <!-- Empty -->
      <div v-else-if="tables.length === 0" class="center-state">
        <div class="empty-visual">
          <svg viewBox="0 0 80 80" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="width:80px">
            <rect x="6" y="6" width="30" height="30" rx="4"/>
            <rect x="44" y="6" width="30" height="30" rx="4"/>
            <rect x="6" y="44" width="30" height="30" rx="4"/>
            <rect x="44" y="44" width="30" height="30" rx="4" stroke-dasharray="4 3"/>
          </svg>
        </div>
        <h3>No tables registered</h3>
        <p>Go to <strong>Tables</strong> to register tables first.</p>
      </div>

      <!-- Tables Grid -->
      <div v-else class="tables-grid">
        <transition-group name="card-pop" appear>
          <div
            v-for="(table, idx) in filteredTables"
            :key="table.id"
            class="table-card"
            :class="{ 'has-qr': table.qr_code_path }"
            :style="{ '--delay': idx * 0.05 + 's' }"
          >
            <!-- Glow effect shown when QR is ready -->
            <div v-if="table.qr_code_path" class="card-glow"></div>

            <!-- Header -->
            <div class="tc-header">
              <div class="tc-table-id">
                <div class="tc-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px">
                    <rect x="3" y="8" width="18" height="10" rx="2"/>
                    <path d="M6 8V6a2 2 0 012-2h8a2 2 0 012 2v2"/>
                    <line x1="6" y1="13" x2="18" y2="13"/>
                  </svg>
                </div>
                <div>
                  <span class="tc-label">Table</span>
                  <span class="tc-num">{{ table.number }}</span>
                </div>
              </div>
              <div :class="['tc-badge', table.qr_code_path ? 'badge-ready' : 'badge-pending']">
                <span class="badge-dot"></span>
                {{ table.qr_code_path ? 'Active' : 'No QR' }}
              </div>
            </div>

            <!-- QR Zone -->
            <div class="tc-qr-zone" @click="table.qr_code_path && openPreview(table)">
              <transition name="qr-reveal" mode="out-in">
                <div v-if="table.qr_code_path" key="has" class="qr-display">
                  <div class="qr-corner tl"></div>
                  <div class="qr-corner tr"></div>
                  <div class="qr-corner bl"></div>
                  <div class="qr-corner br"></div>
                  <img
                    :src="backendUrl + '/' + table.qr_code_path"
                    :alt="`QR Table ${table.number}`"
                    class="qr-img"
                  >
                  <div class="qr-hover-overlay">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:28px">
                      <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                      <path d="M11 8v6M8 11h6"/>
                    </svg>
                    <span>Click to Preview</span>
                  </div>
                </div>
                <div v-else key="empty" class="qr-placeholder-zone">
                  <div class="qr-placeholder-icon">
                    <svg viewBox="0 0 60 60" fill="none" stroke="currentColor" stroke-width="1.5">
                      <rect x="4" y="4" width="22" height="22" rx="2"/>
                      <rect x="34" y="4" width="22" height="22" rx="2"/>
                      <rect x="4" y="34" width="22" height="22" rx="2"/>
                      <rect x="10" y="10" width="10" height="10" rx="1" fill="currentColor" stroke="none" opacity="0.12"/>
                      <rect x="40" y="10" width="10" height="10" rx="1" fill="currentColor" stroke="none" opacity="0.12"/>
                      <rect x="10" y="40" width="10" height="10" rx="1" fill="currentColor" stroke="none" opacity="0.12"/>
                      <path d="M34 34h6M44 34h6M50 34h6M34 42h6M34 50h6M42 42h6M50 42h6M44 50h8" stroke-linecap="round" stroke-width="2"/>
                    </svg>
                  </div>
                  <span class="placeholder-label">No QR Code</span>
                  <span class="placeholder-sub">Click generate below</span>
                </div>
              </transition>
            </div>

            <!-- URL chip -->
            <div v-if="table.qr_token" class="url-chip">
              <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" style="width:12px; flex-shrink:0">
                <path d="M10 6H6a2 2 0 100 4h4M6 10h4a2 2 0 100-4H6"/>
              </svg>
              <span class="url-text">/menu/{{ table.qr_token.slice(0,8) }}...</span>
            </div>

            <!-- Actions -->
            <div class="tc-actions">
              <button
                class="btn btn-gen"
                :class="{ 'btn-regen': table.qr_code_path }"
                @click="generateQr(table)"
                :disabled="table._generating"
              >
                <span class="spin-icon" v-if="table._generating"></span>
                <svg v-else viewBox="0 0 20 20" fill="currentColor" style="width:14px; height:14px;">
                  <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                </svg>
                {{ table._generating ? 'Generating...' : (table.qr_code_path ? 'Regenerate' : 'Generate QR') }}
              </button>

              <a
                v-if="table.qr_code_path"
                :href="backendUrl + '/' + table.qr_code_path"
                :download="`table-${table.number}-qr.svg`"
                class="btn-icon-sq" title="Download"
              >
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;">
                  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </a>
            </div>

            <!-- Error -->
            <div v-if="table._error" class="tc-error">⚠ {{ table._error }}</div>

          </div>
        </transition-group>
      </div>
    </div>

    <!-- ── Preview Modal ─────────────────────────────────────────── -->
    <transition name="modal-pop">
      <div v-if="previewTable" class="modal-backdrop" @click.self="closePreview">
        <div class="preview-modal">

          <!-- Modal left: large QR -->
          <div class="modal-left">
            <div class="modal-qr-frame">
              <div class="modal-qr-corners">
                <div></div><div></div><div></div><div></div>
              </div>
              <img
                :src="backendUrl + '/' + previewTable.qr_code_path"
                :alt="`QR Table ${previewTable.number}`"
                class="modal-qr-img"
              >
            </div>
            <div class="modal-table-label">Table <strong>{{ previewTable.number }}</strong></div>
            <div class="modal-restaurant-name">ML Up Dong Restaurant</div>
          </div>

          <!-- Modal right: info + actions -->
          <div class="modal-right">
            <div class="modal-top-row">
              <div>
                <h3 class="modal-title">QR Code Preview</h3>
                <p class="modal-subtitle">Ready for print & display</p>
              </div>
              <button class="close-btn" @click="closePreview">&times;</button>
            </div>

            <div class="modal-section">
              <p class="modal-field-label">Scan URL</p>
              <div class="modal-url-box">{{ menuUrl(previewTable.qr_token) }}</div>
            </div>

            <div class="modal-section">
              <p class="modal-field-label">How customers use it</p>
              <div class="modal-steps">
                <div class="modal-step"><span>1</span> Open camera app</div>
                <div class="modal-step"><span>2</span> Aim at QR code</div>
                <div class="modal-step"><span>3</span> Tap the link that appears</div>
                <div class="modal-step"><span>4</span> Browse menu & order 🎉</div>
              </div>
            </div>

            <div class="modal-actions">
              <a
                :href="backendUrl + '/' + previewTable.qr_code_path"
                :download="`table-${previewTable.number}-qr.svg`"
                class="btn btn-primary"
              >
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:15px"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                Download SVG
              </a>
              <button class="btn btn-ghost" @click="generateQr(previewTable); closePreview()">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:15px"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg>
                Regenerate
              </button>
            </div>
          </div>

        </div>
      </div>
    </transition>

  </div>
</template>

<script>
import axios from 'axios';

const BACKEND_URL = 'http://127.0.0.1:8000';
const FRONTEND_URL = 'http://localhost:5173';

export default {
  name: 'MenuQRCode',
  data() {
    return {
      tables: [],
      loading: true,
      fetchError: null,
      generatingAll: false,
      previewTable: null,
      search: '',
      backendUrl: BACKEND_URL,
      steps: [
        { n: 1, text: 'Click Generate QR for any table', bg: '#ecfdf5', icon: '<svg viewBox="0 0 20 20" fill="#22c55e" style="width:20px"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/></svg>' },
        { n: 2, text: 'Code is saved with a unique secure token', bg: '#eff6ff', icon: '<svg viewBox="0 0 20 20" fill="#3b82f6" style="width:20px"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' },
        { n: 3, text: 'Download & print for table placement', bg: '#fdf4ff', icon: '<svg viewBox="0 0 20 20" fill="#a855f7" style="width:20px"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>' },
        { n: 4, text: 'Customer scans → menu on their phone', bg: '#fff7ed', icon: '<svg viewBox="0 0 20 20" fill="#f97316" style="width:20px"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>' },
      ],
    };
  },
  computed: {
    readyCount()   { return this.tables.filter(t => t.qr_code_path).length; },
    pendingCount() { return this.tables.filter(t => !t.qr_code_path).length; },
    filteredTables() {
      if (!this.search.trim()) return this.tables;
      return this.tables.filter(t =>
        String(t.number).toLowerCase().includes(this.search.toLowerCase())
      );
    },
  },
  async created() {
    await this.fetchTables();
  },
  methods: {
    menuUrl(token) {
      return token ? `${FRONTEND_URL}/menu/${token}` : '(not generated yet)';
    },
    async fetchTables() {
      this.loading = true;
      this.fetchError = null;
      try {
        const res = await axios.get('/api/tables');
        this.tables = res.data.map(t => ({ ...t, _generating: false, _error: null }));
      } catch {
        this.fetchError = 'Failed to load tables. Is the backend running?';
      } finally {
        this.loading = false;
      }
    },
    async generateQr(table) {
      table._generating = true;
      table._error = null;
      try {
        await axios.post(`/api/tables/${table.id}/generate-qr`);
        const res = await axios.get('/api/tables');
        const fresh = res.data.find(t => t.id === table.id);
        if (fresh) {
          Object.assign(table, { ...fresh, _generating: false, _error: null });
        }
      } catch (err) {
        table._error = err.response?.data?.message || 'Generation failed.';
      } finally {
        table._generating = false;
      }
    },
    async generateAll() {
      this.generatingAll = true;
      try {
        await axios.post('/api/tables/generate-all');
        await this.fetchTables();
      } catch {
        alert('Bulk generation failed.');
      } finally {
        this.generatingAll = false;
      }
    },
    openPreview(table)  { this.previewTable = table; },
    closePreview()      { this.previewTable = null; },
  },
};
</script>

<style scoped>
/* ═══════════════════════════════════════════
   BASE
═══════════════════════════════════════════ */
.qr-page { color: #1e293b; }

/* ═══════════════════════════════════════════
   HERO HEADER
═══════════════════════════════════════════ */
.hero-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f2027 100%);
  border-radius: 28px;
  padding: 2.5rem 2.5rem 0;
  margin-bottom: 2rem;
  position: relative;
  overflow: hidden;
}

.hero-bg-dots {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, rgba(34,197,94,0.08) 1px, transparent 1px);
  background-size: 28px 28px;
  pointer-events: none;
}

/* Glowing accent blobs */
.hero-header::before {
  content: '';
  position: absolute;
  width: 320px; height: 320px;
  background: radial-gradient(circle, rgba(34,197,94,0.15), transparent 70%);
  top: -80px; right: 120px;
  pointer-events: none;
}

.hero-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
  padding-bottom: 2rem;
}

/* Badge */
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(34,197,94,0.12);
  border: 1px solid rgba(34,197,94,0.25);
  color: #4ade80;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  padding: 5px 12px;
  border-radius: 20px;
  margin-bottom: 1rem;
}

.hero-badge svg { width: 13px; }

.hero-title {
  font-size: 2.75rem;
  font-weight: 900;
  letter-spacing: -1.5px;
  color: white;
  line-height: 1.1;
  margin-bottom: 0.75rem;
}

.hero-accent {
  background: linear-gradient(90deg, #4ade80, #22d3ee);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-sub {
  color: #94a3b8;
  font-size: 0.95rem;
  font-weight: 500;
  max-width: 380px;
  line-height: 1.6;
  margin-bottom: 1.75rem;
}

/* Stats */
.hero-stats {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}

.stat-pill { display: flex; flex-direction: column; }
.stat-val  { font-size: 1.6rem; font-weight: 900; color: white; line-height: 1; }
.stat-val.green  { color: #4ade80; }
.stat-val.orange { color: #fb923c; }
.stat-label { font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }
.stat-divider { width: 1px; height: 36px; background: rgba(255,255,255,0.08); }

/* Phone Mockup */
.hero-right { flex-shrink: 0; }

.phone-mockup {
  width: 130px;
  height: 230px;
  background: linear-gradient(160deg, #1e293b, #0f172a);
  border-radius: 22px;
  border: 2px solid rgba(255,255,255,0.1);
  position: relative;
  box-shadow: 0 30px 60px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);
  transform: rotate(4deg);
  animation: floatPhone 4s ease-in-out infinite;
}

@keyframes floatPhone {
  0%, 100% { transform: rotate(4deg) translateY(0); }
  50%       { transform: rotate(4deg) translateY(-8px); }
}

.phone-notch {
  position: absolute;
  top: 6px; left: 50%; transform: translateX(-50%);
  width: 36px; height: 6px;
  background: rgba(255,255,255,0.08);
  border-radius: 3px;
}

.phone-btn { position: absolute; background: rgba(255,255,255,0.12); border-radius: 2px; }
.phone-btn-right  { right: -3px; top: 60px; width: 3px; height: 36px; }
.phone-btn-left1  { left: -3px; top: 50px; width: 3px; height: 22px; }
.phone-btn-left2  { left: -3px; top: 80px; width: 3px; height: 22px; }

.phone-screen {
  position: absolute;
  inset: 8px;
  border-radius: 16px;
  background: linear-gradient(160deg, #22c55e15, #0ea5e910);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  padding: 10px 8px 8px;
  gap: 8px;
}

.mock-topbar {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.42rem;
  color: #4ade80;
  font-weight: 700;
}

.mock-dot { width: 5px; height: 5px; background: #4ade80; border-radius: 50%; }

.mock-qr-area {
  background: rgba(255,255,255,0.05);
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6px;
  gap: 3px;
}

.mock-hint { font-size: 0.38rem; color: #94a3b8; font-weight: 600; }

.mock-menu-items { display: flex; flex-direction: column; gap: 4px; }
.mock-item {
  height: 8px; background: rgba(255,255,255,0.06);
  border-radius: 4px; animation: shimmer 1.8s ease-in-out infinite;
}
.mock-item:nth-child(2) { width: 80%; animation-delay: 0.3s; }
.mock-item:nth-child(3) { width: 60%; animation-delay: 0.6s; }

@keyframes shimmer {
  0%, 100% { opacity: 0.4; }
  50%       { opacity: 0.8; }
}

/* Action bar */
.hero-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  background: rgba(255,255,255,0.04);
  border-top: 1px solid rgba(255,255,255,0.06);
  padding: 1rem 0;
  margin: 0 -2.5rem;
  padding-left: 2.5rem;
  padding-right: 2.5rem;
}

.search-wrap {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  padding: 8px 14px;
}

.search-icon { width: 16px; color: #64748b; flex-shrink: 0; }

.search-input {
  background: transparent;
  border: none;
  outline: none;
  color: white;
  font-size: 0.875rem;
  font-weight: 500;
  width: 100%;
}

.search-input::placeholder { color: #475569; }

.hero-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.25s;
  white-space: nowrap;
  box-shadow: 0 4px 15px rgba(34,197,94,0.3);
}

.hero-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(34,197,94,0.4); }
.hero-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* ═══════════════════════════════════════════
   PAGE BODY
═══════════════════════════════════════════ */
.page-body { }

/* Steps row */
.steps-row {
  display: grid;
  grid-template-columns: 1fr auto 1fr auto 1fr auto 1fr;
  align-items: center;
  gap: 0;
  background: white;
  border-radius: 20px;
  border: 1px solid #f1f5f9;
  padding: 1.5rem 2rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 12px rgba(0,0,0,0.025);
}

.step-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
  padding: 0 1rem;
}

.step-icon-wrap {
  width: 44px; height: 44px;
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 4px;
}

.step-n { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; }
.step-text { font-size: 0.8rem; color: #64748b; font-weight: 500; line-height: 1.4; }

.step-connector {
  width: 2px;
  height: 24px;
  background: linear-gradient(to bottom, transparent, #e2e8f0, transparent);
  border-radius: 1px;
  transform: rotate(90deg);
}

/* States */
.center-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 5rem 2rem; gap: 1.25rem; text-align: center; color: #64748b;
}

/* Pulse rings loader */
.pulse-rings { position: relative; width: 60px; height: 60px; }
.ring {
  position: absolute; inset: 0;
  border: 2px solid #22c55e;
  border-radius: 50%;
  opacity: 0;
  animation: pulse-ring 2s ease-out infinite;
}
.ring:nth-child(2) { animation-delay: 0.6s; }
.ring:nth-child(3) { animation-delay: 1.2s; }
@keyframes pulse-ring { 0% { transform: scale(0.3); opacity: 0.8; } 100% { transform: scale(1.4); opacity: 0; } }

.error-text { color: #ef4444; font-weight: 600; }
.empty-visual { margin-bottom: 0.5rem; }
.center-state h3 { font-size: 1.25rem; font-weight: 700; color: #1e293b; }
.center-state p  { font-size: 0.9rem; }

/* ═══════════════════════════════════════════
   TABLES GRID
═══════════════════════════════════════════ */
.tables-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 1.25rem;
}

/* ═══════════════════════════════════════════
   TABLE CARD
═══════════════════════════════════════════ */
.table-card {
  background: white;
  border-radius: 22px;
  border: 1.5px solid #f1f5f9;
  box-shadow: 0 2px 12px rgba(0,0,0,0.03);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  position: relative;
  overflow: hidden;
  animation: cardIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
  animation-delay: var(--delay, 0s);
}

@keyframes cardIn {
  from { opacity: 0; transform: translateY(20px) scale(0.96); }
  to   { opacity: 1; transform: none; }
}

.table-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(0,0,0,0.08);
  border-color: #e2e8f0;
}

.table-card.has-qr { border-color: #d1fae5; }
.table-card.has-qr:hover { box-shadow: 0 16px 40px rgba(34,197,94,0.1); }

/* Green glow on cards with QR */
.card-glow {
  position: absolute;
  top: -40px; right: -40px;
  width: 120px; height: 120px;
  background: radial-gradient(circle, rgba(34,197,94,0.12), transparent 70%);
  pointer-events: none;
}

/* Card header */
.tc-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.tc-table-id {
  display: flex; align-items: center; gap: 10px;
}

.tc-icon {
  width: 34px; height: 34px;
  background: #f8fafc;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: #64748b;
  flex-shrink: 0;
}

.tc-label { display: block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.4px; }
.tc-num   { display: block; font-size: 1.25rem; font-weight: 800; color: #1e293b; line-height: 1.1; }

.tc-badge {
  display: flex; align-items: center; gap: 5px;
  font-size: 0.65rem; font-weight: 800;
  text-transform: uppercase; letter-spacing: 0.3px;
  padding: 4px 10px; border-radius: 20px;
}

.badge-dot {
  width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
}

.badge-ready  { background: #ecfdf5; color: #16a34a; }
.badge-ready  .badge-dot { background: #22c55e; animation: blink 2s ease-in-out infinite; }
.badge-pending { background: #fff7ed; color: #c2410c; }
.badge-pending .badge-dot { background: #f97316; }

@keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

/* QR Zone */
.tc-qr-zone {
  border-radius: 16px;
  overflow: hidden;
  background: #f8fafc;
  aspect-ratio: 1;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: background 0.25s;
}

.tc-qr-zone:hover .qr-hover-overlay { opacity: 1; }

.qr-display {
  width: 100%; height: 100%;
  position: relative;
  display: flex; align-items: center; justify-content: center;
  padding: 0.75rem;
}

/* Corner brackets */
.qr-corner {
  position: absolute;
  width: 16px; height: 16px;
  border-color: #22c55e; border-style: solid;
  opacity: 0.6;
}
.qr-corner.tl { top: 6px; left: 6px; border-width: 2px 0 0 2px; border-radius: 2px 0 0 0; }
.qr-corner.tr { top: 6px; right: 6px; border-width: 2px 2px 0 0; border-radius: 0 2px 0 0; }
.qr-corner.bl { bottom: 6px; left: 6px; border-width: 0 0 2px 2px; border-radius: 0 0 0 2px; }
.qr-corner.br { bottom: 6px; right: 6px; border-width: 0 2px 2px 0; border-radius: 0 0 2px 0; }

.qr-img {
  width: 100%; height: 100%;
  object-fit: contain;
  mix-blend-mode: multiply;
}

.qr-hover-overlay {
  position: absolute; inset: 0;
  background: rgba(15,23,42,0.6);
  backdrop-filter: blur(4px);
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  color: white; gap: 6px;
  opacity: 0; transition: opacity 0.25s;
  border-radius: 14px;
}

.qr-hover-overlay span { font-size: 0.75rem; font-weight: 700; }

.qr-placeholder-zone {
  width: 100%; height: 100%;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 8px; color: #cbd5e1;
}

.qr-placeholder-icon svg { width: 60px; }
.placeholder-label { font-size: 0.78rem; font-weight: 700; color: #94a3b8; }
.placeholder-sub   { font-size: 0.68rem; font-weight: 500; color: #cbd5e1; }

/* URL chip */
.url-chip {
  display: flex; align-items: center; gap: 6px;
  background: #f1f5f9; border-radius: 8px;
  padding: 5px 10px;
}

.url-text {
  font-size: 0.68rem; font-family: monospace; color: #64748b;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Actions */
.tc-actions { display: flex; gap: 6px; align-items: center; }

.btn-gen {
  flex: 1;
  display: inline-flex; align-items: center; justify-content: center; gap: 6px;
  padding: 9px 14px; border-radius: 11px;
  font-weight: 700; font-size: 0.8rem;
  border: none; cursor: pointer;
  transition: all 0.25s;
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white;
  box-shadow: 0 3px 10px rgba(34,197,94,0.25);
}

.btn-gen:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(34,197,94,0.35); }
.btn-gen:disabled { opacity: 0.5; cursor: not-allowed; }

.btn-regen {
  background: white;
  color: #64748b;
  border: 1.5px solid #e2e8f0;
  box-shadow: none;
}
.btn-regen:hover:not(:disabled) { border-color: #cbd5e1; color: #1e293b; background: #f8fafc; box-shadow: none; transform: translateY(-1px); }

.btn-icon-sq {
  width: 36px; height: 36px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  background: #f1f5f9; color: #64748b; border: none;
  cursor: pointer; transition: all 0.2s;
  text-decoration: none; flex-shrink: 0;
}
.btn-icon-sq:hover { background: #e2e8f0; color: #1e293b; }

/* Inline error */
.tc-error {
  font-size: 0.75rem; color: #ef4444;
  background: #fef2f2; border-radius: 8px;
  padding: 6px 10px; font-weight: 600;
}

/* ═══════════════════════════════════════════
   MODAL
═══════════════════════════════════════════ */
.modal-backdrop {
  position: fixed; inset: 0;
  background: rgba(2, 6, 23, 0.75);
  backdrop-filter: blur(10px);
  z-index: 2000;
  display: flex; align-items: center; justify-content: center;
  padding: 2rem;
}

.preview-modal {
  background: white;
  border-radius: 28px;
  max-width: 700px; width: 100%;
  display: flex;
  overflow: hidden;
  box-shadow: 0 40px 80px rgba(0,0,0,0.3);
}

/* Modal left pane */
.modal-left {
  background: linear-gradient(160deg, #0f172a, #1e293b);
  padding: 2.5rem 2rem;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 1rem;
  min-width: 240px;
  position: relative;
  overflow: hidden;
}

.modal-left::before {
  content: '';
  position: absolute;
  width: 200px; height: 200px;
  background: radial-gradient(circle, rgba(34,197,94,0.15), transparent 65%);
  top: -40px; left: -40px;
}

.modal-qr-frame {
  position: relative;
  padding: 1rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

.modal-qr-corners > div { display: none; } /* decorative, hidden for simplicity */

.modal-qr-img { width: 180px; height: 180px; mix-blend-mode: multiply; }

.modal-table-label {
  color: #94a3b8; font-size: 0.85rem; font-weight: 600;
}
.modal-table-label strong { color: white; font-size: 1.25rem; }

.modal-restaurant-name {
  color: #4ade80; font-size: 0.7rem; font-weight: 700;
  text-transform: uppercase; letter-spacing: 1px;
}

/* Modal right pane */
.modal-right {
  flex: 1;
  padding: 2rem;
  display: flex; flex-direction: column;
  gap: 1.5rem;
}

.modal-top-row {
  display: flex; justify-content: space-between; align-items: flex-start;
}

.modal-title    { font-size: 1.3rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
.modal-subtitle { font-size: 0.82rem; color: #64748b; font-weight: 500; }

.close-btn {
  background: #f1f5f9; border: none; color: #64748b;
  width: 32px; height: 32px; border-radius: 8px;
  font-size: 1.2rem; cursor: pointer; display: flex;
  align-items: center; justify-content: center;
  transition: all 0.2s; line-height: 1;
}
.close-btn:hover { background: #e2e8f0; color: #1e293b; }

.modal-section { display: flex; flex-direction: column; gap: 6px; }
.modal-field-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; }

.modal-url-box {
  font-size: 0.75rem; font-family: monospace;
  background: #f8fafc; border: 1px solid #e2e8f0;
  border-radius: 8px; padding: 8px 12px;
  color: #475569; word-break: break-all;
}

.modal-steps { display: flex; flex-direction: column; gap: 6px; }
.modal-step {
  display: flex; align-items: center; gap: 10px;
  font-size: 0.82rem; color: #475569; font-weight: 500;
}
.modal-step span {
  width: 22px; height: 22px;
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.7rem; font-weight: 800; flex-shrink: 0;
}

.modal-actions {
  display: flex; gap: 8px;
  margin-top: auto;
}

.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 6px;
  padding: 10px 18px; border-radius: 12px;
  font-weight: 700; font-size: 0.875rem;
  border: none; cursor: pointer; transition: all 0.25s;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white; flex: 1;
  box-shadow: 0 4px 12px rgba(34,197,94,0.25);
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(34,197,94,0.35); }

.btn-ghost {
  background: #f1f5f9; color: #64748b;
  border: 1.5px solid #e2e8f0; flex: 1;
}
.btn-ghost:hover { background: #e2e8f0; color: #1e293b; }

/* ═══════════════════════════════════════════
   SPINNERS
═══════════════════════════════════════════ */
.spin-icon {
  width: 13px; height: 13px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  flex-shrink: 0;
}

.spin-icon-dark {
  width: 15px; height: 15px;
  border: 2px solid rgba(255,255,255,0.25);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
  flex-shrink: 0;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ═══════════════════════════════════════════
   TRANSITIONS
═══════════════════════════════════════════ */
.qr-reveal-enter-active { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
.qr-reveal-leave-active { transition: all 0.2s ease; }
.qr-reveal-enter-from   { opacity: 0; transform: scale(0.85); }
.qr-reveal-leave-to     { opacity: 0; transform: scale(1.05); }

.modal-pop-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-pop-leave-active { transition: all 0.2s ease; }
.modal-pop-enter-from   { opacity: 0; }
.modal-pop-enter-from .preview-modal { transform: scale(0.92) translateY(20px); }
.modal-pop-leave-to     { opacity: 0; }

.card-pop-enter-active { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
.card-pop-enter-from   { opacity: 0; transform: translateY(16px) scale(0.95); }
</style>
