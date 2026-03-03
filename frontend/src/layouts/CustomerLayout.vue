<template>
  <div class="customer-app">
    <div class="mobile-container">
      <router-view v-slot="{ Component }">
        <transition name="page-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
      
      <!-- Bottom Navigation for Customer -->
      <nav class="bottom-nav">
        <router-link :to="menuPath" class="nav-item" active-class="active">
          <Utensils :size="20" />
          <span>Menu</span>
        </router-link>
        <router-link :to="historyPath" class="nav-item" active-class="active">
          <History :size="20" />
          <span>History</span>
        </router-link>
      </nav>
    </div>
  </div>
</template>

<script>
import { Utensils, History } from 'lucide-vue-next';

export default {
  name: 'CustomerLayout',
  components: { Utensils, History },
  computed: {
    token() {
      return this.$route.params.token;
    },
    menuPath() {
      return `/menu/${this.token}`;
    },
    historyPath() {
      return `/menu/${this.token}/history`;
    }
  }
};
</script>

<style scoped>
.customer-app {
  background: #f8fafc;
  min-height: 100vh;
  display: flex;
  justify-content: center;
}

.mobile-container {
  width: 100%;
  max-width: 500px;
  background: white;
  min-height: 100vh;
  position: relative;
  box-shadow: 0 0 20px rgba(0,0,0,0.05);
  padding-bottom: 80px; /* Space for bottom nav */
}

.bottom-nav {
  position: fixed;
  bottom: 0;
  width: 100%;
  max-width: 500px;
  background: white;
  display: flex;
  justify-content: space-around;
  padding: 12px 0;
  border-top: 1px solid #f1f5f9;
  z-index: 1000;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  color: #64748b;
  text-decoration: none;
  font-size: 0.75rem;
  font-weight: 600;
  transition: all 0.2s;
}

.nav-item.active {
  color: #22c55e;
}

.page-fade-enter-active,
.page-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.page-fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
