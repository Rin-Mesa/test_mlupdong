<template>
  <div class="login-view">
    <div class="login-container fade-in">
      <div class="login-header">
        <div class="logo">🌿</div>
        <h1>MLUP DONG</h1>
        <p v-if="!selectedRole">Select your portal to continue</p>
        <p v-else>Login as <strong>{{ selectedRole.name }}</strong></p>
      </div>

      <!-- Role Selection -->
      <div v-if="!selectedRole" class="role-grid">
        <button 
          v-for="role in roles" 
          :key="role.id" 
          class="role-card"
          @click="selectedRole = role"
        >
          <div class="role-icon" :style="{ background: role.color }">
            <component :is="role.icon" :size="32" color="white" />
          </div>
          <div class="role-info">
            <h3>{{ role.name }}</h3>
            <p>{{ role.desc }}</p>
          </div>
          <ArrowRight :size="20" class="arrow" />
        </button>
      </div>

      <!-- Password Form -->
      <div v-else class="login-form-card">
        <button class="back-link" @click="selectedRole = null">
          <ArrowLeft :size="16" /> Back to roles
        </button>

        <form @submit.prevent="handleLogin" class="modern-form">
          <div class="input-group">
            <label>Email Address</label>
            <div class="input-wrapper">
              <Mail :size="18" />
              <input 
                type="email" 
                v-model="form.email" 
                placeholder="chef@mlupdong.com"
                required
              >
            </div>
          </div>

          <div class="input-group">
            <label>Security Password</label>
            <div class="input-wrapper">
              <Lock :size="18" />
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="form.password" 
                placeholder="••••••••"
                required
              >
              <button type="button" class="toggle-pass" @click="showPassword = !showPassword">
                <Eye v-if="!showPassword" :size="18" />
                <EyeOff v-else :size="18" />
              </button>
            </div>
          </div>

          <div v-if="error" class="error-msg">
            <AlertCircle :size="16" />
            <span>{{ error }}</span>
          </div>

          <button type="submit" class="login-submit" :disabled="loading">
            <span v-if="!loading">Unlock Portal</span>
            <div v-else class="spinner"></div>
          </button>
          
          <p class="hint">Default password is <strong>password</strong></p>
        </form>
      </div>

      <div class="login-footer">
        <p>Internal access only. Unauthorized entry is logged.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { 
  ShieldCheck, ChefHat, UserCircle2, ArrowRight, 
  ArrowLeft, Mail, Lock, Eye, EyeOff, AlertCircle 
} from 'lucide-vue-next';
import axios from 'axios';

export default {
  name: 'LoginView',
  components: { 
    ShieldCheck, ChefHat, UserCircle2, ArrowRight, 
    ArrowLeft, Mail, Lock, Eye, EyeOff, AlertCircle 
  },
  data() {
    return {
      selectedRole: null,
      showPassword: false,
      loading: false,
      error: null,
      form: {
        email: '',
        password: ''
      },
      roles: [
        { 
          id: 'admin', 
          name: 'Administrator', 
          desc: 'Manage menu, staff, and view reports.', 
          icon: 'ShieldCheck', 
          color: '#22c55e',
          path: '/'
        },
        { 
          id: 'chef', 
          name: 'Kitchen Chef', 
          desc: 'Monitor real-time orders and prep status.', 
          icon: 'ChefHat', 
          color: '#facc15',
          path: '/kitchen'
        },
        { 
          id: 'waiter', 
          name: 'Dining Staff', 
          desc: 'Manage floor plan and serve customers.', 
          icon: 'UserCircle2', 
          color: '#3b82f6',
          path: '/floor-plan'
        }
      ]
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.post('/api/login', {
          email: this.form.email,
          password: this.form.password,
          role: this.selectedRole.id
        });

        const { user, token } = response.data;
        
        localStorage.setItem('user_token', token);
        localStorage.setItem('user_role', user.role);
        localStorage.setItem('user_name', user.name);
        
        // Set axios default header
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        this.$router.push(this.selectedRole.path);
      } catch (err) {
        this.error = err.response?.data?.message || 'Verification failed. Check your credentials.';
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.login-view {
  min-height: 100vh;
  background: #0f172a;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  font-family: 'Outfit', sans-serif;
  color: #fff;
}

.login-container {
  width: 100%;
  max-width: 480px;
}

.login-header {
  text-align: center;
  margin-bottom: 3rem;
}

.logo {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.login-header h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -1.5px;
  margin-bottom: 0.5rem;
}

.login-header p {
  color: #94a3b8;
  font-weight: 500;
}

.role-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.role-card {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 20px;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-align: left;
  width: 100%;
  color: #fff;
}

.role-card:hover {
  transform: translateX(10px);
  border-color: #22c55e;
  background: #243145;
}

.role-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.role-info { flex: 1; }
.role-info h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 2px; }
.role-info p { font-size: 0.8rem; color: #94a3b8; font-weight: 500; }
.arrow { color: #475569; transition: transform 0.3s; }
.role-card:hover .arrow { transform: translateX(5px); color: #22c55e; }

/* Login Form Card */
.login-form-card {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 24px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.back-link {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 0.85rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  margin-bottom: 2rem;
}
.back-link:hover { color: #fff; }

.modern-form { display: flex; flex-direction: column; gap: 1.5rem; }

.input-group { display: flex; flex-direction: column; gap: 8px; }
.input-group label { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

.input-wrapper {
  background: #0f172a;
  border: 1px solid #334155;
  border-radius: 12px;
  display: flex;
  align-items: center;
  padding: 0 16px;
  gap: 12px;
  transition: 0.3s;
}
.input-wrapper:focus-within { border-color: #22c55e; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); }

.input-wrapper input {
  background: transparent;
  border: none;
  outline: none;
  padding: 14px 0;
  color: #fff;
  font-weight: 600;
  font-size: 0.95rem;
  width: 100%;
}

.toggle-pass { background: transparent; border: none; color: #475569; cursor: pointer; }
.toggle-pass:hover { color: #94a3b8; }

.login-submit {
  background: #22c55e;
  color: #000;
  border: none;
  height: 52px;
  border-radius: 12px;
  font-weight: 800;
  font-size: 1rem;
  cursor: pointer;
  transition: 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.login-submit:hover:not(:disabled) { background: #16a34a; transform: translateY(-2px); }
.login-submit:disabled { opacity: 0.6; cursor: not-allowed; }

.error-msg {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  padding: 12px;
  border-radius: 10px;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 10px;
}

.hint { text-align: center; font-size: 0.75rem; color: #64748b; font-weight: 500; }
.hint strong { color: #94a3b8; }

.spinner {
  width: 20px; height: 20px;
  border: 3px solid rgba(0,0,0,0.1);
  border-top-color: #000;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.login-footer { margin-top: 3rem; text-align: center; color: #475569; font-size: 0.8rem; font-weight: 600; }

.fade-in { animation: fadeIn 0.8s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
