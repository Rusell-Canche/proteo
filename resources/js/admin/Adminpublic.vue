<template>
    <div>
      <nav class="navbar navbar-expand-lg" :style="{ backgroundColor: navbarColor, padding: '0.5rem 1rem' }">
        <div class="container-fluid">
          <div class="d-flex flex-grow-1"> <!-- Utilizamos flex para alinear los elementos -->
            <a class="navbar-brand " href="#">
              <!-- Utilizamos me-auto para mover el logo a la izquierda -->
              <img src="/assets/logo.png" alt="Logo" style="height: 30px;">
            </a>
          </div>
        </div>
      </nav>
      <div>
        <h1>Vista pública</h1>
        <!-- Selector de color -->
        <input type="color" v-model="selectedColor">
        <p>Color seleccionado: {{ selectedColor }}</p>
        <!-- Botón para cambiar el color en vista previa -->
        <button @click="vistaPrevia" class="btn btn-primary">Vista Previa</button>
        <!-- Botón para confirmar y enviar al controlador -->
        <button @click="confirmarCambios" class="btn btn-success">Guardar Cambios</button>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Swal from 'sweetalert2';
  
  export default {
    data() {
      return {
        navbarColor: window.navbarColor || '#007BFF', // Inicializa con el color por defecto o el color almacenado en sesión
        selectedColor: '#007BFF' // Color inicial del selector
      };
    },
    created() {
      axios.get('/get-navbar-color')
        .then(response => {
          this.navbarColor = response.data.color;
        })
        .catch(error => {
          console.error('Error al obtener el color del navbar:', error);
        });
    },
    methods: {
      vistaPrevia() {
        this.navbarColor = this.selectedColor; // Cambia el color del navbar localmente
        console.log('Vista previa - Color cambiado:', this.selectedColor);
      },
      confirmarCambios() {
        Swal.fire({
          title: '¿Estás seguro?',
          text: '¿Estás seguro de cambiar el color del navbar?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, cambiar color',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            this.cambiarColor();
          }
        });
      },
      cambiarColor() {
        axios.post('/cambiar-color-navbar', { color: this.navbarColor })
          .then(response => {
            console.log('Color cambiado correctamente:', this.navbarColor);
            Swal.fire({
              icon: 'success',
              title: 'Color cambiado con éxito',
              text: 'El color del navbar se ha cambiado exitosamente.',
              timer: 1000, // Muestra la alerta por 2 segundos
              timerProgressBar: true,
              showConfirmButton: false
            });
          })
          .catch(error => {
            console.error('Error al cambiar el color del navbar:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Hubo un problema al cambiar el color del navbar.',
            });
          });
      }
    }
  };
  </script>
  
  <style scoped>
  .navbar {
    padding: 0.5rem 1rem;
  }
  
  input[type="color"] {
    border: none;
    width: 100px;
    height: 50px;
    cursor: pointer;
  }
  
  button {
    margin-top: 10px;
    margin-right: 10px;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .btn-primary {
    background-color: #007BFF;
    color: white;
    border: none;
  }
  
  .btn-primary:hover {
    background-color: #0056b3;
  }
  
  .btn-success {
    background-color: #28a745;
    color: white;
    border: none;
  }
  
  .btn-success:hover {
    background-color: #218838;
  }
  </style>
  