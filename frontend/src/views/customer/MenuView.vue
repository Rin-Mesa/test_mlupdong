<template>
  <div class="menu-view glass-bg">
    <!-- Premium Hero Section -->
    <div class="hero-section">
      <div class="hero-overlay"></div>
      <img src="/images/hero-banner.png" class="hero-img" alt="Top Gastronomy">
      <div class="hero-content fade-in">
        <div class="header-top">
          <div class="brand">
            <div class="logo-circle">🌿</div>
            <div class="brand-info">
              <h1>Mlup Dong</h1>
              <span class="location">Boutique Kitchen • Phnom Penh</span>
            </div>
          </div>
          <div class="table-badge">
            <div class="pulse-dot"></div>
            <span>Table {{ table?.number || '...' }}</span>
          </div>
        </div>
        <div class="welcome-text">
          <h2>Indulge in <span class="accent">Fine Khmer Cuisine</span></h2>
          <p>Scan, order, and savor our signature dishes crafted for your pleasure.</p>
        </div>
      </div>
    </div>

    <!-- Floating Order Navigation -->
    <div class="sticky-nav">
      <div class="search-wrap">
        <Search :size="18" class="search-icon" />
        <input v-model="searchQuery" type="text" placeholder="Discover your next favorite meal...">
      </div>
      <div class="categories-container">
        <div class="categories-scroll">
          <button 
            v-for="cat in categories" 
            :key="cat"
            @click="activeCategory = cat"
            :class="['premium-cat-btn', { active: activeCategory === cat }]"
          >
            <component :is="getCategoryIconComp(cat)" :size="14" class="cat-icon" />
            {{ cat }}
          </button>
        </div>
      </div>
    </div>

    <!-- Menu Feed -->
    <main class="menu-feed">
      <!-- Signature Showcase (Slider/Special) -->
      <section v-if="activeCategory === 'All' && !searchQuery" class="showcase-section">
        <div class="section-title">
          <div class="accent-line"></div>
          <h3>Signature Showcase</h3>
        </div>
        <div class="premium-slider">
          <div v-for="item in chefSpecials" :key="item.id" class="spec-card">
            <img :src="item.image_url" :alt="item.name" class="spec-img">
            <div class="spec-overlay">
              <div class="spec-badge">SIGNATURE</div>
              <div class="spec-meta">
                <h4>{{ item.name }}</h4>
                <p>{{ item.description }}</p>
                <div class="spec-footer">
                  <span class="price">${{ item.price }}</span>
                  <button class="add-special-btn" @click="addToCart(item)">
                    <PlusSquare :size="24" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Main Category Grid -->
      <section v-for="category in sortedCategories" :key="category" class="category-block">
        <div class="category-header">
          <h3>{{ category }}</h3>
          <span class="count">{{ getItemsByCategory(category).length }} Items</span>
        </div>

        <div class="items-grid">
          <div v-for="item in getItemsByCategory(category)" :key="item.id" class="modern-item-card">
            <div class="card-asset">
              <img :src="item.image_url" :alt="item.name">
              <div v-if="!item.is_available" class="sold-out-overlay">
                <span>Sold Out</span>
              </div>
              <button 
                v-if="item.is_available" 
                class="quick-add" 
                @click="addToCart(item)"
                :class="{ 'adding': addingId === item.id }"
              >
                <Plus :size="20" />
              </button>
            </div>
            <div class="card-details">
              <div class="d-flex">
                <h4 class="item-name">{{ item.name }}</h4>
                <span class="item-price">${{ item.price }}</span>
              </div>
              <p class="item-desc">{{ item.description }}</p>
              <div class="item-stats">
                <div class="rating">
                  <Star :size="12" fill="#fbbf24" color="#fbbf24" />
                  <span>4.9</span>
                </div>
                <div class="time">
                  <Clock :size="12" />
                  <span>~15m</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Elevated Cart Bar -->
    <Transition name="slide-up">
      <div v-if="cartCount > 0" class="unified-cart-bar-wrap">
        <div class="unified-cart-bar" @click="showCart = true">
          <div class="bar-left">
            <div class="cart-stack">
              <ShoppingBag :size="20" />
              <div class="badge">{{ cartCount }}</div>
            </div>
            <div class="price-stack">
              <span class="label">SUBTOTAL</span>
              <span class="total">${{ cartTotal.toFixed(2) }}</span>
            </div>
          </div>
          <button class="checkout-hint-btn">
            View Order <ArrowRight :size="18" />
          </button>
        </div>
      </div>
    </Transition>

    <!-- Modal Cart Drawer -->
    <Transition name="fade">
      <div v-if="showCart" class="premium-drawer-overlay" @click.self="showCart = false">
        <div class="premium-drawer">
          <div class="drawer-header">
            <div class="indicator"></div>
            <div class="header-row">
              <h3>In My Selection</h3>
              <button class="close-drawer" @click="showCart = false"><X :size="20" /></button>
            </div>
          </div>
          
          <div class="drawer-content">
            <div v-if="cart.length === 0" class="empty-cart">
              <div class="empty-icon">🍽️</div>
              <p>Your culinary journey starts here. Add items to your selection.</p>
              <button class="btn btn-primary" @click="showCart = false">Explore Menu</button>
            </div>
            <div v-else class="cart-items-list">
              <div v-for="item in cart" :key="item.id" class="cart-item-row">
                <div class="row-asset">
                  <img :src="item.image_url" :alt="item.name">
                </div>
                <div class="row-info">
                  <div class="row-meta">
                    <h4>{{ item.name }}</h4>
                    <span class="row-price">${{ (item.price * item.quantity).toFixed(2) }}</span>
                  </div>
                  <div class="row-controls">
                    <div class="notes-wrap">
                      <input 
                        type="text" 
                        v-model="item.notes" 
                        placeholder="Add special requests..."
                        @change="saveCart"
                      >
                    </div>
                    <div class="qty-stepper">
                      <button @click="updateQty(item, -1)"><Minus :size="12" /></button>
                      <span>{{ item.quantity }}</span>
                      <button @click="updateQty(item, 1)"><Plus :size="12" /></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="drawer-footer">
            <div class="summary-line">
              <span>Subtotal</span>
              <span>${{ cartTotal.toFixed(2) }}</span>
            </div>
            <div class="summary-line tax">
              <span>Includes 10% Service Tax</span>
              <span>${{ (cartTotal * 0.1).toFixed(2) }}</span>
            </div>
            <button class="mega-checkout-btn" @click="placeOrder" :disabled="isPlacingOrder">
              <span v-if="!isPlacingOrder">Finalize & Send Order</span>
              <div v-else class="spinner"></div>
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import { 
  Search, Armchair, Star, Plus, 
  ShoppingBag, ArrowRight, X, Minus,
  Clock, PlusSquare, Utensils, Coffee,
  IceCream, Gift, ChevronRight
} from 'lucide-vue-next';
import axios from 'axios';

export default {
  name: 'PremiumMenuView',
  components: { 
    Search, Armchair, Star, Plus, 
    ShoppingBag, ArrowRight, X, Minus,
    Clock, PlusSquare, Utensils, Coffee,
    IceCream, Gift, ChevronRight
  },
  data() {
    return {
      table: null,
      menuItems: [],
      categories: ['All', 'Main Course', 'Appetizers', 'Beverages', 'Desserts'],
      activeCategory: 'All',
      searchQuery: '',
      cart: [],
      showCart: false,
      isPlacingOrder: false,
      addingId: null
    };
  },
  computed: {
    chefSpecials() {
      return this.menuItems.filter(i => i.is_available).slice(0, 3);
    },
    sortedCategories() {
      const catsInUse = [...new Set(this.menuItems.map(item => item.category))];
      if (this.activeCategory !== 'All') {
        return catsInUse.includes(this.activeCategory) ? [this.activeCategory] : [];
      }
      return catsInUse;
    },
    cartCount() {
      return this.cart.reduce((sum, item) => sum + item.quantity, 0);
    },
    cartTotal() {
      return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    }
  },
  async created() {
    await this.fetchTable();
    await this.fetchMenu();
    this.loadCart();
  },
  methods: {
    async fetchTable() {
      try {
        const res = await axios.get(`/api/tables/token/${this.$route.params.token}`);
        this.table = res.data;
      } catch (err) {
        console.error('Error fetching table', err);
      }
    },
    async fetchMenu() {
      try {
        const res = await axios.get('/api/menu-items');
        this.menuItems = res.data;
      } catch (err) {
        console.error('Error fetching menu', err);
      }
    },
    getCategoryIconComp(cat) {
      const maps = {
        'Main Course': 'Utensils',
        'Appetizers': 'Star',
        'Beverages': 'Coffee',
        'Desserts': 'IceCream',
        'Specials': 'Gift',
        'All': 'LayoutGrid'
      };
      return maps[cat] || 'Utensils';
    },
    getItemsByCategory(cat) {
      return this.menuItems.filter(item => {
        const matchCat = cat === 'All' || item.category === cat;
        const matchSearch = item.name.toLowerCase().includes(this.searchQuery.toLowerCase());
        return matchCat && matchSearch;
      });
    },
    addToCart(item) {
      this.addingId = item.id;
      setTimeout(() => { this.addingId = null; }, 1000);

      const existing = this.cart.find(i => i.id === item.id);
      if (existing) {
        existing.quantity++;
      } else {
        this.cart.push({ ...item, quantity: 1, notes: '' });
      }
      this.saveCart();
    },
    updateQty(item, delta) {
      item.quantity += delta;
      if (item.quantity <= 0) {
        this.cart = this.cart.filter(i => i.id !== item.id);
      }
      this.saveCart();
    },
    saveCart() {
      localStorage.setItem(`cart_${this.$route.params.token}`, JSON.stringify(this.cart));
    },
    loadCart() {
      const saved = localStorage.getItem(`cart_${this.$route.params.token}`);
      if (saved) this.cart = JSON.parse(saved);
    },
    async placeOrder() {
      if (this.cart.length === 0) return;
      this.isPlacingOrder = true;
      try {
        const res = await axios.post('/api/orders', {
          table_id: this.table.id,
          items: this.cart.map(i => ({ 
            id: i.id, 
            quantity: i.quantity,
            notes: i.notes || null
          })),
          customer_name: 'Guest'
        });
        
        const history = JSON.parse(localStorage.getItem('order_history') || '[]');
        history.push(res.data.order.id);
        localStorage.setItem('order_history', JSON.stringify(history));

        this.cart = [];
        this.saveCart();
        this.showCart = false;
        this.$router.push({ 
          name: 'order-status', 
          params: { 
            token: this.$route.params.token, 
            id: res.data.order.id 
          } 
        });
      } catch (err) {
        alert('Transaction interrupted. Please consult personnel.');
      } finally {
        this.isPlacingOrder = false;
      }
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap');

.menu-view {
  font-family: 'Outfit', sans-serif;
  background: #fdfdfd;
  min-height: 100vh;
  color: #1e293b;
}

/* Hero Section */
.hero-section {
  position: relative;
  height: 340px;
  overflow: hidden;
  border-bottom-left-radius: 40px;
  border-bottom-right-radius: 40px;
}

.hero-img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 10s ease;
}

.hero-section:hover .hero-img { transform: scale(1.1); }

.hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.8));
  z-index: 1;
}

.hero-content {
  position: absolute; inset: 0;
  padding: 24px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  z-index: 2;
  color: white;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand { display: flex; align-items: center; gap: 12px; }
.logo-circle {
  width: 48px; height: 48px;
  background: rgba(255,255,255,0.2);
  backdrop-filter: blur(10px);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
}

.brand-info h1 { font-size: 1.5rem; font-weight: 800; line-height: 1; margin: 0; }
.location { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #22c55e; letter-spacing: 0.5px; }

.table-badge {
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(14px);
  padding: 8px 16px;
  border-radius: 100px;
  display: flex; align-items: center; gap: 8px;
  font-size: 0.85rem; font-weight: 700;
  border: 1px solid rgba(255,255,255,0.1);
}

.pulse-dot {
  width: 8px; height: 8px;
  background: #22c55e;
  border-radius: 50%;
  box-shadow: 0 0 10px #22c55e;
  animation: pulse 2s infinite;
}

.welcome-text h2 { font-size: 2rem; font-weight: 800; line-height: 1.1; margin-bottom: 8px; }
.accent { color: #22c55e; }
.welcome-text p { font-size: 0.9rem; font-weight: 400; opacity: 0.8; max-width: 80%; }

/* Sticky Navigation */
.sticky-nav {
  position: sticky; top: 0;
  background: rgba(255,255,255,0.8);
  backdrop-filter: blur(20px);
  padding: 16px 20px;
  z-index: 100;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.search-wrap {
  display: flex; align-items: center; gap: 12px;
  background: #f1f5f9;
  padding: 12px 18px;
  border-radius: 16px;
  margin-bottom: 16px;
}
.search-icon { color: #94a3b8; }
.search-wrap input {
  background: transparent; border: none; outline: none;
  width: 100%; font-family: inherit; font-weight: 600; font-size: 0.9rem;
}

.categories-scroll {
  display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;
}
.categories-scroll::-webkit-scrollbar { display: none; }

.premium-cat-btn {
  white-space: nowrap;
  padding: 10px 18px;
  border-radius: 100px;
  border: 1px solid #f1f5f9;
  background: white;
  color: #64748b;
  font-size: 0.8rem; font-weight: 700;
  cursor: pointer;
  display: flex; align-items: center; gap: 6px;
  transition: all 0.3s;
}

.premium-cat-btn.active {
  background: #1e293b; color: white; border-color: #1e293b;
  transform: scale(1.05);
}

/* Menu Content */
.menu-feed { padding: 24px 20px 140px; }

.section-title { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
.accent-line { width: 40px; height: 3px; background: #22c55e; border-radius: 2px; }
.section-title h3 { font-size: 1.25rem; font-weight: 800; color: #1e293b; }

.premium-slider {
  display: flex; gap: 16px; overflow-x: auto; padding-bottom: 10px;
}
.premium-slider::-webkit-scrollbar { display: none; }

.spec-card {
  min-width: 300px;
  height: 200px;
  border-radius: 24px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.spec-img { width: 100%; height: 100%; object-fit: cover; }
.spec-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
  padding: 16px;
  display: flex; flex-direction: column; justify-content: flex-end;
}

.spec-badge {
  position: absolute; top: 12px; left: 12px;
  background: #22c55e; color: #fff; padding: 4px 10px; border-radius: 8px;
  font-size: 0.6rem; font-weight: 800;
}

.spec-meta h4 { color: white; font-size: 1.1rem; margin-bottom: 4px; font-weight: 800; }
.spec-meta p { color: rgba(255,255,255,0.7); font-size: 0.75rem; margin-bottom: 12px; line-height: 1.3; }

.spec-footer { display: flex; justify-content: space-between; align-items: center; }
.spec-footer .price { color: #fff; font-weight: 800; font-size: 1.25rem; }
.add-special-btn { background: transparent; border: none; color: #22c55e; cursor: pointer; }

/* Grid Items */
.category-block { margin-top: 40px; }
.category-header {
  display: flex; justify-content: space-between; align-items: flex-end;
  margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;
}
.category-header h3 { font-size: 1.15rem; font-weight: 800; text-transform: capitalize; }
.category-header .count { font-size: 0.7rem; font-weight: 700; color: #94a3b8; }

.items-grid { display: flex; flex-direction: column; gap: 20px; }

.modern-item-card {
  display: flex; gap: 16px; background: white; border-radius: 24px;
  padding: 12px; border: 1px solid #f8fafc;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}

.card-asset { position: relative; width: 110px; height: 110px; flex-shrink: 0; }
.card-asset img { width: 100%; height: 100%; object-fit: cover; border-radius: 18px; }

.quick-add {
  position: absolute; bottom: -8px; right: -8px;
  width: 38px; height: 38px; border-radius: 50%;
  background: white; color: #1e293b; border: none;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  cursor: pointer; transition: 0.3s;
}
.quick-add:active { transform: scale(0.85); background: #22c55e; color: white; }

.sold-out-overlay {
  position: absolute; inset: 0; background: rgba(255,255,255,0.7);
  backdrop-filter: blur(2px); border-radius: 18px;
  display: flex; align-items: center; justify-content: center;
}
.sold-out-overlay span { font-size: 0.7rem; font-weight: 800; color: #ef4444; border: 1.5px solid #ef4444; padding: 2px 6px; border-radius: 6px; }

.card-details { flex: 1; display: flex; flex-direction: column; justify-content: center; }
.d-flex { display: flex; justify-content: space-between; align-items: flex-start; }
.item-name { font-size: 1rem; font-weight: 700; color: #1e293b; }
.item-price { font-weight: 800; color: #22c55e; }
.item-desc { 
  font-size: 0.75rem; 
  color: #64748b; 
  line-clamp: 2;
  -webkit-line-clamp: 2; 
  -webkit-box-orient: vertical; 
  display: -webkit-box; 
  overflow: hidden; 
  margin: 4px 0 10px; 
}

.item-stats { display: flex; gap: 14px; }
.item-stats > div { display: flex; align-items: center; gap: 4px; font-size: 0.65rem; font-weight: 700; color: #94a3b8; }

/* Unified Cart Bar */
.unified-cart-bar-wrap {
  position: fixed; bottom: 30px; left: 0; width: 100%;
  display: flex; justify-content: center; padding: 0 20px; z-index: 1000;
}

.unified-cart-bar {
  width: 100%; max-width: 480px;
  background: #1e293b; color: white;
  padding: 12px 14px 12px 20px; border-radius: 20px;
  display: flex; justify-content: space-between; align-items: center;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
  cursor: pointer;
}

.bar-left { display: flex; align-items: center; gap: 20px; }
.cart-stack { position: relative; }
.cart-stack .badge {
  position: absolute; top: -10px; right: -10px;
  background: #22c55e; padding: 2px 6px; border-radius: 100px;
  font-size: 0.65rem; font-weight: 800;
}

.price-stack { display: flex; flex-direction: column; }
.price-stack .label { font-size: 0.6rem; color: #94a3b8; font-weight: 800; letter-spacing: 0.5px; }
.price-stack .total { font-size: 1.1rem; font-weight: 800; }

.checkout-hint-btn {
  background: #22c55e; color: #000; border: none;
  padding: 12px 20px; border-radius: 16px; font-weight: 800;
  display: flex; align-items: center; gap: 8px;
}

/* Drawer UI */
.premium-drawer-overlay {
  position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(8px); z-index: 2000;
  display: flex; align-items: flex-end;
}

.premium-drawer {
  width: 100%; background: white; border-radius: 32px 32px 0 0;
  padding: 0 24px 40px; max-height: 90vh; display: flex; flex-direction: column;
}

.drawer-header { margin-bottom: 24px; }
.indicator { width: 40px; height: 4px; background: #e2e8f0; border-radius: 10px; margin: 12px auto 20px; }
.header-row { display: flex; justify-content: space-between; align-items: center; }
.header-row h3 { font-size: 1.4rem; font-weight: 800; }
.close-drawer { background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b; }

.drawer-content { flex: 1; overflow-y: auto; padding-bottom: 20px; }
.cart-items-list { display: flex; flex-direction: column; gap: 16px; }

.cart-item-row { display: flex; gap: 16px; padding: 12px; background: #f8fafc; border-radius: 20px; }
.row-asset img { width: 64px; height: 64px; border-radius: 14px; object-fit: cover; }
.row-info { flex: 1; display: flex; flex-direction: column; gap: 8px; }
.row-meta { display: flex; justify-content: space-between; align-items: flex-start; }
.row-meta h4 { font-size: 0.9rem; font-weight: 700; width: 70%; }
.row-price { font-weight: 800; color: #22c55e; font-size: 0.95rem; }

.row-controls { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.notes-wrap { flex: 1; }
.notes-wrap input { width: 100%; border: none; background: white; padding: 8px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 600; outline: none; }

.qty-stepper { display: flex; align-items: center; gap: 14px; background: white; padding: 4px 10px; border-radius: 10px; }
.qty-stepper button { border: none; background: #f1f5f9; width: 22px; height: 22px; border-radius: 6px; color: #1e293b; display: flex; align-items: center; justify-content: center; }
.qty-stepper span { font-size: 0.85rem; font-weight: 800; }

.drawer-footer { border-top: 1px solid #f1f5f9; padding-top: 24px; display: flex; flex-direction: column; gap: 12px; }
.summary-line { display: flex; justify-content: space-between; font-weight: 800; font-size: 1.1rem; }
.summary-line.tax { font-size: 0.7rem; color: #94a3b8; font-weight: 600; margin-top: -8px; }

.mega-checkout-btn {
  background: #22c55e; color: #000; border: none; height: 64px; border-radius: 18px;
  font-size: 1.1rem; font-weight: 800; cursor: pointer; transition: 0.3s;
  display: flex; align-items: center; justify-content: center; margin-top: 8px;
}
.mega-checkout-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.spinner { width: 24px; height: 24px; border: 3px solid rgba(0,0,0,0.1); border-top-color: #000; border-radius: 50%; animation: spin 0.8s linear infinite; }

@keyframes pulse {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.5); opacity: 0.4; }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes spin { to { transform: rotate(360deg); } }

.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100px); opacity: 0; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
