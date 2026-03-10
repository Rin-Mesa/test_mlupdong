<template>
  <div class="menu-manager fade-in">
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">Culinary <span class="highlight">Inventory</span></h2>
        <p class="page-subtitle">Manage your kitchen assets and digital menu items</p>
      </div>
      <div class="header-actions">
        <button @click="openAddModal" class="btn btn-primary">
          <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/></svg>
          Add Item
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar white-card">
      <div class="search-input">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
        <input v-model="searchQuery" type="text" placeholder="Search menu database...">
      </div>
      <div class="category-tabs">
        <button v-for="cat in categories" :key="cat" 
                @click="selectedCategory = cat"
                :class="['tab', { active: selectedCategory === cat }]">
          {{ cat }}
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="modern-spinner"></div>
      <span>Synchronizing menu...</span>
    </div>

    <!-- Grid -->
    <div v-else class="menu-grid">
      <div v-for="item in filteredItems" :key="item.id" class="menu-card white-card">
        <div class="card-image">
          <img v-if="item.image_url" :src="item.image_url" :alt="item.name">
          <div v-else class="image-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z"/>
              <path d="M17 12h-3M6.5 12h4.5M12 7l-2 5h4l-2 5"/>
            </svg>
          </div>
          <div class="category-tag">{{ item.category }}</div>
        </div>
        <div class="card-content">
          <div class="card-header-row">
            <h3 class="item-name">{{ item.name }}</h3>
            <span class="item-price">${{ item.price }}</span>
          </div>
          <p class="item-desc">{{ item.description || 'No description provided.' }}</p>
          <div class="card-footer">
            <div :class="['availability-toggle', { disabled: !item.is_available }]" 
                 @click="toggleAvailability(item)">
              <div class="toggle-track">
                <div class="toggle-thumb"></div>
              </div>
              <span>{{ item.is_available ? 'Available' : 'Sold Out' }}</span>
            </div>
            <div class="item-actions">
              <button @click="editItem(item)" class="icon-btn" title="Edit Item">
                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
              </button>
              <button @click="deleteItem(item.id)" class="icon-btn danger" title="Remove Item">
                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <transition name="fade">
      <div v-if="showModal" class="modal-backdrop" @click="closeModal">
        <div class="premium-modal" @click.stop>
          <div class="modal-inner">
            <div class="modal-top">
              <h3>{{ editingItem ? 'Modify Asset' : 'New Culinary Entry' }}</h3>
              <button @click="closeModal" class="close-btn">&times;</button>
            </div>
            
            <form @submit.prevent="saveItem" class="modern-form">
              <div class="form-grid">
                <div class="input-container full">
                  <input v-model="form.name" type="text" required class="modern-input" placeholder=" ">
                  <label>Item Name</label>
                  <div class="input-underline"></div>
                </div>

                <div class="input-container">
                  <input v-model="form.price" type="number" step="0.01" required class="modern-input" placeholder=" ">
                  <label>Unit Price ($)</label>
                  <div class="input-underline"></div>
                </div>

                <div class="input-container">
                  <select v-model="form.category" class="modern-input select">
                    <option v-for="cat in categories.slice(1)" :key="cat" :value="cat">{{ cat }}</option>
                  </select>
                  <label>Classification</label>
                </div>

                <div class="input-container full">
                  <textarea v-model="form.description" class="modern-input" placeholder=" "></textarea>
                  <label>Technical Description</label>
                  <div class="input-underline"></div>
                </div>

                <div class="input-container full">
                  <div class="file-drop-zone" @click="$refs.fileInput.click()">
                    <input type="file" ref="fileInput" @change="handleFileUpload" hidden accept="image/*">
                    <div v-if="imagePreview" class="preview-wrap">
                      <img :src="imagePreview">
                      <span class="change-hint">Change Image</span>
                    </div>
                    <div v-else class="upload-hint">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                      </svg>
                      <span>Upload Asset Image</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-footer">
                <button type="button" @click="closeModal" class="btn-text">Discard</button>
                <button type="submit" class="btn btn-primary" :disabled="submitting">
                  {{ submitting ? 'Synchronizing...' : (editingItem ? 'Update Asset' : 'Initialize Asset') }}
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

export default {
  data() {
    return {
      items: [],
      loading: true,
      submitting: false,
      showModal: false,
      editingItem: null,
      searchQuery: '',
      selectedCategory: 'All Assets',
      categories: ['All Assets', 'Main Course', 'Appetizers', 'Beverages', 'Desserts', 'Specials'],
      form: {
        name: '',
        description: '',
        price: '',
        category: 'Main Course',
        image: null
      },
      imagePreview: null
    };
  },
  computed: {
    filteredItems() {
      return this.items.filter(item => {
        const matchesSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase());
        const matchesCat = this.selectedCategory === 'All Assets' || item.category === this.selectedCategory;
        return matchesSearch && matchesCat;
      });
    }
  },
  async created() {
    await this.fetchItems();
  },
  methods: {
    async fetchItems() {
      this.loading = true;
      try {
        const res = await axios.get('/api/menu-items');
        this.items = res.data;
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    openAddModal() {
      this.editingItem = null;
      this.form = { name: '', description: '', price: '', category: 'Main Course', image: null };
      this.imagePreview = null;
      this.showModal = true;
    },
    editItem(item) {
      this.editingItem = item;
      this.form = { 
        name: item.name, 
        description: item.description, 
        price: item.price, 
        category: item.category,
        is_available: item.is_available
      };
      this.imagePreview = item.image_url;
      this.showModal = true;
    },
    handleFileUpload(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.form.image = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    async saveItem() {
      this.submitting = true;
      const formData = new FormData();
      Object.keys(this.form).forEach(key => {
        if (this.form[key] !== null && this.form[key] !== undefined) {
          // Convert booleans to 0/1 so PHP reads them correctly
          if (typeof this.form[key] === 'boolean') {
            formData.append(key, this.form[key] ? '1' : '0');
          } else {
            formData.append(key, this.form[key]);
          }
        }
      });

      try {
        if (this.editingItem) {
          formData.append('_method', 'PUT');
          const res = await axios.post(`/api/menu-items/${this.editingItem.id}`, formData);
          const idx = this.items.findIndex(i => i.id === this.editingItem.id);
          this.items[idx] = res.data;
        } else {
          const res = await axios.post('/api/menu-items', formData);
          this.items.push(res.data);
        }
        this.closeModal();
      } catch (err) {
        alert('Data corruption during link. Verification failed.');
      } finally {
        this.submitting = false;
      }
    },
    async toggleAvailability(item) {
      try {
        item.is_available = !item.is_available;
        await axios.put(`/api/menu-items/${item.id}`, {
          name: item.name,
          price: item.price,
          category: item.category,
          description: item.description,
          is_available: item.is_available ? 1 : 0
        });
      } catch (err) {
        item.is_available = !item.is_available; // rollback
      }
    },
    async deleteItem(id) {
      if (!confirm('Execute purge for this culinary asset?')) return;
      try {
        await axios.delete(`/api/menu-items/${id}`);
        this.items = this.items.filter(i => i.id !== id);
      } catch (err) {
        alert('Purge sequence aborted.');
      }
    },
    closeModal() {
      this.showModal = false;
      this.editingItem = null;
    }
  }
};
</script>

<style scoped>
.menu-manager { color: #1e293b; }

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
  color: #1e293b;
}

.highlight { color: #22c55e; }

.page-subtitle { color: #64748b; font-weight: 500; font-size: 1rem; }

.white-card {
  background: white;
  border-radius: 20px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}

.filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  margin-bottom: 2rem;
  gap: 2rem;
}

.search-input {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #f1f5f9;
  padding: 10px 18px;
  border-radius: 12px;
  flex: 1;
}

.search-input input {
  background: transparent;
  border: none;
  color: #1e293b;
  width: 100%;
  outline: none;
  font-weight: 500;
}

.search-input svg { width: 18px; color: #64748b; }

.category-tabs {
  display: flex;
  gap: 6px;
}

.tab {
  padding: 8px 16px;
  border-radius: 10px;
  border: none;
  background: transparent;
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.tab:hover { color: #1e293b; background: #f1f5f9; }
.tab.active { background: #22c55e20; color: #22c55e; }

.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.menu-card {
  overflow: hidden;
  transition: all 0.3s;
}

.menu-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.card-image {
  height: 180px;
  position: relative;
  background: #f8fafc;
}

.card-image img {
  width: 100%; height: 100%; object-fit: cover;
}

.image-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  color: #cbd5e1;
}

.image-placeholder svg { width: 44px; }

.category-tag {
  position: absolute;
  top: 12px; right: 12px;
  background: rgba(255,255,255,0.9);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.65rem;
  font-weight: 800;
  text-transform: uppercase;
  color: #1e293b;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-content { padding: 1.25rem; }

.card-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.item-name { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.item-price { font-weight: 800; color: #22c55e; font-size: 1.1rem; }

.item-desc {
  font-size: 0.85rem;
  color: #64748b;
  line-height: 1.5;
  margin-bottom: 1.5rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.availability-toggle {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  user-select: none;
}

.toggle-track {
  width: 34px; height: 16px;
  background: #22c55e;
  border-radius: 20px;
  position: relative;
  transition: all 0.3s;
}

.toggle-thumb {
  width: 12px; height: 12px;
  background: white;
  border-radius: 50%;
  position: absolute;
  top: 2px; right: 2px;
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
}

.disabled .toggle-track { background: #e2e8f0; }
.disabled .toggle-thumb { right: 20px; }
.availability-toggle span { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.disabled span { color: #94a3b8; }

.icon-btn {
  width: 32px; height: 32px;
  border-radius: 8px;
  border: none; background: #f1f5f9;
  color: #64748b;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s;
}

.icon-btn:hover { background: #e2e8f0; color: #1e293b; }
.icon-btn.danger:hover { color: #ef4444; background: #fef2f2; }

/* Modals */
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(8px);
  z-index: 2000; display: flex; align-items: center; justify-content: center;
  padding: 2rem;
}

.premium-modal {
  background: white;
  border-radius: 24px;
  max-width: 550px; width: 100%;
  overflow: hidden;
}

.modal-inner { padding: 2rem; }
.modal-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.close-btn { background: transparent; border: none; color: #64748b; font-size: 2rem; cursor: pointer; line-height: 1; }

.btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px; border-radius: 12px; font-weight: 600; border: none; cursor: pointer;
  transition: all 0.3s;
}

.btn-primary { background: #22c55e; color: white; }
.btn-primary:hover { background: #16a34a; transform: translateY(-2px); }

/* Form Styles */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
.full { grid-column: span 2; }

.modern-input {
  width: 100%; background: transparent; border: none; border-bottom: 2px solid #f1f5f9;
  padding: 12px 0; color: #1e293b; font-size: 1rem; outline: none; transition: all 0.3s;
}

.input-container { position: relative; }
.input-container label {
  position: absolute; top: 12px; left: 0; color: #94a3b8;
  transition: all 0.3s; pointer-events: none; font-weight: 500;
}

.modern-input:focus ~ label,
.modern-input:not(:placeholder-shown) ~ label { top: -12px; font-size: 0.8rem; color: #22c55e; }

.input-underline { position: absolute; bottom: 0; left: 0; height: 2px; width: 0; background: #22c55e; transition: width 0.3s; }
.modern-input:focus ~ .input-underline { width: 100%; }

.file-drop-zone {
  width: 100%; aspect-ratio: 16/7; border: 2px dashed #f1f5f9; border-radius: 16px;
  display: flex; align-items: center; justify-content: center; cursor: pointer;
}

.upload-hint { text-align: center; color: #94a3b8; }
.upload-hint svg { width: 32px; display: block; margin: 0 auto 8px; }
.preview-wrap img { width: 100%; height: 100%; object-fit: cover; border-radius: 14px; }

.loading-state { text-align: center; padding: 4rem; color: #64748b; }
.modern-spinner {
  width: 40px; height: 40px; border: 3px solid #f1f5f9; border-top-color: #22c55e;
  border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1rem;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
