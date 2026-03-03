<template>
  <div class="settings-view fade-in">
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">System <span class="highlight">Configuration</span></h2>
        <p class="page-subtitle">Manage restaurant preferences and API integration</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-primary" @click="saveSettings" :disabled="saving">
          <Save :size="18" />
          {{ saving ? 'Syncing...' : 'Save Changes' }}
        </button>
      </div>
    </div>

    <div class="settings-grid">
      <!-- General Settings -->
      <section class="settings-section white-card">
        <div class="section-header">
          <Store :size="20" />
          <h3>Restaurant Profile</h3>
        </div>
        <div class="form-grid">
          <div class="input-group">
            <label>Business Name</label>
            <input v-model="settings.name" type="text" placeholder="Mlup Dong Restaurant">
          </div>
          <div class="input-group">
            <label>Contact Email</label>
            <input v-model="settings.email" type="email" placeholder="contact@mlupdong.com">
          </div>
          <div class="input-group full">
            <label>Physical Address</label>
            <textarea v-model="settings.address" placeholder="Phnom Penh, Cambodia"></textarea>
          </div>
        </div>
      </section>

      <!-- Operation Settings -->
      <section class="settings-section white-card">
        <div class="section-header">
          <Clock :size="20" />
          <h3>Operational Logic</h3>
        </div>
        <div class="form-grid">
          <div class="input-group">
            <label>Currency Symbol</label>
            <input v-model="settings.currency" type="text" placeholder="$">
          </div>
          <div class="input-group">
            <label>Tax Rate (%)</label>
            <input v-model="settings.tax" type="number" placeholder="10">
          </div>
          <div class="input-group">
            <label>Avg. Prep Time (mins)</label>
            <input v-model="settings.avgPrepTime" type="number" placeholder="20">
          </div>
          <div class="input-group switch-group">
            <label>Auto-Print KOT</label>
            <div class="toggle-switch" :class="{ active: settings.autoPrint }" @click="settings.autoPrint = !settings.autoPrint">
              <div class="thumb"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Security & Branding -->
      <section class="settings-section white-card full-width">
        <div class="section-header">
          <Palette :size="20" />
          <h3>Branding & Theme</h3>
        </div>
        <div class="branding-grid">
          <div class="brand-preview">
            <div class="logo-circle">🌿</div>
            <button class="btn-outline">Upload New Logo</button>
          </div>
          <div class="color-picker-grid">
            <div class="color-item">
              <label>Primary Brand Color</label>
              <div class="color-input-wrap">
                <div class="color-circle" :style="{ background: settings.primaryColor }"></div>
                <input v-model="settings.primaryColor" type="text">
              </div>
            </div>
            <div class="color-item">
              <label>Accent Surface</label>
              <div class="color-input-wrap">
                <div class="color-circle" :style="{ background: settings.accentColor }"></div>
                <input v-model="settings.accentColor" type="text">
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import { Save, Store, Clock, Palette, Globe, Shield } from 'lucide-vue-next';

export default {
  name: 'SettingsView',
  components: { Save, Store, Clock, Palette, Globe, Shield },
  data() {
    return {
      saving: false,
      settings: {
        name: 'Mlup Dong',
        email: 'admin@mlupdong.com',
        address: '123 Norodom Blvd, Phnom Penh',
        currency: '$',
        tax: 10,
        avgPrepTime: 15,
        autoPrint: true,
        primaryColor: '#22c55e',
        accentColor: '#1e293b'
      }
    };
  },
  methods: {
    async saveSettings() {
      this.saving = true;
      // Mock API call
      await new Promise(r => setTimeout(r, 1000));
      this.saving = false;
      alert('System configuration updated successfully.');
    }
  }
};
</script>

<style scoped>
.settings-view { color: #1e293b; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
.page-title { font-size: 2.25rem; font-weight: 800; color: #1e293b; }
.highlight { color: #22c55e; }
.page-subtitle { color: #64748b; font-weight: 500; font-size: 1rem; }

.btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; border: none; }
.btn-primary { background: #22c55e; color: #000; }
.btn-outline { background: #f1f5f9; color: #64748b; font-size: 0.8rem; padding: 8px 16px; border-radius: 10px; }

.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.full-width { grid-column: span 2; }

.white-card { background: white; border-radius: 24px; padding: 2rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }

.section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 2rem; color: #1e293b; }
.section-header h3 { font-size: 1.15rem; font-weight: 800; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.full { grid-column: span 2; }

.input-group { display: flex; flex-direction: column; gap: 8px; }
.input-group label { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.input-group input, .input-group textarea {
  background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px;
  font-family: inherit; font-size: 0.95rem; font-weight: 600; color: #1e293b; outline: none;
}
.input-group input:focus { border-color: #22c55e; background: white; }

.switch-group { align-items: flex-start; }
.toggle-switch {
  width: 48px; height: 24px; background: #e2e8f0; border-radius: 20px;
  position: relative; cursor: pointer; transition: 0.3s;
}
.toggle-switch.active { background: #22c55e; }
.toggle-switch .thumb {
  width: 18px; height: 18px; background: white; border-radius: 50%;
  position: absolute; top: 3px; left: 3px; transition: 0.3s;
}
.toggle-switch.active .thumb { left: 27px; }

.branding-grid { display: grid; grid-template-columns: 200px 1fr; gap: 3rem; align-items: center; }
.brand-preview { text-align: center; display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.logo-circle { width: 100px; height: 100px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; }

.color-picker-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.color-input-wrap { display: flex; align-items: center; gap: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 8px 12px; }
.color-circle { width: 24px; height: 24px; border-radius: 6px; flex-shrink: 0; }
.color-input-wrap input { background: transparent; border: none; font-family: monospace; font-weight: 700; width: 100%; outline: none; }

.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
