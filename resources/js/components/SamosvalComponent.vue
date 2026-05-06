<template>
  <div class="container">
    <h3 style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
      <b>Список техник</b>
    </h3>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
      <button class="btn btn-secondary" @click="showModel('create')" type="button">
        Создать заявку
      </button>
    </div>

    <div class="row g-2 align-items-end mb-3">
      <div class="col-md-5">
        <label class="form-label mb-1">Фильтр по типу</label>
        <input
          type="text"
          class="form-control"
          placeholder="Например: самосвал"
          v-model.trim="filters.type"
        />
      </div>
      <div class="col-md-5">
        <label class="form-label mb-1">Фильтр по статусу</label>
        <select class="form-select" v-model.number="filters.status">
          <option :value="-1">Все статусы</option>
          <option :value="1">Активен</option>
          <option :value="0">Неактивен</option>
          <option :value="2">В ремонте</option>
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
          <th scope="col">ID (системный номер)</th>
          <th scope="col">Тип</th>
          <th scope="col">Адрес / объект</th>
          <th scope="col">Серийный номер</th>
          <th scope="col">Статус</th>
          <th scope="col">Дата добавления</th>
          <th scope="col">Дата редактирования</th>
          <th scope="col">Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(Samosval, key) in filteredSamosvals" :key="key">
          <th scope="row">{{ Samosval.system_id }}</th>
          <td>{{ Samosval.type }}</td>
          <td>{{ Samosval.address }}</td>
          <td>{{ Samosval.serial_number }}</td>
          <td :style="{ color: statusColor(Samosval.status) }">
            {{ statusLabel(Samosval.status) }}
          </td>
          <td>{{ Samosval.created_at }}</td>
          <td>{{ Samosval.updated_at }}</td>
          <td>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-warning btn-sm me-2" @click="showModel('edit', Samosval)">
                Редактировать
              </button>
            </div>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-success btn-sm me-2" v-if="Samosval.status === 0"
                @click="toggleStatus(Samosval)">
                Активировать
              </button>
              <button type="button" class="btn btn-danger btn-sm" v-if="Samosval.status === 1"
                @click="toggleStatus(Samosval)">
                Деактивировать
              </button>
            </div>
          </td>
        </tr>
        <tr v-if="filteredSamosvals.length === 0">
          <td colspan="8" class="text-muted py-4">По выбранным фильтрам ничего не найдено</td>
        </tr>
      </tbody>
    </table>

    <MDBModal id="createModal" tabindex="-1" labelledby="createModalLabel" v-model="createModal">
      <MDBModalHeader>
        <MDBModalTitle id="createModalLabel">Необходимо указать следующие данные:</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>

        <form>
          <div class="mb-3">
            <label class="form-label">Статус</label>
            <select class="form-select" v-model="newSamosval.status">
              <option value="1">Активен</option>
              <option value="0">Неактивен</option>
              <option value="2">В ремонте</option>
            </select>
            <div class="form-text">Выберите статус техники.</div>
            <div v-if="errors.status" class="alert alert-danger">
              <span v-for="msg in errors.status" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Тип</label>
            <input type="text" class="form-control" placeholder="Самосвал, экскаватор и т.д."
              v-model="newSamosval.type" />
            <div class="form-text">Укажите тип техники.</div>
            <div v-if="errors.type" class="alert alert-danger">
              <span v-for="msg in errors.type" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Системный номер</label>
            <input type="text" class="form-control" placeholder="RCL*****" v-model="newSamosval.system_id" />
            <div class="form-text">Формат: RCL + 5 цифр (итого 8 символов).</div>
            <div v-if="errors.system_id" class="alert alert-danger">
              <span v-for="msg in errors.system_id" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Адрес / объект</label>
            <input type="text" class="form-control" placeholder="Введите адрес или объект..." v-model="newSamosval.address" />
            <div class="form-text">Укажите адрес или объект размещения техники.</div>
            <div v-if="errors.address" class="alert alert-danger">
              <span v-for="msg in errors.address" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Серийный номер</label>
            <input type="text" class="form-control" placeholder="S*********" v-model="newSamosval.serial_number" />
            <div class="form-text">Формат: S + 9 цифр (итого 10 символов).</div>
            <div v-if="errors.serial_number" class="alert alert-danger">
              <span v-for="msg in errors.serial_number" :key="msg">{{ msg }}</span>
            </div>
          </div>

        </form>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="showModel('create')">Закрыть</MDBBtn>
        <MDBBtn color="success" @click="save('create')">Создать</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal id="editModal" tabindex="-1" labelledby="editModalLabel" v-model="editModal">
      <MDBModalHeader>
        <MDBModalTitle id="editModalLabel">Редактирование данных </MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>

        <form>
          <div class="mb-3">
            <label class="form-label">Статус</label>
            <select class="form-select" v-model="currentSamosval.status">
              <option value="1">Активен</option>
              <option value="0">Неактивен</option>
              <option value="2">В ремонте</option>
            </select>
            <div class="form-text">Выберите статус техники.</div>
            <div v-if="errors.status" class="alert alert-danger">
              <span v-for="msg in errors.status" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Тип</label>
            <input type="text" class="form-control" placeholder="Редактировать тип техники..."
              v-model="currentSamosval.type" />
            <div class="form-text">Укажите тип техники.</div>
            <div v-if="errors.type" class="alert alert-danger">
              <span v-for="msg in errors.type" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Системный номер</label>
            <input type="text" class="form-control" placeholder="Редактировать системный номер..." v-model="currentSamosval.system_id" />
            <div class="form-text">Формат: RCL + 5 цифр (итого 8 символов).</div>
            <div v-if="errors.system_id" class="alert alert-danger">
              <span v-for="msg in errors.system_id" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Адрес / объект</label>
            <input type="text" class="form-control" placeholder="Редактировать адрес или объект..." v-model="currentSamosval.address" />
            <div class="form-text">Укажите адрес или объект размещения техники.</div>
            <div v-if="errors.address" class="alert alert-danger">
              <span v-for="msg in errors.address" :key="msg">{{ msg }}</span>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Серийный номер</label>
            <input type="text" class="form-control" placeholder="Редактировать серийный номер..." v-model="currentSamosval.serial_number" />
            <div class="form-text">Формат: S + 9 цифр (итого 10 символов).</div>
            <div v-if="errors.serial_number" class="alert alert-danger">
              <span v-for="msg in errors.serial_number" :key="msg">{{ msg }}</span>
            </div>
          </div>

          
        </form>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="showModel('edit')">Закрыть</MDBBtn>
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
} from "mdb-vue-ui-kit";
import axios from "axios";

export default {
  components: {
    MDBModal,
    MDBModalHeader,
    MDBModalTitle,
    MDBModalBody,
    MDBModalFooter,
    MDBBtn,
  },
  data() {
    return {
      Samosvals: [],
      filters: {
        type: "",
        status: -1,
      },
      createModal: false,
      editModal: false,
      errors: {},
      newSamosval: {
        status: 0,
        type: "",
        address: "",
        serial_number: "",
        system_id: "",
      },
      currentSamosval: {
        id: null,
        status: 0,
        type: "",
        address: "",
        serial_number: "",
        system_id: "",
      },
    };
  },
  computed: {
    filteredSamosvals() {
      return this.Samosvals.filter((item) => {
        const byType = this.filters.type
          ? (item.type || "").toLowerCase().includes(this.filters.type.toLowerCase())
          : true;
        const byStatus = this.filters.status === -1
          ? true
          : Number(item.status) === Number(this.filters.status);

        return byType && byStatus;
      });
    },
  },
  mounted() {
    this.loadSamosvals();
  },
  methods: {
    resetFilters() {
      this.filters = {
        type: "",
        status: -1,
      };
    },
    loadSamosvals() {
      axios.get("/api/Samosvals")
        .then((response) => {
          this.Samosvals = response.data.Samosvals;
        })
        .catch((error) => console.log(error));
    },
    showModel(modal, Samosval = null) {
      this.errors = {};
      if (modal === "create") {
        this.createModal = !this.createModal;
        this.newSamosval = { status: 0, type: "", address: "", serial_number: "", system_id: "" };
      } else if (modal === "edit") {
        if (Samosval) this.currentSamosval = { ...Samosval };
        this.editModal = !this.editModal;
      }
    },
    statusLabel(status) {
      return {
        0: "Неактивен",
        1: "Активен",
        2: "В ремонте",
      }[status] || "Неизвестно";
    },
    statusColor(status) {
      return {
        0: "red",
        1: "green",
        2: "#d97706",
      }[status] || "#6b7280";
    },
    save(modal) {
      this.errors = {};
      const data = modal === "create" ? this.newSamosval : this.currentSamosval;
      const url = modal === "create" ? "/api/Samosvals" : `/api/Samosvals/${data.id}`;
      const method = modal === "create" ? "post" : "patch";
      axios[method](url, data)
        .then((response) => {
          this.Samosvals = response.data.Samosvals;
          modal === "create" ? (this.createModal = false) : (this.editModal = false);
        })
        .catch((error) => {
          if (error.response?.status === 422)
            this.errors = error.response.data.errors || {};
        });
    },
    toggleStatus(Samosval) {
      const updatedSamosval = { ...Samosval, status: Samosval.status === 1 ? 0 : 1 };
      axios.patch(`/api/Samosvals/${updatedSamosval.id}`, updatedSamosval)
        .then((response) => {
          this.Samosvals = response.data.Samosvals;
        })
        .catch((error) => console.log(error));
    },
  },
};
</script>
