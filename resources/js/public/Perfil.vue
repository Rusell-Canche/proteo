<template>
  <div v-if="isAuthenticated">
    <h1>Perfil de {{ user.nombre }} {{ user.apellido_materno }} {{ user.apellido_paterno }}</h1>


    <!-- Más contenido del perfil -->
  </div>
  <div v-else class="access-denied">
    <div class="lock"></div>
    <div class="message">
      <h1>Acceso prohibido</h1>
      <p>Lo sentimos, no tienes permisos para acceder a esta página</p>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  computed: {
    ...mapGetters(['isAuthenticated', 'getUser']),
    user() {
      return this.getUser;
    }
  },
  watch: {
    isAuthenticated(newVal) {
      if (!newVal) {
        this.redirectToLogin();
      }
    }
  },
  created() {
    if (!this.isAuthenticated) {

    }
  },
  methods: {

  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css?family=Lato');

* {
  position: relative;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Lato', sans-serif;
}

.access-denied {
  height: 80vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
} 

.container {
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: linear-gradient(to bottom right, #EEE, #AAA);
}

.message {
  text-align: center;
}

.message h1 {
  font-weight: bold; /* Añade esta línea para poner el texto en negritas */
}

.lock {
  border-radius: 5px;
  width: 55px;
  height: 45px;
  background-color: #333;
  animation: dip 1s;
  animation-delay: 1.5s;
  /* Sustituye la variable SASS por el valor directo */
}

.lock::before,
.lock::after {
  content: '';
  position: absolute;
  border-left: 5px solid #333;
  height: 20px;
  width: 15px;
  left: calc(50% - 12.5px);
}

.lock::before {
  top: -30px;
  border: 5px solid #333;
  border-bottom-color: transparent;
  border-radius: 15px 15px 0 0;
  height: 30px;
  animation: lock 2s, spin 2s;
}

.lock::after {
  top: -10px;
  border-right: 5px solid transparent;
  animation: spin 2s;
}

@keyframes lock {
  0% {
    top: -45px;
  }

  65% {
    top: -45px;
  }

  100% {
    top: -30px;
  }
}

@keyframes spin {
  0% {
    transform: scaleX(-1);
    left: calc(50% - 30px);
  }

  65% {
    transform: scaleX(1);
    left: calc(50% - 12.5px);
  }
}

@keyframes dip {
  0% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(10px);
  }

  100% {
    transform: translateY(0px);
  }
}
</style>
