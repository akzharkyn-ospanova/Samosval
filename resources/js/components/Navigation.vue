<template>
  <div class="app-wrapper">
    <nav class="navbar navbar-expand-lg navbar-modern">
      <div class="container-fluid">
        <router-link to="/" class="navbar-brand">
          <i class="fas fa-toolbox"></i> SamosVal Pro
        </router-link>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            
            <!-- Техника Dropdown -->
            <li v-if="user && canAccessTech(user.role)" class="nav-item dropdown">
              <button type="button" class="nav-link dropdown-toggle" @click="toggleMenu('Samosvals')" aria-expanded="false">
                <i class="fas fa-cogs"></i> Техника
              </button>
              <ul v-if="openSamosvals" class="dropdown-menu dropdown-menu-modern show">
                <li>
                  <router-link to="/Samosvals" class="dropdown-item">
                    <i class="fas fa-list"></i> Список техники
                  </router-link>
                </li>
                <li>
                  <router-link to="/Samosvals/problems" class="dropdown-item">
                    <i class="fas fa-exclamation-triangle"></i> Справочник поломок
                  </router-link>
                </li>
                <li>
                  <router-link to="/Samosvals/solutions" class="dropdown-item">
                    <i class="fas fa-lightbulb"></i> Справочник решений
                  </router-link>
                </li>
                <li>
                  <router-link to="/Samosvals/requests" class="dropdown-item">
                    <i class="fas fa-check-circle"></i> Справочник заявок
                  </router-link>
                </li>
              </ul>
            </li>

            <!-- Список заявок -->
            <li class="nav-item">
              <router-link to="/inbox" class="nav-link">
                <i class="fas fa-inbox"></i> Заявки
              </router-link>
            </li>

            <!-- Сотрудники -->
            <li class="nav-item">
              <router-link to="/staff/employees" class="nav-link">
                <i class="fas fa-users"></i> Сотрудники
              </router-link>
            </li>
          </ul>

          <!-- User Section -->
          <div class="user-section ms-lg-4 mt-3 mt-lg-0">
            <div v-if="user" class="user-info">
              <span class="user-name">
                <i class="fas fa-user-circle"></i> {{ user.name }}
              </span>
              <span class="user-role">{{ getRoleLabel(user.role) }}</span>
              <button class="btn btn-outline-danger btn-sm ms-2" @click="logout">
                <i class="fas fa-sign-out-alt"></i> Выход
              </button>
            </div>
            <div v-else>
              <router-link to="/login" class="btn btn-primary btn-sm">
                <i class="fas fa-sign-in-alt"></i> Вход
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script>
export default {
  data() {
    return {
      openSamosvals: false,
      user: null,
      clickOutsideHandler: null,
    };
  },
  mounted() {
    const userData = localStorage.getItem('user');
    if (userData) {
      this.user = JSON.parse(userData);
    }
    // Закрываем меню при клике вне navbar
    this.clickOutsideHandler = (e) => {
      const navbar = document.querySelector('.navbar-modern');
      if (navbar && !navbar.contains(e.target)) {
        this.openSamosvals = false;
      }
    };
    document.addEventListener('click', this.clickOutsideHandler);
  },
  beforeUnmount() {
    if (this.clickOutsideHandler) {
      document.removeEventListener('click', this.clickOutsideHandler);
    }
  },
  methods: {
    canAccessTech(role) {
      return role === 'super_admin' || role === 'admin';
    },
    toggleMenu(which) {
      if (which === 'Samosvals') {
        this.openSamosvals = !this.openSamosvals;
      }
    },
    closeAll() {
      this.openSamosvals = false;
    },
    logout() {
      if (confirm('Вы уверены, что хотите выйти?')) {
        localStorage.removeItem('user');
        this.user = null;
        window.location.href = '/login';
      }
    },
    getRoleLabel(role) {
      const labels = {
        admin: 'Админ',
        super_admin: 'Суперадмин',
        sales_manager: 'Менеджер по продажам',
      };
      return labels[role] || role;
    },
  },
};
</script>

<style scoped>
.app-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.navbar-modern {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border-bottom: 2px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  padding: 0.8rem 0;
}

.navbar-brand {
  font-weight: 800;
  font-size: 1.3rem;
  color: #0066cc !important;
  margin-right: 2rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.nav-link {
  font-weight: 600;
  color: #475569 !important;
  transition: all 0.3s ease;
  margin: 0 0.5rem;
  padding: 0.5rem 0.8rem !important;
  border-radius: 0.4rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.nav-link:hover {
  color: #0066cc !important;
  background-color: rgba(0, 102, 204, 0.05);
  transform: translateY(-2px);
}

.nav-link.router-link-active {
  background-color: rgba(0, 102, 204, 0.15);
  color: #0066cc !important;
}

.dropdown-toggle::after {
  margin-left: 0.5rem;
}

.dropdown-menu-modern {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 0.5rem;
  margin-top: 0.5rem;
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 250px;
}

.dropdown-item {
  padding: 0.6rem 1rem;
  border-radius: 0.4rem;
  transition: all 0.2s ease;
  color: #475569 !important;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dropdown-item:hover {
  background-color: #f1f5f9;
  color: #0066cc !important;
  transform: translateX(2px);
}

.user-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  border-left: 2px solid #e2e8f0;
  padding-left: 1rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-size: 0.9rem;
}

.user-name {
  font-weight: 600;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.user-role {
  color: #94a3b8;
  font-size: 0.85rem;
}

.main-content {
  flex: 1;
  padding: 1.5rem 0;
}

@media (max-width: 991px) {
  .user-section {
    border-left: none;
    padding-left: 0;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #e2e8f0;
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
