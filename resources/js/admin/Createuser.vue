<template>
  <div>
    <h2 class="title">Bienvenido, para crear la cuenta proporciona los siguientes datos</h2>
  </div>
  
  <br>
  <div class="container">
    <div class="form-container">
      <form @submit.prevent="submitForm" class="max-w-md mx-auto"> 
        <div class="row mb-3">
          <div class="col-sm-6">
            <label for="nombre" class="form-label">Nombre:</label>
            <input v-model="nombre" type="text" id="nombre" name="nombre" class="form-control" required>
          </div> 
          <div class="col-sm-6">
            <label for="apellido_paterno" class="form-label">Apellido Paterno:</label>
            <input v-model="apellido_paterno" type="text" id="apellido_paterno" name="ape llido_paterno"
              class="form-control" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-sm-6">
            <label for="apellido_materno" class="form-label">Apellido Materno:</label>
            <input v-model="apellido_materno" type="text" id="apellido_materno" name="apellido_materno"
              class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label for="email" class="form-label">Correo Electrónico:</label>
            <input v-model="email" type="email" id="email" name="email" class="form-control" required>
          </div>
        </div>
         <div class="row mb-3">
          <div class="col-sm-6">
            <label for="password" class="form-label">Contraseña:</label>
            <input v-model="password" type="password" id="password" name="password" class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label for="confirm_password" class="form-label">Confirmar Contraseña:</label>
            <input v-model="confirm_password" type="password" id="confirm_password" name="confirm_password"
              class="form-control" required>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-sm-12 text-center">
            <div class="form-check form-switch d-inline-block mx-2">
              <input class="form-check-input" type="checkbox" id="adminSwitch" name="roles[]" value="administrador"
                @change="updateRoles">
              <label class="form-check-label switch-label" for="adminSwitch">Administrador</label>
            </div>
            <div class="form-check form-switch d-inline-block mx-2">
              <input class="form-check-input" type="checkbox" id="comentariosSwitch" name="roles[]" value="capturista"
                @change="updateRoles">
              <label class="form-check-label switch-label" for="comentariosSwitch">Capturista</label>
            </div>
            <div class="form-check form-switch d-inline-block mx-2">
              <input class="form-check-input" type="checkbox" id="validadorSwitch" name="roles[]" value="validador"
                @change="updateRoles">
              <label class="form-check-label switch-label" for="validadorSwitch">Validador</label>
            </div>
            <!--
            <div class="form-check form-switch d-inline-block mx-2">
              <input class="form-check-input" type="checkbox" id="plantillasSwitch" name="roles[]" value="plantillas"
                @change="updateRoles">
              <label class="form-check-label switch-label" for="plantillasSwitch">Plantillas</label>
            </div>
            <div class="form-check form-switch d-inline-block mx-2">
              <input class="form-check-input" type="checkbox" id="carruselSwitch" name="roles[]" value="carrusel"
                @change="updateRoles">
              <label class="form-check-label switch-label" for="carruselSwitch">Carrusel</label>
            </div>
            <div class="form-check form-switch d-inline-block mx-2">
              <input class="form-check-input" type="checkbox" id="carruselSwitch" name="roles[]" value="onepiece"
                @change="updateRoles">
              <label class="form-check-label switch-label" for="carruselSwitch">One Piece</label>
            </div>-->
          </div>
        </div>
        <div class="row">
          <div class="form-check form-switch text-center">
            <br>
            <button type="submit" class="btn btn-primary" @mouseenter="isHovered = true" @mouseleave="isHovered = false">
              <span v-if="!isHovered" class="default-icon"><i class="fas fa-user-plus"></i></span>
              <span v-else class="hover-icon"><i class="fas fa-user-check"></i></span>
              Registrar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style>
.container {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 8px;
}

.title {
  text-align: center;
}

.btn-primary {
  background-color: #a52446;
  border: 1px solid #a52446;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}

.btn-primary:hover {
  background-color: #ba2023;
  border-color: #ba2023;
}

.default-icon {
  margin-right: 8px;
}

.hover-icon {
  margin-right: 8px;
}
</style>

<script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  data() {
    return {
      nombre: '',
      apellido_materno: '',
      apellido_paterno: '',
      email: '',
      password: '',
      confirm_password: '',
      roles: [],
      isHovered: false // Estado para el ícono de hover

    };
  },
  methods: {
    updateRoles() {
      this.roles = [];
      const checkboxes = document.getElementsByName('roles[]');
      checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
          this.roles.push(checkbox.value);
        }
      });
    },
    resetForm() {
      this.nombre = '';
      this.apellido_materno = '';
      this.apellido_paterno = '';
      this.email = '';
      this.password = '';
      this.confirm_password = '';
      this.roles = [];

      const checkboxes = document.getElementsByName('roles[]');
      checkboxes.forEach(checkbox => {
        checkbox.checked = false;
      });
    },
    async submitForm() {
  // Verificación de campos obligatorios
  if (!this.nombre || !this.apellido_materno || !this.apellido_paterno || !this.email || !this.password || !this.confirm_password) {
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: 'Todos los campos son obligatorios',
    });
    return;
  }

  // Verificación de coincidencia de contraseñas
  if (this.password !== this.confirm_password) {
    Swal.fire({
      icon: 'error',
      title: '¡Error!',
      text: 'Las contraseñas no coinciden',
    });
    return;
  }

  // Confirmación de creación del usuario
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Quieres crear el usuario?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, crear',
    cancelButtonText: 'Cancelar'
  });

  // Proceder con la creación del usuario si se confirma
  if (result.isConfirmed) {
    const formData = {
      nombre: this.nombre,
      apellido_materno: this.apellido_materno,
      apellido_paterno: this.apellido_paterno,
      email: this.email,
      password: this.password,
      confirm_password: this.confirm_password,
      roles: [...this.roles]
    };

    try {
      const response = await axios.post('/users', formData);
      Swal.fire({
        icon: 'success',
        title: '¡Usuario creado!',
        text: response.data.message
      }).then(() => {
        this.resetForm();
      });
    } catch (error) {
      if (error.response && error.response.data && error.response.data.errors) {
        const errors = error.response.data.errors;
        let errorMessage = 'Hubo un error al crear el usuario.';

        if (errors.email && errors.email.length > 0) {
          errorMessage = 'El correo electrónico ya está en uso.';
        } else if (errors.password && errors.password.length > 0) {
          errorMessage = 'La contraseña no cumple con los requisitos.';
        }

        Swal.fire({
          icon: 'error',
          title: '¡Error!',
          text: errorMessage
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: '¡Error!',
          text: 'Hubo un error al crear el usuario. Por favor, inténtalo de nuevo.'
        });
      }
    }
  }
    }
  }
}

</script>