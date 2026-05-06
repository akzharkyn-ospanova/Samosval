<template>
  <div class="container mt-3">
    <h3 class="text-center my-4"><b>Неисправность техники</b></h3>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
      <button class="btn btn-secondary" @click="showModal('create')">
        Добавить поломку
      </button>
    </div>

    <table class="table table-hover mt-3 text-center">
      <thead class="text-center">
        <tr>
          <th>#</th>
          <th>Название</th>
          <th>Описание</th>
          <th>Дата добавления</th>
          <th>Дата редактирования</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="problems.length === 0">
          <td colspan="6" class="text-muted py-4">Поломок не найдено</td>
        </tr>
        <tr v-for="problem in problems" :key="problem.id">
          <th scope="row">{{ problem.id }}</th>
          <td>{{ problem.title }}</td>
          <td v-html="shortDescription(problem.description)"></td>
          <td>{{ problem.created_at }}</td>
          <td>{{ problem.updated_at }}</td>
          <td>
            <div class="d-flex justify-content-center gap-2">
              <button class="btn btn-warning btn-sm" @click="showModal('edit', problem)">
                Редактировать
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <MDBModal v-model="createModal">
      <MDBModalHeader>
        <MDBModalTitle>Необходимо описать проблему:</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>
        <form>
          <div class="mb-3 text-start">
            <label class="form-label">Название</label>
            <input placeholder="Введите название поломки..." type="text" class="form-control" v-model="form.title" />
            <div class="form-text">Поле должно содержать не менее 5 символов и не более 50.</div>
            <div v-if="errors.title" class="alert alert-danger mt-1">
              <span v-for="msg in errors.title" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label">Описание</label>
            <input placeholder="Введите описание поломки..." type="text" class="form-control" v-model="form.description" />
            <div class="form-text">Поле должно содержать не менее 5 символов и не более 255.</div>
            <div v-if="errors.description" class="alert alert-danger mt-1">
              <span v-for="msg in errors.description" :key="msg">{{ msg }}</span>
            </div>
          </div>
        </form>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="showModal('create')">Закрыть</MDBBtn>
        <MDBBtn color="success" @click="save('create')">Создать</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="editModal">
      <MDBModalHeader>
        <MDBModalTitle>Редактирование данных</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>
        <form>
          <div class="mb-3 text-start">
            <label class="form-label">Название</label>
            <input type="text" class="form-control" v-model="form.title" />
            <div class="form-text">Поле должно содержать не менее 5 символов и не более 50.</div>
            <div v-if="errors.title" class="alert alert-danger mt-1">
              <span v-for="msg in errors.title" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3 text-start">
            <label class="form-label">Описание</label>
            <input type="text" class="form-control" v-model="form.description" />
            <div class="form-text">Поле должно содержать не менее 5 символов и не более 255.</div>
            <div v-if="errors.description" class="alert alert-danger mt-1">
              <span v-for="msg in errors.description" :key="msg">{{ msg }}</span>
            </div>
          </div>
        </form>
        
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="showModal('edit')">Закрыть</MDBBtn>
        <MDBBtn color="warning" @click="save('edit')">Сохранить изменения</MDBBtn>
      </MDBModalFooter>
    </MDBModal>
  </div>
</template>

<script>
import {
  MDBModal,
  MDBModalHeader,
  MDBModalTitle,
  MDBModalBody,
  MDBModalFooter,
  MDBBtn,
} from 'mdb-vue-ui-kit';
import axios from 'axios';

export default {
  components: { MDBModal, MDBModalHeader, MDBModalTitle, MDBModalBody, MDBModalFooter, MDBBtn },
  data() {
    return {
      problems: [],
      createModal: false,
      editModal: false,
      errors: {},
      form: { id: null, title: '', description: '' },
    };
  },
  mounted() {
    this.loadProblems();
  },
  methods: {
    loadProblems() {
      axios
        .get('/api/Samosvals/problems')
        .then(r => { this.problems = r.data.problems || []; })
        .catch(console.error);
    },
    showModal(mode, problem = null) {
      this.errors = {};
      if (mode === 'create') {
        this.form = { id: null, title: '', description: '' };
        this.createModal = !this.createModal;
      } else if (mode === 'edit') {
        if (problem) this.form = { ...problem, description: problem.description || '' };
        this.editModal = !this.editModal;
      }
    },
    save(mode) {
      this.errors = {};
      const data = { title: this.form.title, description: this.form.description };
      if (mode === 'create') {
        axios.post('/api/Samosvals/problems', data)
          .then(r => { this.problems = r.data.problems; this.createModal = false; })
          .catch(err => { if (err.response?.status === 422) this.errors = err.response.data.errors || {}; });
      } else if (mode === 'edit') {
        axios.patch(`/api/Samosvals/problems/${this.form.id}`, data)
          .then(r => { this.problems = r.data.problems; this.editModal = false; })
          .catch(err => { if (err.response?.status === 422) this.errors = err.response.data.errors || {}; });
      }
    },
    shortDescription(text) {
      if (!text) return '';
      return text.length > 120 ? text.slice(0, 120) + '...' : text;
    },
  },
};
</script>

<style scoped>
.table th, .table td { vertical-align: middle; }
.text-start { text-align: left; }
.badge { font-size: .85rem; }
</style>
