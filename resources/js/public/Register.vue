<template>
  <div>
    <h2 class="title">¡Bienvenido! Para crear la cuenta proporciona los siguientes datos</h2>
  </div>
  <br>
  <div class="container">
    <div class="form-container">
      <form @submit.prevent="submitForm" class="max-w-md mx-auto">
        <!-- Existing fields -->
        <div class="row mb-3">
          <div class="col-sm-6">
            <label for="nombre" class="form-label">Nombre:</label>
            <input v-model="nombre" type="text" id="nombre" name="nombre" class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label for="apellido_paterno" class="form-label">Apellido Paterno:</label>
            <input v-model="apellido_paterno" type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" required>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-sm-6">
            <label for="apellido_materno" class="form-label">Apellido Materno:</label>
            <input v-model="apellido_materno" type="text" id="apellido_materno" name="apellido_materno" class="form-control" required>
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
            <input v-model="confirm_password" type="password" id="confirm_password" name="confirm_password" class="form-control" required>
          </div>
        </div>
        <!-- New fields -->
        <div class="row mb-3">
          <div class="col-sm-6">
            <label for="edad" class="form-label">Edad:</label>
            <input v-model="edad" type="number" id="edad" name="edad" class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label for="estado" class="form-label">Estado:</label>
            <select v-model="estado" class="form-select">
              <option value="">Selecciona un estado</option>
              <option value="Aguascalientes">Aguascalientes</option>
              <option value="Baja California">Baja California</option>
              <option value="Baja California Sur">Baja California Sur</option>
              <option value="Campeche">Campeche</option>
              <option value="Chiapas">Chiapas</option>
              <option value="Chihuahua">Chihuahua</option>
              <option value="Coahuila">Coahuila</option>
              <option value="Colima">Colima</option>
              <option value="Durango">Durango</option>
              <option value="Estado de México">Estado de México</option>
              <option value="Guanajuato">Guanajuato</option>
              <option value="Guerrero">Guerrero</option>
              <option value="Hidalgo">Hidalgo</option>
              <option value="Jalisco">Jalisco</option>
              <option value="Michoacán">Michoacán</option>
              <option value="Morelos">Morelos</option>
              <option value="Nayarit">Nayarit</option>
              <option value="Nuevo León">Nuevo León</option>
              <option value="Oaxaca">Oaxaca</option>
              <option value="Puebla">Puebla</option>
              <option value="Querétaro">Querétaro</option>
              <option value="Quintana Roo">Quintana Roo</option>
              <option value="San Luis Potosí">San Luis Potosí</option>
              <option value="Sinaloa">Sinaloa</option>
              <option value="Sonora">Sonora</option>
              <option value="Tabasco">Tabasco</option>
              <option value="Tamaulipas">Tamaulipas</option>
              <option value="Tlaxcala">Tlaxcala</option>
              <option value="Veracruz">Veracruz</option>
              <option value="Yucatán">Yucatán</option>
              <option value="Zacatecas">Zacatecas</option>
            </select>          
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-sm-6">
            <label for="ocupacion" class="form-label">Ocupación:</label>
            <select v-model="ocupacion" id="editedOcupacion" class="form-select">
              <option value="" disabled selected>Selecciona tu ocupación</option>
              <option value="Estudiante">Estudiante</option>
              <option value="Maestro">Maestro</option>
              <option value="Trabajador">Trabajador</option>
              <option value="Investigador">Investigador</option>
              <option value="Desempleado">Desempleado</option>
            </select>          
          </div>
          <div class="col-sm-6">
            <label for="escolaridad" class="form-label">Escolaridad:</label>
            <select v-model="escolaridad" id="editedEscolaridad" class="form-select">
              <option value="">Selecciona una escolaridad</option>
              <option value="Primaria">Primaria</option>
              <option value="Secundaria">Secundaria</option>
              <option value="Bachillerato">Bachillerato</option>
              <option value="Universidad">Universidad</option>
              <option value="Postgrado">Postgrado</option>
            </select>          
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-sm-6">
            <label for="institucion" class="form-label">Institución de Origen:</label>
            <input v-model="institucion" type="text" id="institucion" name="institucion" class="form-control" required>
          </div>
          <div class="col-sm-6">
            <label for="genero" class="form-label">Género:</label>
            <select v-model="genero" id="genero" class="form-select">
              <option value="" disabled selected>Selecciona tu género</option>
              <option value="Masculino">Masculino</option>
              <option value="Femenino">Femenino</option>
              <option value="Otro">Otro</option>
            </select>          
          </div>
        </div>
        <div class="row">
          <div class="form-check form-switch text-center">
            <br>
            <button type="submit" class="btn btn-primary">Registrar</button>
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
      edad: '', // New data property
      estado: '', // New data property
      ocupacion: '', // New data property
      escolaridad: '', // New data property
      genero: '' // New data property
    };
  },
  methods: {
    resetForm() {
      this.nombre = '';
      this.apellido_materno = '';
      this.apellido_paterno = '';
      this.email = '';
      this.password = '';
      this.confirm_password = '';
      this.edad = ''; // Reset new fields
      this.estado = '';
      this.ocupacion = '';
      this.escolaridad = '';
      this.genero = '';
    },
    submitForm() {
      if (!this.nombre || !this.apellido_materno || !this.apellido_paterno || !this.email || !this.password || !this.confirm_password || !this.edad || !this.estado || !this.ocupacion || !this.escolaridad || !this.genero) {
        Swal.fire({
          icon: 'error',
          title: '¡Error!',
          text: 'Todos los campos son obligatorios',
        });
        return;
      }

      if (this.password !== this.confirm_password) {
        Swal.fire({
          icon: 'error',
          title: '¡Error!',
          text: 'Las contraseñas no coinciden',
        });
        return;
      }

      const formData = {
        nombre: this.nombre,
        apellido_materno: this.apellido_materno,
        apellido_paterno: this.apellido_paterno,
        email: this.email,
        password: this.password,
        confirm_password: this.confirm_password,
        edad: this.edad, // Send new fields
        estado: this.estado,
        ocupacion: this.ocupacion,
        escolaridad: this.escolaridad,
        genero: this.genero,
        roles: ['user']
      };

      axios.post('/registeruser', formData)
        .then(response => {
          Swal.fire({
            icon: 'success',
            title: '¡Usuario creado!',
            text: response.data.message
          }).then(() => {
              window.location.href = '/loginUser';              
          });
        })
        .catch(error => {
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
        });
    }
  }
};
</script>
