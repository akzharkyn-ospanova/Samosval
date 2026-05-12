<template>
  <div class="inbox-container">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1>📬 Список заявок</h1>
        <p class="text-muted">Управление всеми входящими заявками и запросами</p>
      </div>
      <button v-if="user && (user.role === 'sales_manager' || user.role === 'super_admin')" 
        class="btn btn-primary btn-lg" @click="openCreate">
        <i class="fas fa-plus"></i> Создать заявку
      </button>
    </div>

    <!-- Create Form -->
    <div v-if="showCreate" class="card form-card mb-4">
      <div class="card-header">
        <h5 class="mb-0">
          <i class="fas fa-edit"></i>
          {{ editingId ? 'Редактирование заявки' : 'Создание новой заявки' }}
        </h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Имя клиента *</label>
            <input v-model="form.name" type="text" class="form-control" placeholder="Введите имя клиента" />
            <div v-if="errors.name" class="error-message">{{ errors.name[0] }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Номер KZ / почта *</label>
            <input v-model="form.contacts" type="text" class="form-control" placeholder="+7 (701) 123-45-67 или email@example.com" />
            <div v-if="errors.contacts" class="error-message">{{ errors.contacts[0] }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Источник заявки *</label>
            <input v-model="form.source" type="text" class="form-control" placeholder="Сайт, қоңырау, реклама..." />
            <div v-if="errors.source" class="error-message">{{ errors.source[0] }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Комментарий по технике</label>
            <input type="text" class="form-control" placeholder="Например: срочно нужна диагностика техники" />
          </div>
          <div class="col-12">
            <label class="form-label">Комментарий по технике *</label>
            <textarea v-model="form.comment" class="form-control" rows="3" placeholder="Описание техники, неисправности и детали..."></textarea>
            <div v-if="errors.comment" class="error-message">{{ errors.comment[0] }}</div>
          </div>
          <div class="col-12">
            <div class="form-actions">
              <button class="btn btn-primary" @click="editingId ? saveEdit() : createLead()" :disabled="saving">
                <i class="fas fa-save"></i> {{ editingId ? 'Сохранить изменения' : 'Создать заявку' }}
              </button>
              <button class="btn btn-outline-secondary" @click="closeCreate" :disabled="saving">
                <i class="fas fa-times"></i> Отмена
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card filters-card mb-4">
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">🔍 Поиск по имени</label>
            <input type="text" class="form-control" placeholder="Введите имя клиента..."
              v-model.trim="filters.name" />
          </div>
          <div class="col-md-4">
            <label class="form-label">📌 Фильтр по источнику</label>
            <select class="form-select" v-model="filters.source">
              <option value="">Все источники</option>
              <option value="Телефон">☎️ Телефон</option>
              <option value="Email">📧 Email</option>
              <option value="Сайт">🌐 Сайт</option>
              <option value="Рекомендация">👥 Рекомендация</option>
            </select>
          </div>
          <div class="col-md-4 d-grid">
            <button class="btn btn-outline-secondary" @click="resetFilters">
              <i class="fas fa-redo"></i> Сброс фильтров
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Analytics -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="stat-icon">📊</div>
          <div class="stat-content">
            <div class="stat-label">Всего заявок</div>
            <div class="stat-value">{{ analytics.total }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card success">
          <div class="stat-icon">✅</div>
          <div class="stat-content">
            <div class="stat-label">Обработано</div>
            <div class="stat-value">{{ filteredItems.filter(i => i.status === 'done').length }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card warning">
          <div class="stat-icon">⏳</div>
          <div class="stat-content">
            <div class="stat-label">В работе</div>
            <div class="stat-value">{{ filteredItems.filter(i => !i.status || i.status !== 'done').length }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card info">
          <div class="stat-icon">👤</div>
          <div class="stat-content">
            <div class="stat-label">Менеджеров</div>
            <div class="stat-value">{{ managers.length }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive card">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>#ID</th>
            <th>Клиент</th>
            <th>Контакты</th>
            <th>Комментарий</th>
            <th>Источник</th>
            <th>Менеджер</th>
            <th>Дата</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredItems.length === 0">
            <td colspan="8" class="text-center py-4 text-muted">
              <i class="fas fa-inbox"></i> Нет заявок по выбранным фильтрам
            </td>
          </tr>
          <tr v-for="item in filteredItems" :key="item.id" class="item-row">
            <td class="fw-bold text-primary">#{{ item.id }}</td>
            <td class="fw-semibold">{{ item.name }}</td>
            <td>
              <a v-if="item.contacts" :href="`tel:${item.contacts}`" class="text-decoration-none">
                {{ item.contacts }}
              </a>
            </td>
            <td>
              <span class="text-truncate d-block" :title="item.comment">
                {{ item.comment ? item.comment.substring(0, 40) + '...' : '—' }}
              </span>
            </td>
            <td>
              <span class="badge" :class="getSourceBadgeClass(item.source)">
                {{ item.source }}
              </span>
            </td>
            <td>
              <span v-if="item.assigned_to" class="badge bg-info">
                {{ getAssignedUserName(item.assigned_to) }}
              </span>
              <span v-else class="text-muted">—</span>
            </td>
            <td class="small text-muted">{{ formatDate(item.created_at) }}</td>
            <td>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary" @click="showCommentsModal(item)" title="Комментарии">
                  <i class="fas fa-comments"></i>
                </button>
                <button v-if="user && (user.role === 'sales_manager' || user.role === 'super_admin')" 
                  class="btn btn-outline-info" @click="openAssignModal(item)" title="Распределить">
                  <i class="fas fa-user-tie"></i>
                </button>
                <button v-if="user && (user.role === 'sales_manager' || user.role === 'super_admin')" 
                  class="btn btn-outline-warning" @click="startEdit(item)" title="Редактировать">
                  <i class="fas fa-edit"></i>
                </button>
                <button v-if="user && user.role === 'super_admin'" 
                  class="btn btn-outline-danger" @click="confirmDelete(item)" title="Удалить">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modals -->
    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="modal-backdrop">
      <div class="modal-dialog modal-dialog-centered">
        <div class="card">
          <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
              <i class="fas fa-exclamation-triangle"></i> Подтверждение удаления
            </h5>
          </div>
          <div class="card-body">
            <p>Вы уверены, что хотите удалить заявку <strong>#{{ deleteTarget?.id }}</strong> от <strong>{{ deleteTarget?.name }}</strong>?</p>
            <p class="text-danger small mb-0">⚠️ Это действие необратимо</p>
          </div>
          <div class="card-footer">
            <button class="btn btn-outline-secondary" @click="cancelDelete" :disabled="saving">Отмена</button>
            <button class="btn btn-danger" @click="confirmDeleteNow" :disabled="saving">Удалить</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Comments Modal -->
    <div v-if="showComments" class="modal-backdrop">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="fas fa-comments"></i> Комментарии к заявке #{{ selectedItem?.id }}
            </h5>
          </div>
          <div class="card-body">
            <div class="comments-list">
              <div v-if="comments.length === 0" class="text-center text-muted py-3">
                <i class="fas fa-comment-slash"></i> Комментариев нет
              </div>
              <div v-for="c in comments" :key="c.id" class="comment-item">
                <div class="comment-header">
                  <strong>{{ c.user.name }}</strong>
                  <span class="comment-role">{{ c.user.role }}</span>
                  <span class="comment-date">{{ formatDate(c.created_at) }}</span>
                </div>
                <div class="comment-body">{{ c.body }}</div>
              </div>
            </div>
            <div class="mt-3">
              <textarea v-model="newComment" class="form-control" rows="2" placeholder="Добавить комментарий..."></textarea>
            </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-outline-secondary" @click="showComments = false">Закрыть</button>
            <button class="btn btn-primary" @click="addComment" :disabled="saving || !newComment.trim()">
              <i class="fas fa-paper-plane"></i> Отправить
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Assign Modal -->
    <div v-if="showAssignModal" class="modal-backdrop">
      <div class="modal-dialog modal-dialog-centered">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="fas fa-user-check"></i> Распределить заявку менеджеру
            </h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Заявка от:</label>
              <div class="alert alert-info mb-0">
                <strong>{{ selectedItemForAssign?.name }}</strong>
                <br>
                <small>{{ selectedItemForAssign?.contacts }}</small>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Выберите менеджера:</label>
              <select v-model="selectedManager" class="form-select">
                <option :value="null">-- Выберите менеджера --</option>
                <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                  {{ manager.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-outline-secondary" @click="closeAssignModal" :disabled="saving">Отмена</button>
            <button class="btn btn-primary" @click="confirmAssign" :disabled="saving || !selectedManager">
              <i class="fas fa-check"></i> Распределить
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'InboxComponent',
  data() {
    return {
      items: [],
      analytics: { total: 0 },
      showCreate: false,
      form: { name: '', contacts: '', comment: '', source: '' },
      editingId: null,
      saving: false,
      errors: {},
      showDeleteModal: false,
      deleteTarget: null,
      user: null,
      selectedItem: null,
      showComments: false,
      newComment: '',
      comments: [],
      staffMembers: [],
      filters: {
        name: '',
        source: '',
      },
      showAssignModal: false,
      selectedItemForAssign: null,
      selectedManager: null,
    };
  },
  computed: {
    filteredItems() {
      return this.items.filter((item) => {
        const byName = this.filters.name
          ? (item.name || '').toLowerCase().includes(this.filters.name.toLowerCase())
          : true;
        const bySource = this.filters.source
          ? item.source === this.filters.source
          : true;

        return byName && bySource;
      });
    },
    managers() {
      return this.staffMembers.filter((member) => member.role === 'manager');
    },
  },
  mounted() {
    const userData = localStorage.getItem('user');
    if (!userData) {
      window.location.href = '/login';
      return;
    }
    this.user = JSON.parse(userData);
    this.load();
    this.loadStaffMembers();
  },
  methods: {
    resetFilters() {
      this.filters = { name: '', source: '' };
    },
    async load() {
      const params = new URLSearchParams();
      if (this.user) {
        params.append('user_id', this.user.id);
        params.append('role', this.user.role);
      }
      const res = await axios.get(`/api/inbox?${params.toString()}`);
      this.items = res.data.data;
      this.analytics = res.data.analytics;
    },
    openCreate() {
      this.showCreate = true;
      this.form = { name: '', contacts: '', comment: '', source: '' };
    },
    closeCreate() {
      this.showCreate = false;
      this.editingId = null;
      this.errors = {};
    },
    async createLead() {
      if (this.saving) return;
      this.saving = true;
      this.errors = {};
      if (!this.form.name || !this.form.contacts || !this.form.comment || !this.form.source) {
        if (!this.form.name) this.errors.name = ['Заполните имя клиента.'];
        if (!this.form.contacts) this.errors.contacts = ['Укажите контакты клиента.'];
        if (!this.form.comment) this.errors.comment = ['Укажите комментарий к заявке.'];
        if (!this.form.source) this.errors.source = ['Укажите источник заявки.'];
        this.saving = false;
        return;
      }

      try {
        await axios.post('/api/inbox', {...this.form, user_id: this.user.id});
        this.showCreate = false;
        await this.load();
      } catch (e) {
        if (e.response && e.response.status === 422) {
          this.errors = e.response.data.errors || {};
        } else {
          console.error(e);
          alert('Ошибка при создании заявки');
        }
      } finally {
        this.saving = false;
      }
    },
    startEdit(item) {
      this.editingId = item.id;
      this.showCreate = true;
      this.form = { name: item.name || '', contacts: item.contacts || '', comment: item.comment || '', source: item.source || '' };
    },
    async saveEdit() {
      if (!this.editingId) return;
      this.saving = true;
      this.errors = {};
      try {
        await axios.patch(`/api/inbox/${this.editingId}`, {...this.form, user_id: this.user.id});
        this.editingId = null;
        this.showCreate = false;
        await this.load();
      } catch (e) {
        if (e.response && e.response.status === 422) {
          this.errors = e.response.data.errors || {};
        } else {
          console.error(e);
          alert('Ошибка при сохранении заявки');
        }
      } finally {
        this.saving = false;
      }
    },
    confirmDelete(item) {
      this.deleteTarget = item;
      this.showDeleteModal = true;
    },
    async deleteLead(id) {
      try {
        await axios.delete(`/api/inbox/${id}`, { data: { user_id: this.user.id } });
        await this.load();
      } catch (e) {
        console.error(e);
        alert('Ошибка при удалении заявки');
      }
    },
    cancelDelete() {
      this.showDeleteModal = false;
      this.deleteTarget = null;
    },
    async confirmDeleteNow() {
      if (!this.deleteTarget) return;
      await this.deleteLead(this.deleteTarget.id);
      this.showDeleteModal = false;
      this.deleteTarget = null;
    },
    async loadStaffMembers() {
      try {
        const res = await axios.get('/api/staff-members');
        this.staffMembers = res.data.data || [];
      } catch (e) {
        console.error('Error loading staff members:', e);
      }
    },
    async loadComments(leadId) {
      try {
        const res = await axios.get(`/api/leads/${leadId}/comments`);
        this.comments = res.data.data || [];
      } catch (e) {
        console.error('Error loading comments:', e);
      }
    },
    async showCommentsModal(item) {
      this.selectedItem = item;
      this.showComments = true;
      this.newComment = '';
      await this.loadComments(item.id);
    },
    async addComment() {
      if (!this.newComment.trim() || !this.selectedItem) return;
      this.saving = true;
      try {
        const res = await axios.post(`/api/leads/${this.selectedItem.id}/comments`, {
          body: this.newComment,
          user_id: this.user.id,
        });
        this.comments.unshift(res.data.data);
        this.newComment = '';
      } catch (e) {
        console.error('Error adding comment:', e);
        alert('Ошибка при добавлении комментария');
      } finally {
        this.saving = false;
      }
    },
    openAssignModal(item) {
      if (this.managers.length === 0) {
        alert('Нет доступных менеджеров');
        return;
      }
      this.selectedItemForAssign = item;
      this.selectedManager = null;
      this.showAssignModal = true;
    },
    closeAssignModal() {
      this.showAssignModal = false;
      this.selectedItemForAssign = null;
      this.selectedManager = null;
    },
    async confirmAssign() {
      if (!this.selectedManager || !this.selectedItemForAssign) return;
      this.saving = true;
      try {
        await axios.patch(`/api/inbox/${this.selectedItemForAssign.id}`, {
          assigned_to: this.selectedManager,
          user_id: this.user.id,
        });
        await this.load();
        this.closeAssignModal();
      } catch (e) {
        console.error('Error assigning lead:', e);
        alert('Ошибка при распределении заявки');
      } finally {
        this.saving = false;
      }
    },
    getAssignedUserName(userId) {
      const assignedMember = this.staffMembers.find((item) => Number(item.id) === Number(userId));
      return assignedMember ? assignedMember.name : 'Неизвестно';
    },
    getSourceBadgeClass(source) {
      const classes = {
        'Телефон': 'bg-danger',
        'Email': 'bg-info',
        'Сайт': 'bg-primary',
        'Рекомендация': 'bg-success',
      };
      return classes[source] || 'bg-secondary';
    },
    formatDate(dt) {
      if (!dt) return '';
      return new Date(dt).toLocaleString();
    },
  },
};
</script>

<style scoped>
.inbox-container {
  padding: 0 1.5rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  gap: 2rem;
}

.page-header h1 {
  margin: 0;
  font-size: 2.2rem;
}

.page-header p {
  margin: 0.5rem 0 0 0;
}

.form-card {
  border: 2px solid #e2e8f0;
}

.form-card .card-header {
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
}

.filters-card .card-body {
  padding: 1.5rem;
}

.error-message {
  color: #ef4444;
  font-size: 0.85rem;
  margin-top: 0.5rem;
  display: block;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.stat-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 0.75rem;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.stat-card:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  border-color: #cbd5e1;
}

.stat-card.success {
  border-left: 4px solid #10b981;
}

.stat-card.warning {
  border-left: 4px solid #f59e0b;
}

.stat-card.info {
  border-left: 4px solid #3b82f6;
}

.stat-icon {
  font-size: 2rem;
}

.stat-label {
  color: #94a3b8;
  font-size: 0.9rem;
  font-weight: 500;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1e293b;
}

.table-responsive {
  border-radius: 0.75rem !important;
  overflow: hidden;
}

.item-row:hover {
  background-color: #f8fafc;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(2px);
}

.modal-dialog {
  width: 100%;
  max-width: 500px;
  margin: 0;
  animation: slideIn 0.3s ease;
  pointer-events: auto;
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100%;
}

.modal-dialog-lg {
  max-width: 800px;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card-footer {
  padding: 1rem;
  border-top: 1px solid #e2e8f0;
  background: #f8fafc;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.comments-list {
  max-height: 400px;
  overflow-y: auto;
  border-radius: 0.5rem;
  background: #f8fafc;
  padding: 1rem;
}

.comment-item {
  padding: 1rem;
  background: white;
  border-radius: 0.5rem;
  margin-bottom: 0.75rem;
  border-left: 3px solid #3b82f6;
}

.comment-header {
  display: flex;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
  align-items: center;
  font-size: 0.9rem;
}

.comment-role {
  background: #dbeafe;
  color: #0c4a6e;
  padding: 0.25rem 0.6rem;
  border-radius: 0.3rem;
  font-size: 0.8rem;
  font-weight: 600;
}

.comment-date {
  color: #94a3b8;
  font-size: 0.85rem;
  margin-left: auto;
}

.comment-body {
  color: #475569;
  line-height: 1.5;
}

.btn-group-sm {
  gap: 0.25rem;
}

.badge {
  padding: 0.5rem 0.75rem;
  font-size: 0.85rem;
  font-weight: 600;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .table {
    font-size: 0.85rem;
  }

  .modal-dialog {
    max-width: 90%;
  }
}
</style>
