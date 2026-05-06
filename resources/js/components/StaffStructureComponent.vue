<template>
  <div class="container">
    <h3 style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
      <b>Список сотрудников</b>
    </h3>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
      <button v-if="isAdmin" class="btn btn-secondary" @click="showModel('create')" type="button">
        Добавить сотрудника
      </button>
    </div>

    <div class="row g-2 align-items-end mb-3">
      <div class="col-md-4">
        <label class="form-label mb-1">Фильтр по имени</label>
        <input
          type="text"
          class="form-control"
          placeholder="Имя"
          v-model.trim="filters.name"
        />
      </div>
      <div class="col-md-3">
        <label class="form-label mb-1">Фильтр по роли</label>
        <select class="form-select" v-model="filters.role">
          <option value="">Все роли</option>
          <option value="manager">Менеджер</option>
          <option value="mechanic">Механик</option>
          <option value="admin">Админ</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label mb-1">Фильтр по статусу</label>
        <select class="form-select" v-model="filters.status">
          <option value="">Все статусы</option>
          <option value="online">Онлайн</option>
          <option value="offline">Оффлайн</option>
          <option value="vacation">В отпуске</option>
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button class="btn btn-outline-secondary" type="button" @click="resetFilters">
          Сброс
        </button>
      </div>
    </div>

    <table class="table table-hover text-center align-middle">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Имя</th>
          <th scope="col">Роль</th>
          <th scope="col">Телефон / контакт</th>
          <th scope="col">Статус</th>
          <th scope="col">Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading">
          <td colspan="6" class="text-center py-4">Загрузка...</td>
        </tr>
        <tr v-else-if="filteredMembers.length === 0">
          <td colspan="6" class="text-center py-4 text-muted">Сотрудники не найдены</td>
        </tr>
        <tr v-for="(member, index) in filteredMembers" :key="member.id">
          <th scope="row">{{ index + 1 }}</th>
          <td class="fw-semibold">{{ member.name }}</td>
          <td>{{ getRoleLabel(member.role) }}</td>
          <td>{{ member.contact }}</td>
          <td>
            <span class="badge" :class="getStatusClass(member.status)">
              {{ getStatusLabel(member.status) }}
            </span>
          </td>
          <td>
            <div class="btn-group" role="group">
              <button v-if="isAdmin" type="button" class="btn btn-warning btn-sm me-2" @click="showModel('edit', member)">
                Редактировать
              </button>
            </div>
            <div class="btn-group" role="group">
              <button v-if="isAdmin" type="button" class="btn btn-danger btn-sm" @click="confirmDelete(member)">
                Удалить
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Delete confirmation modal -->
    <div v-if="showDeleteModal" class="modal-backdrop d-flex align-items-center justify-content-center" style="position:fixed;inset:0;z-index:2000;">
      <div class="card p-3" style="width:420px;">
        <h5>Подтвердите удаление</h5>
        <p>Удалить сотрудника <strong>{{ deleteTarget?.name }}</strong> — это действие необратимо.</p>
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-outline-secondary" @click="cancelDelete">Отмена</button>
          <button class="btn btn-danger" @click="confirmDeleteNow">Удалить</button>
        </div>
      </div>
    </div>

    <!-- Create/Edit modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-backdrop d-flex align-items-center justify-content-center" style="position:fixed;inset:0;z-index:2000;">
      <div class="card p-4" style="width:520px; max-width:95vw; max-height:90vh; overflow-y:auto;">
        <h5 class="mb-3">{{ showCreateModal ? 'Добавить сотрудника' : 'Редактировать сотрудника' }}</h5>
        
        <div class="mb-3">
          <label class="form-label">Имя</label>
          <input v-model="form.name" class="form-control" placeholder="Имя Фамилия" />
          <div class="form-text">Укажите полное имя сотрудника.</div>
          <div v-if="errors.name" class="alert alert-danger mt-2">
            <span v-for="msg in errors.name" :key="msg">{{ msg }}</span>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Роль</label>
          <select v-model="form.role" class="form-select">
            <option value="manager">Менеджер</option>
            <option value="mechanic">Механик</option>
            <option value="admin">Админ</option>
          </select>
          <div class="form-text">Выберите роль сотрудника.</div>
          <div v-if="errors.role" class="alert alert-danger mt-2">
            <span v-for="msg in errors.role" :key="msg">{{ msg }}</span>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Телефон / Контакт</label>
          <input v-model="form.contact" class="form-control" placeholder="+7 (701) 234-56-78" />
          <div class="form-text">Формат: +7 (999) 999-99-99 или +79999999999.</div>
          <div v-if="errors.contact" class="alert alert-danger mt-2">
            <span v-for="msg in errors.contact" :key="msg">{{ msg }}</span>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Статус</label>
          <select v-model="form.status" class="form-select">
            <option value="online">Онлайн</option>
            <option value="offline">Оффлайн</option>
            <option value="vacation">В отпуске</option>
          </select>
          <div class="form-text">Выберите текущий статус.</div>
          <div v-if="errors.status" class="alert alert-danger mt-2">
            <span v-for="msg in errors.status" :key="msg">{{ msg }}</span>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-outline-secondary" @click="closeModal">Отмена</button>
          <button class="btn btn-primary" :disabled="saving" @click="save">
            {{ showCreateModal ? 'Создать' : 'Сохранить' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    section: {
      type: String,
      default: 'employees',
    },
  },
  data() {
    return {
      user: null,
      members: [],
      loading: false,
      saving: false,
      showCreateModal: false,
      showEditModal: false,
      showDeleteModal: false,
      deleteTarget: null,
      editingId: null,
      errors: {},
      filters: {
        name: '',
        role: '',
        status: '',
      },
      form: {
        name: '',
        role: 'manager',
        contact: '',
        status: 'offline',
      },
    };
  },
  computed: {
    isAdmin() {
      return this.user && this.user.role === 'super_admin';
    },
    filteredMembers() {
      return this.members.filter((item) => {
        const byName = this.filters.name
          ? (item.name || '').toLowerCase().includes(this.filters.name.toLowerCase())
          : true;
        const byRole = this.filters.role
          ? item.role === this.filters.role
          : true;
        const byStatus = this.filters.status
          ? item.status === this.filters.status
          : true;

        return byName && byRole && byStatus;
      });
    },
  },
  mounted() {
    const userData = localStorage.getItem('user');
    if (!userData) {
      window.location.href = '/login';
      return;
    }

    this.user = JSON.parse(userData);
    this.loadMembers();
  },
  methods: {
    resetFilters() {
      this.filters = {
        name: '',
        role: '',
        status: '',
      };
    },
    async loadMembers() {
      this.loading = true;
      try {
        const res = await axios.get('/api/staff-members');
        this.members = res.data.data || [];
      } catch (error) {
        console.error(error);
        this.members = [];
      } finally {
        this.loading = false;
      }
    },
    showModel(action, member = null) {
      this.errors = {};
      if (action === 'create') {
        this.form = { name: '', role: 'manager', contact: '', status: 'offline' };
        this.showCreateModal = true;
        this.showEditModal = false;
        this.editingId = null;
      } else if (action === 'edit' && member) {
        this.form = { name: member.name, role: member.role, contact: member.contact, status: member.status };
        this.showEditModal = true;
        this.showCreateModal = false;
        this.editingId = member.id;
      }
    },
    closeModal() {
      this.showCreateModal = false;
      this.showEditModal = false;
      this.errors = {};
      this.form = { name: '', role: 'manager', contact: '', status: 'offline' };
      this.editingId = null;
    },
    confirmDelete(member) {
      this.deleteTarget = member;
      this.showDeleteModal = true;
    },
    cancelDelete() {
      this.showDeleteModal = false;
      this.deleteTarget = null;
    },
    async confirmDeleteNow() {
      if (!this.deleteTarget) return;
      try {
        await axios.delete(`/api/staff-members/${this.deleteTarget.id}`);
        await this.loadMembers();
        this.cancelDelete();
      } catch (error) {
        console.error(error);
        alert('Не удалось удалить сотрудника');
      }
    },
    async save() {
      this.saving = true;
      this.errors = {};

      try {
        if (this.showCreateModal) {
          await axios.post('/api/staff-members', this.form);
        } else if (this.showEditModal && this.editingId) {
          await axios.patch(`/api/staff-members/${this.editingId}`, this.form);
        }
        await this.loadMembers();
        this.closeModal();
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          console.error(error);
          alert('Ошибка при сохранении');
        }
      } finally {
        this.saving = false;
      }
    },
    getRoleLabel(role) {
      const labels = {
        manager: 'Менеджер',
        mechanic: 'Механик',
        admin: 'Админ',
      };
      return labels[role] || role;
    },
    getStatusLabel(status) {
      const labels = {
        online: 'Онлайн',
        offline: 'Оффлайн',
        vacation: 'В отпуске',
      };
      return labels[status] || status;
    },
    getStatusClass(status) {
      const classes = {
        online: 'bg-success',
        offline: 'bg-secondary',
        vacation: 'bg-warning text-dark',
      };
      return classes[status] || 'bg-secondary';
    },
    formatDate(date) {
      if (!date) return '—';
      const d = new Date(date);
      const pad = (n) => (n < 10 ? '0' + n : n);
      return `${pad(d.getDate())}.${pad(d.getMonth() + 1)}.${d.getFullYear()}, ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    },
  },
};
</script>
