<template>
  <div class="container">
    <h3 class="text-center my-4"><b>Справочник решений по неисправностям техники</b></h3>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
      <button class="btn btn-secondary" @click="showModal('create')">
        Добавить решение 
      </button>
    </div>

    <table class="table table-hover mt-3 text-center">
      <thead class="text-center">
        <tr>
          <th>#</th>
          <th>Неисправность техники</th>
          <th>Решение</th>
          <th>Дата добавления</th>
          <th>Дата редактирования</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="solutions.length === 0">
          <td colspan="6" class="text-muted py-4">Решений пока нет</td>
        </tr>
        <tr v-for="solution in solutions" :key="solution.id">
          <th scope="row">{{ solution.id }}</th>
          <td>{{ solution.problem?.title || '—' }}</td>
          <td>{{ solution.title }}</td>
          <td>{{ solution.created_at }}</td>
          <td>{{ solution.updated_at }}</td>
          <td>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-warning btn-sm" @click="showModal('edit', solution)">
                Редактировать
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <MDBModal v-model="createModal">
      <MDBModalHeader>
        <MDBModalTitle>Необходимо описать решение для техники:</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>

        
        <div class="mb-3">
          <label class="form-label" >Неисправность техники</label>
          <select class="form-select" v-model="newSolution.problem_id">
            <option v-for="p in problems" :key="p.id" :value="p.id">{{ p.title }}</option>
          </select>
          <div class="form-text">Выберите неисправность техники.</div>
          <div v-if="errors.problem_id" class="alert alert-danger mt-1">
            <span v-for="msg in errors.problem_id" :key="msg">{{ msg }}</span>
          </div>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label">Решение</label>
          <input type="text" class="form-control" placeholder="Введите решение..." v-model="newSolution.title"/>
          <div class="form-text">Поле должно содержать не менее 5 символов и не более 255.</div>
          <div v-if="errors.title" class="alert alert-danger mt-1">
            <span v-for="msg in errors.title" :key="msg">{{ msg }}</span>
          </div>
        </div>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="showModal('create')">Закрыть</MDBBtn>
        <MDBBtn color="success" @click="save('create')">Создать</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="editModal">
      <MDBModalHeader>
        <MDBModalTitle>Редактировать решение</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>
        <div class="mb-3 text-start">
          <label class="form-label">Неисправность техники</label>
          <select class="form-select" v-model="currentSolution.problem_id">
            <option v-for="p in problems" :key="p.id" :value="p.id">{{ p.title }}</option>
          </select>
          <div class="form-text">Выберите неисправность техники.</div>
          <div v-if="errors.problem_id" class="alert alert-danger mt-1">
            <span v-for="msg in errors.problem_id" :key="msg">{{ msg }}</span>
          </div>
        </div>
      
        <div class="mb-3 text-start">
          <label class="form-label">Решение</label>
          <input type="text" class="form-control" v-model="currentSolution.title"/>
          <div class="form-text">Поле должно содержать не менее 5 символов и не более 255.</div>
          <div v-if="errors.title" class="alert alert-danger mt-1">
            <span v-for="msg in errors.title" :key="msg">{{ msg }}</span>
          </div>
        </div>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="showModal('edit')">Закрыть</MDBBtn>
        <MDBBtn color="warning" @click="save('edit')">Сохранить изменения</MDBBtn>
      </MDBModalFooter>
    </MDBModal>
  </div>
</template>

<script>
import { MDBModal, MDBModalHeader, MDBModalTitle, MDBModalBody, MDBModalFooter, MDBBtn } from "mdb-vue-ui-kit";
import axios from "axios";

export default {
  components: { MDBModal, MDBModalHeader, MDBModalTitle, MDBModalBody, MDBModalFooter, MDBBtn },
  data() {
    return {
      problems: [],
      solutions: [],
      createModal: false,
      editModal: false,
      errors: {},
      newSolution: { title: "", problem_id: "" },
      currentSolution: { id: null, title: "", problem_id: "" },
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    async loadData() {
      try {
        const [solutionsRes, problemsRes] = await Promise.all([
          axios.get("/api/Samosvals/solutions"),
          axios.get("/api/Samosvals/problems"),
        ]);
        this.solutions = solutionsRes.data.solutions || []; 
        this.problems = problemsRes.data.problems || [];
      } catch (err) {
        console.error("Ошибка загрузки данных:", err);
      }
    },
    showModal(type, solution = null) {
      this.errors = {};
      if (type === "create") {
        this.createModal = !this.createModal;
        this.newSolution = { title: "", problem_id: "" };
      } else if (type === "edit") {
        if (solution) this.currentSolution = { ...solution, problem_id: solution.problem_id || solution.problem?.id || "" };
        this.editModal = !this.editModal;
      }
    },
    async save(type) {
      this.errors = {};
      const data = type === "create" ? this.newSolution : this.currentSolution;
      try {
        if (type === "create") {
          await axios.post("/api/Samosvals/solutions", data);
          this.createModal = false;
        } else {
          await axios.patch(`/api/Samosvals/solutions/${data.id}`, data);
          this.editModal = false;
        }
        await this.loadData(); 
      } catch (err) {
        if (err.response?.status === 422) this.errors = err.response.data.errors || {};
        else console.error("Ошибка сохранения:", err);
      }
    },
  },
};
</script>

<style scoped>
  .table th, .table td { vertical-align: middle; }
  .badge { font-size: .85rem; }
  .text-start { text-align: left; }
  
</style>

