<template>
  <div class="container">
    <h3 class="text-center my-4"><b>Система заявок принтеров</b></h3>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
      <button class="btn btn-secondary" @click="showModal('create')">
        Добавить заявку
      </button>
    </div>

    <table class="table table-hover mt-3">
      <thead class="text-center">
        <tr>
          <th>#</th>
          <th>Системный номер принтера</th>
          <th>Поломка</th>
          <th>Решение</th>
          <th>Статус</th>
          <th>Дата просрочки</th>
          <th>Дата добавления</th>
          <th>Дата редактирования</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <tr v-for="req in requests" :key="req.id">
          <th scope="row">{{ req.id }}</th>
          <td>{{ req.samosval?.system_id || req.Samosval?.system_id || '—' }}</td>
          <td>{{ req.problem?.title || '—' }}</td>
          <td>{{ req.solution?.title || '—' }}</td>
          <td
            :class="{
              'text-warning': req.status === 0,
              'text-primary': req.status === 1,
              'text-info': req.status === 2,
              'text-success': req.status === 3
            }"
          >
            {{ statusText(req.status) }}
          </td>
          <td>{{ req.expires_at }}</td>
          <td>{{ req.created_at }}</td>
          <td>{{ req.updated_at }}</td>
          <td>
            <div class="btn-group btn-group-sm">
              <button v-if="req.status === 0" class="btn btn-warning btn-sm" @click="showModal('edit', req)">
                Редактировать
              </button>
              <button v-if="req.status === 0" class="btn btn-info btn-sm" @click="takeInWork(req)">
                Взять в работу
              </button>
              <button v-if="req.status === 0 || req.status === 1" class="btn btn-danger btn-sm" @click="showCancelModal(req)">
                Ожидает запчасти
              </button>
              <button v-if="req.status === 2" class="btn btn-info btn-sm" @click="takeInWork(req)">
                Вернуть в работу
              </button>
              <button v-if="req.status === 1" class="btn btn-success btn-sm" @click="showModal('close', req)">
                Завершить заявку
              </button>

              <div v-else-if="req.status === 3" :style="{ color: 'grey', fontWeight: 'bold' }">
                Недоступно
              </div>
            </div>
          </td>
        </tr>
        <tr v-if="!requests.length">
          <td colspan="9" class="text-muted py-4">Пусто</td>
        </tr>
      </tbody>
    </table>

    <MDBModal v-model="createModal">
      <MDBModalHeader>
        <MDBModalTitle>Создать новую заявку:</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>
        <div class="mb-3 text-start">
          <label class="form-label">Принтер</label>
          <select class="form-select" v-model="newRequest.samosval_id">
          
            <option v-for="p in Samosvals" :key="p.id" :value="p.id">
              {{ p.system_id }}
            </option>
          </select>
          <div class="form-text">Выберите принтер.</div>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label">Поломка</label>
          <select class="form-select" v-model="newRequest.problem_id">
            
            <option v-for="p in problems" :key="p.id" :value="p.id">
              {{ p.title }}
            </option>
          </select>
          <div class="form-text">Выберите поломку.</div>
        </div>
        <div v-if="errors" class="alert alert-danger">
          <div v-for="(msgs, field) in errors" :key="field">
            <div v-for="(msg, i) in msgs" :key="i">{{ msg }}</div>
          </div>
        </div>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="createModal = false">Закрыть</MDBBtn>
        <MDBBtn color="success" @click="save('create')">Создать</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="editModal">
      <MDBModalHeader>
        <MDBModalTitle>Редактирование данных</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>
        <div class="mb-3 text-start">
          <label class="form-label">Принтер</label>
          <select class="form-select" v-model="currentRequest.samosval_id">
            <option value="">Выберите принтер</option>
            <option v-for="p in Samosvals" :key="p.id" :value="p.id">
              {{ p.system_id }}
            </option>
          </select>
          <div class="form-text">Выберите принтер из списка.</div>
        </div>
        <div class="mb-3 text-start">
          <label class="form-label">Поломка</label>
          <select class="form-select" v-model="currentRequest.problem_id">
            <option value="">Выберите тип поломки.</option>
            <option v-for="p in problems" :key="p.id" :value="p.id">
              {{ p.title }}
            </option>
          </select>
          <div class="form-text">Выберите тип поломки.</div>
        </div>
        <div v-if="errors" class="alert alert-danger">
          <div v-for="(msgs, field) in errors" :key="field">
            <div v-for="(msg, i) in msgs" :key="i">{{ msg }}</div>
          </div>
        </div>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="editModal = false">Закрыть</MDBBtn>
        <MDBBtn color="warning" @click="save('edit')">Сохранить изменения</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="closeModal">
      <MDBModalHeader>
        <MDBModalTitle>Закрытие заявки</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>
        <div class="mb-3">
          <label class="form-label">Решение</label>
          <select class="form-select" v-model="currentRequest.solution_id">
            <option v-for="s in filteredSolutions" :value="s.id" :key="s.id">
              {{ s.title }}
            </option>
          </select>
          <div class="form-text">Выберите решение.</div>
        </div>
        <div v-if="errors" class="alert alert-danger">
          <div v-for="(msgs, field) in errors" :key="field">
            <div v-for="(msg, i) in msgs" :key="i">{{ msg }}</div>
          </div>
        </div>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="closeModal = false">Закрыть</MDBBtn>
        <MDBBtn color="success" @click="save('close')">Закрыть заявку</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="cancelModal">
      <MDBModalHeader>
        <MDBModalTitle>Подтверждение статуса</MDBModalTitle>
      </MDBModalHeader>
      <MDBModalBody>
        <p class="text-danger">Перевести заявку в статус "Ожидает запчасти"?</p>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="cancelModal = false">Нет</MDBBtn>
        <MDBBtn color="danger" @click="cancelRequest">Да</MDBBtn>
      </MDBModalFooter>
    </MDBModal>
  </div>
</template>


<script>
import axios from 'axios'
import { nextTick } from 'vue';
import {
  MDBModal,
  MDBModalHeader,
  MDBModalTitle,
  MDBModalBody,
  MDBModalFooter,
  MDBBtn
} from 'mdb-vue-ui-kit'

export default {
  components: {
    MDBModal,
    MDBModalHeader,
    MDBModalTitle,
    MDBModalBody,
    MDBModalFooter,
    MDBBtn
  },

  data() {
    return {
      requests: [],
      Samosvals: [],
      problems: [],
      solutions: [],

      createModal: false,
      editModal: false,
      closeModal: false,
      cancelModal: false,

      errors: null,

      newRequest: { samosval_id: '', problem_id: '' },
      currentRequest: { id: null, samosval_id: '', problem_id: '', solution_id: '' },
      currentCancelRequest: { id: null }
    }
  },

  mounted() {
    this.fetchAll()
    this.fetchSamosvals()
    this.fetchProblems()
    this.fetchSolutions()
  },

  computed: {
    filteredSolutions() {
      if (!this.currentRequest.id) return []
      const req = this.requests.find(r => r.id === this.currentRequest.id)
      if (!req) return []
      return this.solutions.filter(s => s.problem_id === req.problem_id)
    }
  },

  methods: {
    statusText(status) {
      return { 0: 'Новая', 1: 'В работе', 2: 'Ожидает запчасти', 3: 'Завершена' }[status] || '—'
    },

    async fetchAll() {
      const r = await axios.get('/api/Samosval-requests')
      this.requests = r.data.requests || []
    },
    async fetchSamosvals() {
      const r = await axios.get('/api/Samosvals')
      this.Samosvals = r.data.Samosvals || []
    },
    async fetchProblems() {
      const r = await axios.get('/api/Samosvals/problems')
      this.problems = r.data.problems || []
    },
    async fetchSolutions() {
      const r = await axios.get('/api/Samosvals/solutions')
      this.solutions = r.data.solutions || []
    },

    showModal(modal, req = null) {
      this.errors = null

      if (modal === 'create') {
        this.createModal = true
        this.newRequest = { samosval_id: '', problem_id: '' }
      }

      else if (modal === 'edit') {
        this.editModal = true
        if (req) {
          this.currentRequest = {
            id: req.id,
            samosval_id: req.samosval_id || req.Samosval?.id || '',
            problem_id: req.problem_id || req.problem?.id || ''
          }
        }
      }

      else if (modal === 'close') {
        this.closeModal = true
        if (req) this.currentRequest = { id: req.id, solution_id: '' }
      }
    },

    showCancelModal(req) {
      this.currentCancelRequest = { id: req.id }
      this.cancelModal = true
    },

    async cancelRequest() {
      try {
        const r = await axios.patch(`/api/Samosval-requests/${this.currentCancelRequest.id}/cancel`)
        this.requests = r.data.requests
        this.cancelModal = false
        this.currentCancelRequest = { id: null }
      } catch (err) {
        this.errors = err.response?.data?.errors || null
      }
    },

    async save(type) {
      this.errors = null

      try {
        if (type === 'create') {
          await axios.post('/api/Samosval-requests', this.newRequest)
          this.createModal = false
          this.newRequest = { samosval_id: '', problem_id: '' }
        }

        if (type === 'edit') {
          await axios.patch(
            `/api/Samosval-requests/${this.currentRequest.id}`,
            {
              samosval_id: this.currentRequest.samosval_id,
              problem_id: this.currentRequest.problem_id
            }
          )
          this.editModal = false
        }

        if (type === 'close') {
          await axios.patch(
            `/api/Samosval-requests/${this.currentRequest.id}/close`,
            {
              solution_id: this.currentRequest.solution_id
            }
          )
          this.closeModal = false
        }

        await this.fetchAll()

      } catch (err) {
        this.errors = err.response?.data?.errors || null
        console.error(err)
      }
    },

    async takeInWork(req) {
      try {
        const r = await axios.patch(`/api/Samosval-requests/${req.id}/take`)
        this.requests = r.data.requests
      } catch (err) {
        this.errors = err.response?.data?.errors || null
        console.error(err)
      }
    }
  }
}
</script>




