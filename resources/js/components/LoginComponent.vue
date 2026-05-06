<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <div class="card p-4">
          <h3 class="mb-4">Вход в систему</h3>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input v-model="form.email" type="email" class="form-control" placeholder="admin@test.com" />
            <div v-if="errors.email" class="text-danger small mt-1">{{ errors.email[0] }}</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Пароль</label>
            <input v-model="form.password" type="password" class="form-control" placeholder="password123" />
            <div v-if="errors.password" class="text-danger small mt-1">{{ errors.password[0] }}</div>
          </div>
          <button @click="login" :disabled="loading" class="btn btn-primary w-100">
            <span v-if="!loading">Вход</span>
            <span v-else>Загрузка...</span>
          </button>
          <div v-if="message" class="alert mt-3" :class="{ 'alert-danger': !success, 'alert-success': success }">
            {{ message }}
          </div>

          <hr />
          <p class="small text-muted"><strong>Тестовые учетные данные:</strong></p>
          <ul class="small list-unstyled">
            <li>Админ: <code>admin@test.com</code></li>
            <li>Менеджер: <code>manager@test.com</code></li>
            <li>Пароль (все): <code>password123</code></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginComponent',
  data() {
    return {
      form: { email: '', password: '' },
      loading: false,
      message: '',
      success: false,
      errors: {},
    };
  },
  methods: {
    async login() {
      if (this.loading) return;
      this.loading = true;
      this.errors = {};
      this.message = '';

      try {
        const res = await axios.post('/api/login', this.form);
        this.success = true;
        this.message = 'Вы успешно вошли!';
        // Store user in localStorage or state
        localStorage.setItem('user', JSON.stringify(res.data.user));
        // Redirect to inbox
        setTimeout(() => {
          window.location.href = '/inbox';
        }, 500);
      } catch (e) {
        if (e.response && e.response.status === 422) {
          this.errors = e.response.data.errors || {};
        } else if (e.response && e.response.status === 401) {
          this.message = 'Неверные учетные данные';
        } else {
          this.message = 'Ошибка при входе';
          console.error(e);
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.card {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
</style>
