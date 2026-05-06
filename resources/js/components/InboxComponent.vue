<template>
  <div class="container mt-3">
    <div v-if="user" class="d-flex justify-content-between align-items-center">
      <h3>Список заявок</h3>
      <div v-if="user.role === 'sales_manager' || user.role === 'super_admin'">
        <button class="btn btn-success" @click="openCreate">Создать заявку</button>
      </div>

      <!-- Assign manager modal -->
      <div v-if="showAssignModal" class="modal-backdrop d-flex align-items-center justify-content-center" style="position:fixed;inset:0;z-index:2000;">
        <div class="card p-4" style="width:500px;">
          <h5 class="mb-3">Распределить заявку менеджеру</h5>
          <p class="mb-3">Заявка от: <strong>{{ selectedItemForAssign?.name }}</strong></p>
          <div class="mb-3">
            <label class="form-label">Выберите менеджера:</label>
            <select v-model="selectedManager" class="form-select">
              <option :value="null">-- Выберите менеджера --</option>
              <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                {{ manager.name }}
              </option>
            </select>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary" @click="closeAssignModal" :disabled="saving">Отмена</button>
            <button class="btn btn-primary" @click="confirmAssign" :disabled="saving || !selectedManager">Распределить</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCreate" class="card my-3 p-3">
      <div class="row g-2">
        <div class="col-md-3">
          <input v-model="form.name" class="form-control" placeholder="Имя клиента" />
          <div v-if="errors.name" class="text-danger small mt-1">{{ errors.name[0] }}</div>
        </div>
        <div class="col-md-3">
          <input v-model="form.contacts" class="form-control" placeholder="Контакты (тел/почта)" />
          <div v-if="errors.contacts" class="text-danger small mt-1">{{ errors.contacts[0] }}</div>
        </div>
        <div class="col-md-3">
          <input v-model="form.source" class="form-control" placeholder="Источник (сайт, форма, реклама)" />
          <div v-if="errors.source" class="text-danger small mt-1">{{ errors.source[0] }}</div>
        </div>
        <div class="col-md-12 mt-2">
          <textarea v-model="form.comment" class="form-control" rows="2" placeholder="Комментарий"></textarea>
          <div v-if="errors.comment" class="text-danger small mt-1">{{ errors.comment[0] }}</div>
        </div>
        <div class="col-md-12 mt-2 d-flex gap-2">
          <button v-if="!editingId" class="btn btn-primary" @click="createLead" :disabled="saving">Сохранить</button>
          <button v-else class="btn btn-primary" @click="saveEdit" :disabled="saving">Сохранить изменения</button>
          <button class="btn btn-outline-secondary" @click="closeCreate" :disabled="saving">Отмена</button>
        </div>
      </div>
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
      <div class="col-md-4">
        <label class="form-label mb-1">Фильтр по источнику</label>
        <select class="form-select" v-model="filters.source">
          <option value="">Все источники</option>
          <option value="Телефон">Телефон</option>
          <option value="Email">Email</option>
          <option value="Сайт">Сайт</option>
          <option value="Рекомендация">Рекомендация</option>
        </select>
      </div>
      <div class="col-md-4 d-grid">
        <button class="btn btn-outline-secondary" type="button" @click="resetFilters">
          Сброс
        </button>
      </div>
    </div>

    <table class="table table-hover text-center align-middle">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Имя / Контакты</th>
          <th scope="col">Комментарий</th>
          <th scope="col">Источник</th>
          <th scope="col">Дата</th>
          <th scope="col">Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredItems" :key="item.id">
          <td>{{ item.id }}</td>
          <td>
            <strong>{{ item.name }}</strong>
            <div class="small text-muted">{{ item.contacts }}</div>
            <div v-if="item.assigned_to" class="small text-info">
              В зоне ответственности у {{ getAssignedUserName(item.assigned_to) }}
            </div>
          </td>
          <td>{{ item.comment }}</td>
          <td>{{ item.source }}</td>
          <td>{{ formatDate(item.created_at) }}</td>
          <td>
            <button class="btn btn-sm btn-info" @click="showCommentsModal(item)">Комментарии</button>
            <button v-if="user && (user.role === 'sales_manager' || user.role === 'super_admin')" class="btn btn-sm btn-secondary ms-2" @click="openAssignModal(item)">Распределить</button>
            <button v-if="user && (user.role === 'sales_manager' || user.role === 'super_admin')" class="btn btn-sm btn-warning ms-2" @click="startEdit(item)">Редактировать</button>
            <button v-if="user && user.role === 'super_admin'" class="btn btn-sm btn-danger ms-2" @click="confirmDelete(item)">Удалить</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="mt-4 p-3 border">
      <h5>Аналитика</h5>
      <div>Всего заявок: {{ analytics.total }}</div>
    </div>

    <!-- Delete confirmation modal -->
    <div v-if="showDeleteModal" class="modal-backdrop d-flex align-items-center justify-content-center" style="position:fixed;inset:0;z-index:2000;">
      <div class="card p-3" style="width:420px;">
        <h5>Подтвердите удаление</h5>
        <p>Удалить заявку <strong>#{{ deleteTarget ? deleteTarget.id : '' }}</strong> — это действие необратимо.</p>
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-outline-secondary" @click="cancelDelete">Отмена</button>
          <button class="btn btn-danger" @click="confirmDeleteNow">Удалить</button>
        </div>
      </div>
    </div>

    <!-- Comments modal -->
    <div v-if="showComments" class="modal-backdrop d-flex align-items-center justify-content-center" style="position:fixed;inset:0;z-index:2000;">
      <div class="card p-4" style="width:600px; max-height:80vh; overflow-y:auto;">
        <h5>Комментарии к заявке #{{ selectedItem?.id }}</h5>
        <div class="mb-3 p-3" style="background:#f8f9fa; border-radius:6px; max-height:300px; overflow-y:auto;">
          <div v-if="comments.length === 0" class="text-muted">Комментариев нет</div>
          <div v-for="c in comments" :key="c.id" class="mb-2 pb-2 border-bottom">
            <div class="small"><strong>{{ c.user.name }}</strong> ({{ c.user.role }}) — {{ formatDate(c.created_at) }}</div>
            <div>{{ c.body }}</div>
          </div>
        </div>
        <div class="mb-3">
          <textarea v-model="newComment" class="form-control" rows="2" placeholder="Добавить комментарий..." />
        </div>
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-outline-secondary" @click="showComments = false">Закрыть</button>
          <button class="btn btn-primary" @click="addComment" :disabled="saving || !newComment.trim()">Отправить</button>
        </div>
      </div>
    </div>

    <!-- Assign manager modal -->
    <div v-if="showAssignModal" class="modal-backdrop d-flex align-items-center justify-content-center" style="position:fixed;inset:0;z-index:2000;">
      <div class="card p-4" style="width:500px;">
        <h5 class="mb-3">Распределить заявку менеджеру</h5>
        <p class="mb-3">Заявка от: <strong>{{ selectedItemForAssign?.name }}</strong></p>
        <div class="mb-3">
          <label class="form-label">Выберите менеджера:</label>
          <select v-model="selectedManager" class="form-select">
            <option :value="null">-- Выберите менеджера --</option>
            <option v-for="manager in managers" :key="manager.id" :value="manager.id">
              {{ manager.name }}
            </option>
          </select>
        </div>
        <div class="d-flex justify-content-end gap-2">
          <button class="btn btn-outline-secondary" @click="closeAssignModal" :disabled="saving">Отмена</button>
          <button class="btn btn-primary" @click="confirmAssign" :disabled="saving || !selectedManager">Распределить</button>
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
      this.filters = {
        name: '',
        source: '',
      };
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
    },
    async createLead() {
      if (this.saving) return;
      this.saving = true;
      this.errors = {};
      // simple client-side required check
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
    async assignLead(item, userId) {
      try {
        await axios.patch(`/api/inbox/${item.id}`, {
          name: item.name,
          contacts: item.contacts,
          comment: item.comment,
          source: item.source,
          assigned_to: userId,
          user_id: this.user.id,
        });
        await this.load();
      } catch (e) {
        console.error('Error assigning lead:', e);
        alert('Ошибка при распределении заявки');
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
          name: this.selectedItemForAssign.name,
          contacts: this.selectedItemForAssign.contacts,
          comment: this.selectedItemForAssign.comment,
          source: this.selectedItemForAssign.source,
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
      const member = this.staffMembers.find((item) => Number(item.id) === Number(userId));
      return member ? member.name : 'Неизвестно';
    },
    openHistory(id) {
      this.$router.push({ path: `/inbox/${id}/history` });
    },
    formatDate(dt) {
      if (!dt) return '';
      return new Date(dt).toLocaleString();
    },
    timeSince(dt) {
      if (!dt) return '';
      const then = new Date(dt);
      const diff = Date.now() - then.getTime();
      const minutes = Math.floor(diff / 60000);
      if (minutes < 60) return `${minutes} мин`;
      const hours = Math.floor(minutes / 60);
      if (hours < 24) return `${hours} ч`;
      const days = Math.floor(hours / 24);
      return `${days} дн`;
    },
  },
};
</script>

<style scoped>
.table td { vertical-align: middle; }
</style>
