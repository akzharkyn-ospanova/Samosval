<template>
  <div class="dashboard-container">
    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-content">
        <div class="hero-icon">🏢</div>
        <h1>Добро пожаловать в SamosVal Pro</h1>
        <p class="hero-subtitle">Комплексная система управления техникой и заявками</p>
        <div v-if="user" class="user-greeting">
          <span class="greeting-text">Здравствуйте, <strong>{{ user.name }}</strong>!</span>
          <span class="user-role-badge">{{ getRoleLabel(user.role) }}</span>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-section">
      <h2>📊 Обзор системы</h2>
      <div class="stats-grid">
        <div class="stat-box success">
          <div class="stat-icon">📋</div>
          <div class="stat-info">
            <div class="stat-label">Активные заявки</div>
            <div class="stat-value">{{ stats.activeRequests }}</div>
          </div>
        </div>
        <div class="stat-box info">
          <div class="stat-icon">🛠️</div>
          <div class="stat-info">
            <div class="stat-label">Единиц техники</div>
            <div class="stat-value">{{ stats.totalSamosvals }}</div>
          </div>
        </div>
        <div class="stat-box warning">
          <div class="stat-icon">⚠️</div>
          <div class="stat-info">
            <div class="stat-label">В ремонте</div>
            <div class="stat-value">{{ stats.inRepair }}</div>
          </div>
        </div>
        <div class="stat-box primary">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <div class="stat-label">Сотрудников</div>
            <div class="stat-value">{{ stats.totalStaff }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div class="features-section">
      <h2>✨ Основные возможности</h2>
      <div class="features-grid">
        <div v-if="user && user.role === 'super_admin'" class="feature-card">
          <div class="feature-icon">🛠️</div>
          <h3>Управление техникой</h3>
          <p>Полный контроль над всей техникой в системе: добавление, редактирование, отслеживание статуса</p>
          <router-link to="/Samosvals" class="feature-link">
            <i class="fas fa-arrow-right"></i> Перейти
          </router-link>
        </div>

        <div v-if="user && user.role === 'super_admin'" class="feature-card">
          <div class="feature-icon">📚</div>
          <h3>Справочники</h3>
          <p>Управление справочниками поломок, решений и заявок для быстрого ввода данных</p>
          <div class="feature-submenu">
            <router-link to="/Samosvals/problems" class="submenu-link">
              <i class="fas fa-list"></i> Поломки
            </router-link>
            <router-link to="/Samosvals/solutions" class="submenu-link">
              <i class="fas fa-lightbulb"></i> Решения
            </router-link>
            <router-link to="/Samosvals/requests" class="submenu-link">
              <i class="fas fa-tasks"></i> Заявки
            </router-link>
          </div>
        </div>

        <div class="feature-card">
          <div class="feature-icon">📬</div>
          <h3>Управление заявками</h3>
          <p>Отслеживание входящих заявок, распределение менеджерам, комментирование и история</p>
          <router-link to="/inbox" class="feature-link">
            <i class="fas fa-arrow-right"></i> К заявкам
          </router-link>
        </div>

        <div class="feature-card">
          <div class="feature-icon">👤</div>
          <h3>Структура компании</h3>
          <p>Управление сотрудниками, должностями, подразделениями и контактной информацией</p>
          <router-link to="/staff/employees" class="feature-link">
            <i class="fas fa-arrow-right"></i> К сотрудникам
          </router-link>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-section">
      <h2>📌 Последняя активность</h2>
      <div class="activity-list">
        <div v-if="recentActivities.length === 0" class="empty-state">
          <i class="fas fa-inbox"></i>
          <p>Нет недавней активности</p>
        </div>
        <div v-for="activity in recentActivities" :key="activity.id" class="activity-item">
          <div class="activity-icon" :class="activity.type">
            {{ getActivityIcon(activity.type) }}
          </div>
          <div class="activity-content">
            <div class="activity-title">{{ activity.title }}</div>
            <div class="activity-time">{{ activity.time }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="actions-section">
      <h2>⚡ Быстрые действия</h2>
      <div class="actions-grid">
        <button v-if="user && (user.role === 'sales_manager' || user.role === 'super_admin')" 
          class="action-btn" @click="navigateTo('/inbox')">
          <i class="fas fa-plus-circle"></i>
          <span>Создать заявку</span>
        </button>
        <button v-if="user && user.role === 'super_admin'" 
          class="action-btn" @click="navigateTo('/Samosvals')">
          <i class="fas fa-plus"></i>
          <span>Добавить технику</span>
        </button>
        <button class="action-btn" @click="navigateTo('/inbox')">
          <i class="fas fa-list-check"></i>
          <span>Открыть заявки</span>
        </button>
        <button v-if="user && user.role === 'super_admin'" 
          class="action-btn" @click="navigateTo('/staff/employees')">
          <i class="fas fa-users"></i>
          <span>Управление командой</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'DashboardComponent',
  data() {
    return {
      user: null,
      stats: {
        activeRequests: 0,
        totalSamosvals: 0,
        inRepair: 0,
        totalStaff: 0,
      },
      recentActivities: [
        {
          id: 1,
          type: 'request',
          title: 'Новая заявка от клиента ООО "Логистика"',
          time: 'сегодня в 10:30',
        },
        {
          id: 2,
          type: 'samosval',
          title: 'Техника RCL00042 переведена в статус "В ремонте"',
          time: 'сегодня в 09:15',
        },
        {
          id: 3,
          type: 'staff',
          title: 'Иван Петров назначен менеджером по продажам',
          time: 'вчера в 16:45',
        },
      ],
    };
  },
  mounted() {
    const userData = localStorage.getItem('user');
    if (!userData) {
      this.$router.push('/login');
      return;
    }
    this.user = JSON.parse(userData);
    this.loadStats();
  },
  methods: {
    async loadStats() {
      try {
        // Загружаем статистику (если API доступен)
        const params = new URLSearchParams();
        if (this.user) {
          params.append('user_id', this.user.id);
          params.append('role', this.user.role);
        }

        // Загружаем заявки
        const inboxRes = await axios.get(`/api/inbox?${params.toString()}`);
        this.stats.activeRequests = inboxRes.data.data ? inboxRes.data.data.length : 0;

        // Загружаем технику
        const samosvalRes = await axios.get('/api/samosvals');
        if (samosvalRes.data.data) {
          this.stats.totalSamosvals = samosvalRes.data.data.length;
          this.stats.inRepair = samosvalRes.data.data.filter((s) => s.status === 2).length;
        }

        // Загружаем сотрудников
        const staffRes = await axios.get('/api/staff-members');
        this.stats.totalStaff = staffRes.data.data ? staffRes.data.data.length : 0;
      } catch (e) {
        console.error('Error loading stats:', e);
      }
    },
    getRoleLabel(role) {
      const labels = {
        super_admin: 'Суперадмин',
        sales_manager: 'Менеджер по продажам',
      };
      return labels[role] || role;
    },
    getActivityIcon(type) {
      const icons = {
        request: '📬',
        samosval: '🛠️',
        staff: '👤',
        comment: '💬',
      };
      return icons[type] || '📌';
    },
    navigateTo(path) {
      this.$router.push(path);
    },
  },
};
</script>

<style scoped>
.dashboard-container {
  padding: 0 1.5rem 2rem;
}

/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
  border-radius: 1rem;
  padding: 3rem 2rem;
  color: white;
  margin-bottom: 2rem;
  box-shadow: 0 10px 30px rgba(0, 102, 204, 0.2);
}

.hero-content {
  max-width: 600px;
}

.hero-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: inline-block;
}

.hero-section h1 {
  font-size: 2.5rem;
  margin: 0 0 0.5rem 0;
  color: white;
}

.hero-subtitle {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.9);
  margin: 0 0 1rem 0;
}

.user-greeting {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.greeting-text {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.95);
}

.user-role-badge {
  background: rgba(255, 255, 255, 0.2);
  padding: 0.4rem 0.8rem;
  border-radius: 0.3rem;
  font-size: 0.85rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

/* Stats Section */
.stats-section {
  margin-bottom: 3rem;
}

.stats-section h2 {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-box {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  display: flex;
  gap: 1rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  border-left: 4px solid;
}

.stat-box:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.stat-box.success {
  border-left-color: #10b981;
}

.stat-box.info {
  border-left-color: #3b82f6;
}

.stat-box.warning {
  border-left-color: #f59e0b;
}

.stat-box.primary {
  border-left-color: #0066cc;
}

.stat-icon {
  font-size: 2rem;
  line-height: 1;
}

.stat-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.stat-label {
  color: #94a3b8;
  font-size: 0.85rem;
  font-weight: 500;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #1e293b;
  margin-top: 0.25rem;
}

/* Features Section */
.features-section {
  margin-bottom: 3rem;
}

.features-section h2 {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.feature-card {
  background: white;
  border-radius: 0.75rem;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.feature-card:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
  border-color: #cbd5e1;
}

.feature-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.feature-card h3 {
  margin: 0 0 0.75rem 0;
  font-size: 1.2rem;
  color: #1e293b;
}

.feature-card p {
  margin: 0 0 1rem 0;
  color: #64748b;
  font-size: 0.95rem;
  flex-grow: 1;
}

.feature-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #0066cc;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
}

.feature-link:hover {
  color: #0052a3;
  gap: 0.75rem;
}

.feature-submenu {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 1rem;
}

.submenu-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: #3b82f6;
  font-size: 0.9rem;
  text-decoration: none;
  padding: 0.4rem 0.6rem;
  border-radius: 0.3rem;
  transition: all 0.2s ease;
}

.submenu-link:hover {
  background: #f0f9ff;
  color: #0066cc;
  padding-left: 0.9rem;
}

.feature-highlight {
  background: #f0fdf4;
  color: #166534;
  padding: 0.5rem 1rem;
  border-radius: 0.3rem;
  font-size: 0.9rem;
  font-weight: 600;
  margin-top: auto;
}

/* Activity Section */
.activity-section {
  margin-bottom: 3rem;
}

.activity-section h2 {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.activity-list {
  background: white;
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid #e2e8f0;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.2rem;
  border-bottom: 1px solid #e2e8f0;
  transition: background-color 0.2s ease;
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-item:hover {
  background-color: #f8fafc;
}

.activity-icon {
  font-size: 1.5rem;
  min-width: 40px;
  text-align: center;
}

.activity-content {
  flex-grow: 1;
}

.activity-title {
  color: #1e293b;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.activity-time {
  color: #94a3b8;
  font-size: 0.85rem;
}

.empty-state {
  text-align: center;
  padding: 3rem 1.5rem;
  color: #94a3b8;
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

/* Actions Section */
.actions-section {
  margin-bottom: 3rem;
}

.actions-section h2 {
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.action-btn {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
  color: #1e293b;
}

.action-btn:hover {
  border-color: #0066cc;
  background: #f0f9ff;
  color: #0066cc;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 102, 204, 0.2);
}

.action-btn i {
  font-size: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-container {
    padding: 0 1rem 1.5rem;
  }

  .hero-section {
    padding: 2rem 1.5rem;
  }

  .hero-section h1 {
    font-size: 1.8rem;
  }

  .stats-grid,
  .features-grid,
  .info-grid,
  .actions-grid {
    grid-template-columns: 1fr;
  }

  .stat-box,
  .feature-card,
  .info-card {
    padding: 1.2rem;
  }

  .feature-card {
    padding: 1.5rem;
  }
}
</style>
