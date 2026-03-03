<template>
  <div class="staff-manager fade-in">
    <div class="page-header">
      <div class="header-left">
        <h2 class="page-title">Team <span class="highlight">Management</span></h2>
        <p class="page-subtitle">Configure hospitality staff and adjust permissions</p>
      </div>
      <div class="header-actions">
        <button @click="openAddModal" class="btn btn-primary">
          <UserPlus :size="18" />
          Onboard Staff
        </button>
      </div>
    </div>

    <!-- Staff Cards Grid -->
    <div v-if="loading" class="loading-wrap">
      <div class="modern-spinner"></div>
      <p>Synchronizing team roster...</p>
    </div>

    <div v-else class="staff-grid">
      <div v-for="member in staff" :key="member.id" class="staff-card white-card">
        <div class="card-top">
          <div class="status-indicator" :class="{ active: true }"> Online </div>
          <button @click="editStaff(member)" class="edit-btn"><Settings :size="14" /></button>
        </div>
        
        <div class="card-body">
          <div class="avatar-ring">
            <img :src="getAvatarUrl(member)" :alt="member.name" class="staff-avatar">
          </div>
          <h3 class="staff-name">{{ member.name }}</h3>
          <p class="staff-email">{{ member.email }}</p>
          
          <div class="role-badge" :class="member.role">
            <ShieldCheck v-if="member.role === 'admin'" :size="12" />
            <ChefHat v-else-if="member.role === 'chef'" :size="12" />
            <UserCircle2 v-else :size="12" />
            {{ member.role.toUpperCase() }}
          </div>
        </div>

        <div class="card-stats">
          <div class="stat-item">
            <span class="val">24</span>
            <span class="lab">Shifts</span>
          </div>
          <div class="stat-item">
            <span class="val">4.8</span>
            <span class="lab">Rating</span>
          </div>
        </div>

        <div class="card-footer">
          <button @click="removeStaff(member.id)" class="btn-delete" :disabled="member.id === currentUserId">
            <Trash2 :size="14" /> REMOVE FROM ORG
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ editingStaffMember ? 'Update Profile' : 'Staff Onboarding' }}</h3>
          <p>Provide credentials and profile picture for the team member</p>
        </div>

        <form @submit.prevent="saveStaff" class="modern-form">
          <!-- Profile Picture Upload -->
          <div class="avatar-upload-section">
            <div class="avatar-preview-container" @click="$refs.fileInput.click()">
              <img v-if="imagePreview" :src="imagePreview" class="preview-img">
              <div v-else class="preview-placeholder">
                <Camera :size="24" />
                <span>Upload Photo</span>
              </div>
              <div class="upload-hint">Click to change</div>
            </div>
            <input type="file" ref="fileInput" @change="handleFileChange" accept="image/*" class="hidden-input">
          </div>

          <div class="form-grid">
            <div class="input-group full">
              <label>Full Name</label>
              <div class="input-wrap">
                <User :size="18" />
                <input v-model="form.name" type="text" placeholder="e.g. Serey Roth" required>
              </div>
            </div>

            <div class="input-group full">
              <label>Email Address</label>
              <div class="input-wrap">
                <Mail :size="18" />
                <input v-model="form.email" type="email" placeholder="staff@mlupdong.com" required>
              </div>
            </div>

            <div class="input-group">
              <label>Assigned Role</label>
              <div class="input-wrap">
                <Shield :size="18" />
                <select v-model="form.role" required>
                  <option value="admin">Administrator</option>
                  <option value="chef">Kitchen Chef</option>
                  <option value="waiter">Dining Staff (Waiter)</option>
                </select>
              </div>
            </div>

            <div class="input-group">
              <label>Security Password</label>
              <div class="input-wrap">
                <Lock :size="18" />
                <input v-model="form.password" type="password" :placeholder="editingStaffMember ? 'Leave blank to keep same' : 'Min 6 chars'" :required="!editingStaffMember">
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-secondary">Discard</button>
            <button type="submit" class="btn-primary" :disabled="submitting">
              {{ submitting ? 'Processing...' : (editingStaffMember ? 'Update Access' : 'Authorize Access') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { 
  UserPlus, Settings, Trash2, ShieldCheck, 
  ChefHat, UserCircle2, Mail, User, Lock, Shield, 
  AlertCircle, Camera 
} from 'lucide-vue-next';
import axios from 'axios';

export default {
  name: 'StaffManager',
  components: { 
    UserPlus, Settings, Trash2, ShieldCheck, 
    ChefHat, UserCircle2, Mail, User, Lock, Shield, 
    AlertCircle, Camera 
  },
  data() {
    return {
      staff: [],
      loading: true,
      submitting: false,
      showModal: false,
      editingStaffMember: null,
      currentUserId: null,
      imagePreview: null,
      selectedFile: null,
      form: {
        name: '',
        email: '',
        role: 'waiter',
        password: ''
      }
    };
  },
  async created() {
    await this.fetchStaff();
  },
  methods: {
    async fetchStaff() {
      this.loading = true;
      try {
        const res = await axios.get('/api/staff');
        this.staff = res.data;
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    getAvatarUrl(member) {
      if (member.avatar_path) {
        return `http://127.0.0.1:8000/storage/${member.avatar_path}`;
      }
      const color = member.role === 'admin' ? '22c55e' : (member.role === 'chef' ? 'facc15' : '3b82f6');
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(member.name)}&background=${color}&color=fff&bold=true`;
    },
    handleFileChange(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.selectedFile = file;
      this.imagePreview = URL.createObjectURL(file);
    },
    openAddModal() {
      this.editingStaffMember = null;
      this.imagePreview = null;
      this.selectedFile = null;
      this.form = { name: '', email: '', role: 'waiter', password: '' };
      this.showModal = true;
    },
    editStaff(member) {
      this.editingStaffMember = member;
      this.imagePreview = this.getAvatarUrl(member);
      this.selectedFile = null;
      this.form = { name: member.name, email: member.email, role: member.role, password: '' };
      this.showModal = true;
    },
    async saveStaff() {
      this.submitting = true;
      try {
        const formData = new FormData();
        formData.append('name', this.form.name);
        formData.append('email', this.form.email);
        formData.append('role', this.form.role);
        if (this.form.password) formData.append('password', this.form.password);
        if (this.selectedFile) formData.append('avatar', this.selectedFile);

        if (this.editingStaffMember) {
          // Laravel PUT with files needs a workaround: POST with _method=PUT
          formData.append('_method', 'PUT');
          await axios.post(`/api/staff/${this.editingStaffMember.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        } else {
          await axios.post('/api/staff', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        }
        await this.fetchStaff();
        this.closeModal();
      } catch (err) {
        alert(err.response?.data?.message || 'Transaction failed.');
      } finally {
        this.submitting = false;
      }
    },
    async removeStaff(id) {
      if (!confirm('Permanently revoke access for this staff member?')) return;
      try {
        await axios.delete(`/api/staff/${id}`);
        this.staff = this.staff.filter(s => s.id !== id);
      } catch (err) {
        alert('Operation failed. Likely security restriction.');
      }
    },
    closeModal() {
      this.showModal = false;
      this.editingStaffMember = null;
      this.imagePreview = null;
      this.selectedFile = null;
    }
  }
};
</script>

<style scoped>
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; }
.page-title { font-size: 2.25rem; font-weight: 800; color: #1e293b; }
.highlight { color: #22c55e; }
.page-subtitle { color: #64748b; font-weight: 500; font-size: 1rem; }

.staff-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.white-card {
  background: white;
  border-radius: 24px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  padding: 1.5rem;
  transition: 0.3s;
}

.staff-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }

.card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.status-indicator {
  font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
  padding: 4px 10px; border-radius: 20px; background: #ecfdf5; color: #10b981;
}
.edit-btn { background: #f8fafc; border: none; color: #94a3b8; padding: 6px; border-radius: 8px; cursor: pointer; }

.card-body { text-align: center; margin-bottom: 2rem; }

.avatar-ring {
  width: 90px; height: 90px; border-radius: 26px;
  background: #f1f5f9; padding: 4px; display: inline-block; margin-bottom: 1rem;
  box-shadow: 0 8px 16px rgba(0,0,0,0.05);
}

.staff-avatar { width: 100%; height: 100%; border-radius: 22px; object-fit: cover; }
.staff-name { font-size: 1.15rem; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
.staff-email { font-size: 0.85rem; color: #64748b; font-weight: 500; margin-bottom: 1.25rem; }

.role-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 12px; font-size: 0.75rem; font-weight: 800;
}
.role-badge.admin { background: #eff6ff; color: #3b82f6; }
.role-badge.chef { background: #fff7ed; color: #f97316; }
.role-badge.waiter { background: #f5f3ff; color: #8b5cf6; }

.card-stats {
  display: flex; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;
  padding: 1.25rem 0; margin-bottom: 1.25rem;
}
.stat-item { flex: 1; text-align: center; display: flex; flex-direction: column; }
.stat-item .val { font-size: 1rem; font-weight: 800; color: #1e293b; }
.stat-item .lab { font-size: 0.65rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; }

.card-footer { text-align: center; }
.btn-delete {
  background: transparent; border: none; color: #ef4444; font-size: 0.7rem;
  font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%;
}
.btn-delete:disabled { opacity: 0.3; cursor: not-allowed; }

/* Modal Styles */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center; z-index: 2000;
}

.modal-content {
  background: white; width: 500px; padding: 2.5rem; border-radius: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
  max-height: 90vh; overflow-y: auto;
}

.modal-header { margin-bottom: 2rem; }
.modal-header h3 { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
.modal-header p { color: #64748b; font-size: 0.9rem; margin-top: 4px; }

/* Avatar Upload */
.avatar-upload-section { display: flex; justify-content: center; margin-bottom: 2rem; }
.avatar-preview-container {
  width: 100px; height: 100px; border-radius: 28px; background: #f8fafc;
  border: 2px dashed #e2e8f0; position: relative; cursor: pointer;
  overflow: hidden; display: flex; flex-direction: column; align-items: center; justify-content: center;
  transition: 0.3s;
}
.avatar-preview-container:hover { border-color: #22c55e; background: #f0fdf4; }
.preview-img { width: 100%; height: 100%; object-fit: cover; }
.preview-placeholder { display: flex; flex-direction: column; align-items: center; gap: 4px; color: #94a3b8; }
.preview-placeholder span { font-size: 0.65rem; font-weight: 700; }
.upload-hint {
  position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.5);
  color: white; font-size: 0.6rem; font-weight: 700; padding: 4px 0; text-align: center;
  opacity: 0; transition: 0.3s;
}
.avatar-preview-container:hover .upload-hint { opacity: 1; }
.hidden-input { display: none; }

.modern-form { display: flex; flex-direction: column; gap: 1.5rem; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.full { grid-column: span 2; }

.input-group { display: flex; flex-direction: column; gap: 8px; }
.input-group label { font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

.input-wrap {
  display: flex; align-items: center; gap: 12px; background: #f8fafc;
  border: 1px solid #e2e8f0; border-radius: 12px; padding: 0 16px;
}
.input-wrap:focus-within { border-color: #22c55e; background: white; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); }

.input-wrap input, .input-wrap select {
  border: none; background: transparent; padding: 12px 0; width: 100%; outline: none;
  font-family: inherit; font-size: 0.95rem; font-weight: 600; color: #1e293b;
}

.form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem; }
.btn-primary { background: #22c55e; color: #000; padding: 12px 24px; border-radius: 12px; border: none; font-weight: 800; cursor: pointer; }
.btn-secondary { background: #f1f5f9; color: #64748b; padding: 12px 24px; border-radius: 12px; border: none; font-weight: 800; cursor: pointer; }

.loading-wrap { text-align: center; padding: 5rem; color: #64748b; }
.modern-spinner { width: 40px; height: 40px; border: 3px solid #f1f5f9; border-top-color: #22c55e; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1rem; }
@keyframes spin { to { transform: rotate(360deg); } }

.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
