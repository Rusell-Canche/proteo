<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <img src="/assets/logo.png" alt="Your Logo" width="400" height="180" style="display: block; margin: 0 auto;">

        <form @submit.prevent="submitForm" class="px-4 py-5 bg-white shadow-sm rounded-lg">
          <h2 class="mb-4">Iniciar Sesión</h2>

          <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input v-model="email" id="email" class="form-control" type="email" name="email" placeholder="Correo Electrónico" required autofocus autocomplete="username">
            <div v-if="errors.email" class="text-danger">{{ errors.email }}</div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group">
              <input v-model="password" id="password" class="form-control" :type="passwordFieldType" name="password" placeholder="Contraseña" required autocomplete="current-password">
              <button type="button" class="btn btn-outline-secondary" @click="togglePasswordVisibility">
                <i :class="passwordIconClass"></i>
              </button>
            </div>
            <div v-if="errors.password" class="text-danger">{{ errors.password }}</div>
          </div>

          <div class="mb-3 form-check">
            <input v-model="remember" type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Recuérdame</label>
            <br>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
          </div>
        </form>

        <!-- Leyenda de registro -->
        <p class="text-center mt-3">
          ¿No tienes una cuenta? <a href="/register">Regístrate aquí</a>
        </p>
      </div>
    </div>
  </div>
</template>



<script>
import axios from 'axios';
import { mapActions } from 'vuex';

export default {
  data() {
    return {
      email: '',
      password: '',
      remember: false,
      errors: {},
      passwordFieldType: 'password',
    };
  },
  computed: {
    passwordIconClass() {
      return this.passwordFieldType === 'password' ? 'fas fa-eye-slash text-gray-400 hover:text-red-900' : 'fas fa-eye text-gray-400 hover:text-red-900';
    }
  },
  methods: {
    ...mapActions(['login']),
    togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
    },
    submitForm() {
      axios.post('/loginUser', { email: this.email, password: this.password })
        .then(response => {
          if (response && response.data) {
            console.log('Login successful', response.data);
            // Actualiza el estado de Vuex para indicar que el usuario está autenticado
            this.$store.commit('setUser', response.data.user);
            // Redirige a la página de inicio usando href
            window.location.href = '/home';
          } else {
            throw new Error('Invalid response from server');
          }
        })
        .catch(error => {
          // Manejo de errores
          console.error('Error en inicio de sesión:', error);
          this.errors = error.response && error.response.data ? error.response.data.errors : { general: 'Error desconocido' };
        });
    }
  }
};
</script>

<style>
/* Estilos adicionales pueden ser añadidos aquí, similar a los estilos TailwindCSS usados en la plantilla Blade */
</style>
