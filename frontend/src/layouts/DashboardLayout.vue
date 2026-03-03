<template>
  <div class="app-layout">
    <!-- Left Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-top">
        <div class="brand">
          <div class="brand-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
          </div>
          <div class="brand-text">
            <h2>Mlup Dong</h2>
            <span>Restaurant Admin</span>
          </div>
        </div>

        <nav class="nav-menu">
          <router-link 
            v-for="item in navItems" 
            :key="item.id"
            :to="item.path"
            class="nav-link"
            active-class="active"
          >
            <component :is="item.icon" :size="20" />
            <span>{{ item.label }}</span>
          </router-link>
        </nav>
      </div>

      <div class="sidebar-bottom">
        <div class="user-profile">
          <img :src="userAvatar" :alt="userName" class="avatar">
          <div class="user-info">
            <span class="user-name">{{ userName }}</span>
            <span class="user-role">{{ userRole }}</span>
          </div>
          <button @click="logout" class="logout-btn" title="Logout">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
      <!-- Top Header -->
      <header class="top-header">
        <div class="search-box">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" placeholder="Search analytics...">
        </div>
        
        <div class="header-right">
          <button class="header-icon-btn">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="notif-dot"></span>
          </button>
          <button class="header-icon-btn">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
          </button>
          <div class="date-chip">
            {{ currentDate }}
          </div>
        </div>
      </header>

      <!-- View Content -->
      <main class="content-body">
        <router-view v-slot="{ Component }">
          <transition name="fade-slide" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script>
import { 
  LayoutDashboard, 
  UtensilsCrossed, 
  ChefHat,
  Users, 
  Armchair, 
  BarChart3, 
  Settings,
  LayoutGrid
} from 'lucide-vue-next';

export default {
  name: 'DashboardLayout',
  components: {
    LayoutDashboard, UtensilsCrossed, ChefHat, Users, Armchair, BarChart3, Settings, LayoutGrid
  },
  data() {
    return {
      userRole: localStorage.getItem('user_role') || 'admin',
      userName: localStorage.getItem('user_name') || 'Administrator',
      fullNavItems: [
        { id: 'dashboard', label: 'Dashboard', icon: 'LayoutDashboard', path: '/', roles: ['admin'] },
        { id: 'menu', label: 'Menu', icon: 'UtensilsCrossed', path: '/menu-manager', roles: ['admin'] },
        { id: 'staff', label: 'Staff', icon: 'Users', path: '/staff', roles: ['admin'] },
        { id: 'tables', label: 'Tables', icon: 'Armchair', path: '/tables', roles: ['admin'] },
        { id: 'reports', label: 'Sales Reports', icon: 'BarChart3', path: '/reports', roles: ['admin'] },
        { id: 'settings', label: 'Settings', icon: 'Settings', path: '/settings', roles: ['admin'] },
        { id: 'floor-plan', label: 'Floor Plan', icon: 'LayoutGrid', path: '/floor-plan', roles: ['waiter'] },
        { id: 'kitchen', label: 'Kitchen KDS', icon: 'ChefHat', path: '/kitchen', roles: ['chef'] }
      ]
    };
  },
  computed: {
    navItems() {
      return this.fullNavItems.filter(item => item.roles.includes(this.userRole));
    },
    userAvatar() {
      const color = this.userRole === 'admin' ? '22c55e' : (this.userRole === 'chef' ? 'facc15' : '3b82f6');
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(this.userName)}&background=${color}&color=fff`;
    },
    currentDate() {
      return new Intl.DateTimeFormat('en-GB', { 
        day: 'numeric', month: 'short', year: 'numeric' 
      }).format(new Date());
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('user_role');
      localStorage.removeItem('user_name');
      this.$router.push({ name: 'login' });
    }
  }
};
</script>

<style scoped>
/* Scoped styles from App.vue sidebar/layout logic */
.app-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 280px;
  background: white;
  border-right: 1px solid var(--border-light);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 24px;
  position: fixed;
  height: 100vh;
  z-index: 100;
}

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 40px;
}

.brand-icon {
  width: 40px;
  height: 40px;
  background: var(--primary);
  color: white;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.brand-text h2 {
  font-size: 1.15rem;
  font-weight: 800;
  color: #1e293b;
}

.brand-text span {
  font-size: 0.75rem;
  color: var(--text-muted);
  font-weight: 500;
}

.nav-menu {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 16px;
  border-radius: 12px;
  border: none;
  background: transparent;
  color: var(--text-muted);
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
}

.nav-link:hover {
  background: #f8fafc;
  color: var(--text-main);
}

.nav-link.active {
  background: var(--primary-soft);
  color: var(--primary);
}

.sidebar-bottom {
  padding-top: 24px;
  border-top: 1px solid var(--border-light);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
}

.user-info {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 0.95rem;
  font-weight: 700;
  color: #1e293b;
}

.user-role {
  font-size: 0.75rem;
  color: var(--text-muted);
  font-weight: 500;
}

.logout-btn {
  background: transparent;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
}

.main-wrapper {
  margin-left: 280px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.top-header {
  height: 80px;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--border-light);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 40px;
  position: sticky;
  top: 0;
  z-index: 90;
}

.search-box {
  background: #f1f5f9;
  padding: 10px 16px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 12px;
  width: 350px;
}

.search-box input {
  background: transparent;
  border: none;
  outline: none;
  width: 100%;
  font-family: inherit;
  font-size: 0.9rem;
  font-weight: 500;
}

.search-box svg { color: var(--text-muted); }

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-icon-btn {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  border: none;
  background: #f1f5f9;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
}

.notif-dot {
  position: absolute;
  top: 10px; right: 10px;
  width: 8px; height: 8px;
  background: #ef4444;
  border-radius: 50%;
  border: 2px solid white;
}

.date-chip {
  padding: 8px 16px;
  background: white;
  border: 1px solid var(--border-light);
  border-radius: 10px;
  font-size: 0.85rem;
  font-weight: 600;
  color: #1e293b;
}

.content-body {
  padding: 40px;
  flex: 1;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media (max-width: 1200px) {
  .sidebar { width: 80px; padding: 24px 15px; }
  .nav-link span, .brand-text, .user-info { display: none; }
  .main-wrapper { margin-left: 80px; }
}
</style>
