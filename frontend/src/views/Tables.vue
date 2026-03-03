<template>
  <div class="tables-view fade-in">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">Unit <span class="highlight">Deployment</span></h2>
        <p class="page-subtitle">Configure your restaurant layout and manage digital access points</p>
      </div>
      <div class="header-actions">
        <button @click="showAddForm = !showAddForm" 
                :class="['btn', showAddForm ? 'btn-secondary' : 'btn-primary']">
          <svg v-if="!showAddForm" viewBox="0 0 20 20" fill="currentColor" class="btn-icon"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/></svg>
          <svg v-else viewBox="0 0 20 20" fill="currentColor" class="btn-icon"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
          {{ showAddForm ? 'Close Editor' : 'Register New Table' }}
        </button>
        <button @click="generateAll" 
                :disabled="tables.length === 0 || isGeneratingAll" 
                class="btn btn-outline">
          <svg class="btn-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
          </svg>
          {{ isGeneratingAll ? 'Deploying...' : 'Bulk Generate QRs' }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="modern-spinner"></div>
      <p>Synchronizing assets...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state white-card">
      <div class="error-icon">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
      </div>
      <h3>Sync Interrupted</h3>
      <p>{{ error }}</p>
      <button @click="fetchTables" class="btn btn-primary">Retry Connection</button>
    </div>

    <!-- Main Content -->
    <div v-else class="content-wrapper">
      <!-- Add Table Form -->
      <transition name="fade">
        <div v-if="showAddForm" class="white-card add-table-form">
          <div class="form-header">
            <h3>Register New Unit</h3>
            <p>Define a unique identifier for your guest table</p>
          </div>
          <form @submit.prevent="addTable" class="modern-form">
            <div class="input-container">
              <input 
                id="tableNumber"
                v-model="newNumber" 
                type="text" 
                placeholder=" "
                class="modern-input"
                :class="{ 'input-err': addError }"
                ref="tableNumberInput"
                required
              />
              <label for="tableNumber">Identification Number</label>
              <div class="input-underline"></div>
              <span v-if="addError" class="err-text">{{ addError }}</span>
            </div>
            <div class="form-footer">
              <button type="button" @click="showAddForm = false" class="btn-text">Discard</button>
              <button type="submit" class="btn btn-primary" :disabled="!newNumber.trim()">
                Initialize Table
              </button>
            </div>
          </form>
        </div>
      </transition>

      <!-- Empty State -->
      <div v-if="tables.length === 0" class="empty-state white-card">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18" stroke-dasharray="2 2"/>
          </svg>
        </div>
        <h3>No Units Detected</h3>
        <p>Your floor plan is currently empty. Start by registering your first table.</p>
        <button @click="showAddForm = true" class="btn btn-primary">
          Begin Registration
        </button>
      </div>

      <!-- Tables Grid -->
      <div v-else class="tables-grid">
        <div v-for="table in tables" :key="table.id" class="table-unit white-card" :class="{ 'is-new': table.isNew }">
          <div class="unit-header">
            <div class="unit-info">
              <span class="unit-label">Unit</span>
              <h3 class="unit-number">{{ table.number }}</h3>
            </div>
            <div :class="['status-chip', table.qr_code_path ? 'active' : 'pending']">
              {{ table.qr_code_path ? 'Ready' : 'Pending' }}
            </div>
          </div>
          
          <div class="unit-visual">
            <div v-if="table.qr_code_url" class="qr-preview-box" @click="previewQR(table.qr_code_url)">
              <img :src="table.qr_code_url" alt="QR" class="qr-thumb" />
              <div class="qr-overlay">
                <svg viewBox="0 0 20 20" fill="currentColor" width="20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                <span>View</span>
              </div>
            </div>
            <div v-else class="qr-placeholder">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 9h6v6H9z" stroke-dasharray="2 2"/></svg>
              <span>Needs QR</span>
            </div>
          </div>
          
          <div class="unit-footer">
            <div class="qr-action">
               <TableQr :tableId="table.id" @generated="onQrGenerated(table, $event)" />
            </div>
            <div class="unit-actions">
              <button @click="startEdit(table)" class="icon-btn" title="Edit Unit">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
              </button>
              <button @click="deleteTable(table.id)" class="icon-btn danger" title="Purge Unit">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <transition name="fade">
      <div v-if="previewModalOpen || editingTable" class="modal-backdrop" @click="closeAllModals">
        <!-- Preview Modal -->
        <div v-if="previewModalOpen" class="premium-modal" @click.stop>
          <div class="modal-inner">
            <div class="modal-top">
              <h3>Secure Asset Preview</h3>
              <button @click="closePreview" class="close-btn">&times;</button>
            </div>
            <div class="preview-content">
              <div class="qr-large-frame">
                <img :src="previewImageUrl" alt="Asset" />
              </div>
              <div class="preview-actions">
                <p>Ready for physical deployment</p>
                <a :href="previewImageUrl" download class="btn btn-primary">Download SVG Asset</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="editingTable" class="premium-modal" @click.stop>
          <div class="modal-inner">
            <div class="modal-top">
              <h3>System Override</h3>
              <button @click="cancelEdit" class="close-btn">&times;</button>
            </div>
            <p class="modal-subtitle">Updating unit identification in database.</p>
            <form @submit.prevent="updateTable" class="modern-form">
              <div class="input-container">
                <input 
                  id="editNumber"
                  v-model="editNumber" 
                  type="text" 
                  class="modern-input"
                  :class="{ 'input-err': editError }"
                  ref="editNumberInput"
                  placeholder=" "
                />
                <label for="editNumber">New Identifier</label>
                <div class="input-underline"></div>
                <span v-if="editError" class="err-text">{{ editError }}</span>
              </div>
              <div class="form-footer">
                <button type="button" @click="cancelEdit" class="btn-text">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="!editNumber.trim()">
                  Execute Update
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
import TableQr from '@/components/TableQr.vue';

export default {
  components: { TableQr },
  data() {
    return {
      tables: [],
      loading: true,
      error: null,
      showAddForm: false,
      newNumber: '',
      addError: null,
      isGeneratingAll: false,
      previewModalOpen: false,
      previewImageUrl: null,
      editingTable: null,
      editNumber: '',
      editError: null,
    };
  },
  async created() {
    await this.fetchTables();
  },
  watch: {
    tables: {
      handler(newTables) {
        this.$emit('tables-updated', newTables);
      },
      immediate: true
    }
  },
  methods: {
    async fetchTables() {
      this.loading = true;
      this.error = null;
      try {
        const res = await axios.get('/api/tables');
        this.tables = res.data;
      } catch (err) {
        this.error = 'Central database offline. Verification required.';
      } finally {
        this.loading = false;
      }
    },
    async addTable() {
      this.addError = null;
      if (!this.newNumber.trim()) return;
      try {
        const res = await axios.post('/api/tables', { number: this.newNumber.trim() });
        const newTable = { ...res.data, isNew: true };
        this.tables.push(newTable);
        this.newNumber = '';
        this.showAddForm = false;
        setTimeout(() => { newTable.isNew = false; }, 500);
      } catch (err) {
        this.addError = err.response?.data?.message || 'Initialization failure.';
      }
    },
    async deleteTable(id) {
      if (!confirm('Execute purge for this unit? This cannot be undone.')) return;
      try {
        await axios.delete(`/api/tables/${id}`);
        this.tables = this.tables.filter(t => t.id !== id);
      } catch (err) {
        alert('Purge sequence failed.');
      }
    },
    async generateAll() {
      this.isGeneratingAll = true;
      try {
        await axios.post('/api/tables/generate-all');
        await this.fetchTables();
      } catch (err) {
        alert('Bulk deployment failed.');
      } finally {
        this.isGeneratingAll = false;
      }
    },
    onQrGenerated(table, qrUrl) {
      table.qr_code_url = qrUrl;
      table.qr_code_path = 'generated';
    },
    previewQR(imageUrl) {
      this.previewImageUrl = imageUrl;
      this.previewModalOpen = true;
    },
    closePreview() {
      this.previewModalOpen = false;
      this.previewImageUrl = null;
    },
    startEdit(table) {
      this.editingTable = table;
      this.editNumber = table.number;
      this.editError = null;
      this.$nextTick(() => this.$refs.editNumberInput?.focus());
    },
    cancelEdit() {
      this.editingTable = null;
      this.editNumber = '';
      this.editError = null;
    },
    async updateTable() {
      this.editError = null;
      if (!this.editNumber.trim()) return;
      try {
        const res = await axios.put(`/api/tables/${this.editingTable.id}`, { 
          number: this.editNumber.trim() 
        });
        const index = this.tables.findIndex(t => t.id === this.editingTable.id);
        if (index !== -1) this.tables[index].number = res.data.number;
        this.cancelEdit();
      } catch (err) {
        this.editError = err.response?.data?.message || 'Override rejected.';
      }
    },
    closeAllModals() {
      this.closePreview();
      this.cancelEdit();
    }
  },
};
</script>

<style scoped>
.tables-view { color: #1e293b; }

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 2.5rem;
}

.page-title {
  font-size: 2.25rem;
  font-weight: 800;
  letter-spacing: -1px;
}

.highlight { color: #22c55e; }

.page-subtitle { color: #64748b; font-weight: 500; font-size: 1rem; }

.header-actions { display: flex; gap: 0.75rem; }

/* Cards & Components */
.white-card {
  background: white;
  border-radius: 20px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}

.add-table-form {
  padding: 2rem;
  margin-bottom: 2.5rem;
  max-width: 550px;
}

.form-header h3 { font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }
.form-header p { color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem; }

.tables-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.5rem;
}

.table-unit {
  padding: 1.5rem;
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.table-unit:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.unit-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.unit-label { font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
.unit-number { font-size: 1.5rem; font-weight: 800; color: #1e293b; line-height: 1.2; }

.status-chip {
  font-size: 0.65rem;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 20px;
  text-transform: uppercase;
}

.status-chip.active { background: #ecfdf5; color: #10b981; }
.status-chip.pending { background: #fff7ed; color: #f97316; }

.unit-visual {
  aspect-ratio: 1;
  background: #f8fafc;
  border-radius: 16px;
  overflow: hidden;
  position: relative;
}

.qr-preview-box {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  padding: 12px; cursor: pointer;
}

.qr-thumb { width: 100%; height: 100%; mix-blend-mode: multiply; }

.qr-overlay {
  position: absolute; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(34, 197, 94, 0.8);
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  color: white; opacity: 0; transition: all 0.3s; gap: 4px;
}

.qr-preview-box:hover .qr-overlay { opacity: 1; }
.qr-overlay span { font-weight: 800; font-size: 0.75rem; text-transform: uppercase; }

.qr-placeholder {
  width: 100%; height: 100%;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  color: #cbd5e1; gap: 10px; font-size: 0.75rem; font-weight: 600;
}

.qr-placeholder svg { width: 40px; }

.unit-footer { display: flex; justify-content: space-between; align-items: center; }
.qr-action { flex: 1; }
.unit-actions { display: flex; gap: 4px; }

/* Global-like Styles */
.btn {
  display: inline-flex; 
  align-items: center; 
  gap: 8px;
  padding: 8px 16px; 
  border-radius: 10px; 
  font-weight: 700; 
  border: none; 
  cursor: pointer;
  transition: all 0.2s; 
  font-size: 0.8rem;
  white-space: nowrap;
}

.btn-icon {
  width: 16px;
  height: 16px;
}

.btn-primary { background: #22c55e; color: white; }
.btn-primary:hover { background: #16a34a; transform: translateY(-2px); }

.btn-secondary { background: #f1f5f9; color: #64748b; }
.btn-secondary:hover { background: #e2e8f0; }

.btn-outline { background: transparent; border: 1px solid #e2e8f0; color: #64748b; }
.btn-outline:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }

.icon-btn {
  width: 32px; height: 32px; border-radius: 8px; border: none;
  background: #f1f5f9; color: #64748b; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: 0.2s;
}

.icon-btn:hover { background: #e2e8f0; color: #1e293b; }
.icon-btn.danger:hover { color: #ef4444; background: #fef2f2; }

/* Modals */
.modal-backdrop {
  position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px);
  z-index: 2000; display: flex; align-items: center; justify-content: center;
  padding: 2rem;
}

.premium-modal {
  background: white; border-radius: 24px; max-width: 500px; width: 100%;
  overflow: hidden;
}

.modal-inner { padding: 2rem; }
.modal-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.modal-subtitle { color: #64748b; margin-bottom: 2rem; font-size: 0.9rem; }
.close-btn { background: transparent; border: none; color: #94a3b8; font-size: 2rem; cursor: pointer; line-height: 1; }

.preview-content { display: flex; flex-direction: column; align-items: center; gap: 2rem; }
.qr-large-frame { padding: 2rem; background: #f8fafc; border-radius: 20px; }
.qr-large-frame img { width: 250px; mix-blend-mode: multiply; }
.preview-actions { text-align: center; }
.preview-actions p { color: #64748b; font-size: 0.85rem; margin-bottom: 1rem; font-weight: 500; }

/* Forms */
.modern-form { display: flex; flex-direction: column; gap: 2rem; }
.input-container { position: relative; }
.modern-input {
  width: 100%; background: transparent; border: none; border-bottom: 2px solid #f1f5f9;
  padding: 12px 0; color: #1e293b; font-size: 1.1rem; outline: none; transition: 0.3s;
}
.input-container label {
  position: absolute; top: 12px; left: 0; color: #94a3b8; transition: 0.3s; pointer-events: none; font-weight: 500;
}
.modern-input:focus ~ label,
.modern-input:not(:placeholder-shown) ~ label { top: -14px; font-size: 0.8rem; color: #22c55e; }
.input-underline { position: absolute; bottom: 0; left: 0; height: 2px; width: 0; background: #22c55e; transition: 0.3s; }
.modern-input:focus ~ .input-underline { width: 100%; }

.err-text { font-size: 0.75rem; color: #ef4444; margin-top: 4px; display: block; }
.btn-text { background: transparent; border: none; color: #94a3b8; font-weight: 600; cursor: pointer; }
.btn-text:hover { color: #1e293b; }

.empty-state { text-align: center; padding: 4rem; color: #64748b; }
.empty-icon { color: #cbd5e1; margin-bottom: 1.5rem; }
.empty-icon svg { width: 64px; }

.loading-state { text-align: center; padding: 5rem; color: #64748b; font-weight: 600; }
.modern-spinner {
  width: 40px; height: 40px; border: 3px solid #f1f5f9; border-top-color: #22c55e;
  border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1.5rem;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
