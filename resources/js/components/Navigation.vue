<template>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-3">

          <!-- SamosvalS -->
          <li v-if="user && user.role === 'super_admin'" class="nav-item d-flex flex-column align-items-start position-relative ms-2">
            <button type="button" class="btn btn-outline-info mt-2 ms-1" @click.stop="openMenu('Samosvals')">
              <b>Техника</b>
            </button>
            <div v-if="openSamosvals" class="dropdown-menu show shadow-lg border-0 rounded-4 px-2 py-2"
              style="position:absolute; top:100%; left:0; background:#f8f9fa; min-width:230px;" @click.stop>
              <router-link to="/Samosvals"
                class="dropdown-item py-2 px-3 rounded-3 mb-1 d-flex align-items-center gap-2 fw-semibold"
                style="transition:all .2s; color:#1e293b;" @click="closeAll">
                <i class="fa-solid fa-clipboard-list" style="color:#2563eb;"></i> Список техник
              </router-link>
              <router-link to="/Samosvals/problems"
                class="dropdown-item py-2 px-3 rounded-3 mb-1 d-flex align-items-center gap-2 fw-semibold"
                style="transition:all .2s; color:#1e293b;" @click="closeAll">
                <i class="fa-solid fa-screwdriver-wrench" style="color:#d97706;"></i> Справочник поломок
              </router-link>
              <router-link to="/Samosvals/solutions"
                class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-2 fw-semibold"
                style="transition:all .2s; color:#1e293b;" @click="closeAll">
                <i class="fa-solid fa-lightbulb" style="color:#16a34a;"></i> Справочник решений
              </router-link>
              <router-link to="/Samosvals/requests"
                class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-2 fw-semibold"
                style="transition:all .2s; color:#1e293b;" @click="closeAll">
                <i class="fa-solid fa-clipboard-check"></i> Справочник заявок
              </router-link>
            </div>
          </li>

          <li class="nav-item d-flex align-items-center ms-2">
            <router-link to="/inbox" class="btn btn-outline-primary mt-2 ms-1" @click.native="closeAll">
              <b>Список заявок</b>
            </router-link>
          </li>

          <li class="nav-item d-flex align-items-center ms-2">
            <router-link to="/staff/employees" class="btn btn-outline-info mt-2 ms-1" @click.native="closeAll">
              <b>Сотрудники / структура</b>
            </router-link>
          </li>
        </ul>

        <div class="d-flex align-items-center gap-2">
          <span class="text-muted small">{{ user ? user.name : 'Гость' }} ({{ user ? getRoleLabel(user.role) : '' }})</span>
          <button v-if="user" class="btn btn-outline-danger btn-sm" @click="logout">Выход</button>
          <router-link v-else to="/login" class="btn btn-primary btn-sm">Вход</router-link>
        </div>
      </div>
    </div>
  </nav>

  <router-view />
</template>

<script>
export default {
  data() {
    return {
      openSamosvals: false,
      openStaff: false,
      user: null,
    };
  },
  mounted() {
    const userData = localStorage.getItem('user');
    if (userData) {
      this.user = JSON.parse(userData);
    }
    document.addEventListener('click', this.closeAll);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.closeAll);
  },
  methods: {
    openMenu(which) {
      this.closeAll();
      if (which === 'Samosvals') this.openSamosvals = true;
      if (which === 'Staff') this.openStaff = true;
    },
    closeAll() {
      this.openSamosvals = false;
      this.openStaff = false;
    },
    logout() {
      localStorage.removeItem('user');
      this.user = null;
      window.location.href = '/login';
    },
    getRoleLabel(role) {
      const labels = {
        super_admin: 'Суперадмин',
        sales_manager: 'Менеджер по продажам',
      };
      return labels[role] || role;
    },
  },
};
</script>
