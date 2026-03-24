<template>
    <div class="container mt-4">
  
      <label>Nombre de la Plantilla</label>
      <input v-model="plantillaName" class="form-control mb-2" />
  
      <h3>Seleccionar Plantilla Existente:</h3>
      <select v-model="plantillaSeleccionada" @change="seleccionarPlantilla" class="form-control mb-2">
        <option v-for="plantilla in plantillas" :key="plantilla.nombre" :value="plantilla.nombre">{{ plantilla.nombre }}</option>
      </select>
  
      <h3>Campos de la Plantilla:</h3>
      <div v-for="(campo, index) in camposPlantilla" :key="index" class="campo">
        <label>Nombre del Campo</label>
        <input v-model="campo.name" :name="'campoNombre_' + index" class="form-control mb-2" />
  
        <label>Tipo del Campo</label>
        <select v-model="campo.type" :name="'campoTipo_' + index" class="form-control mb-2">
          <option value="string">String</option>
          <option value="number">Number</option>
          <option value="file">File</option>
          <option value="date">Date</option>
        </select>
  
        <div class="form-check mb-2">
          <input type="checkbox" class="form-check-input" :id="'campoRequerido_' + index" v-model="campo.required" :name="'campoRequerido_' + index" />
          <label class="form-check-label" :for="'campoRequerido_' + index">Campo Obligatorio</label>
        </div>
  
        <button type="button" @click="quitarCampo(index)" class="btn btn-danger mt-2">Quitar Campo</button>
      </div>

      <div class="button-group">

      <button type="button" @click="agregarCampo()" class="btn btn-success mt-2">Agregar Campo</button>
  
      <button type="button" @click="crearPlantilla()" class="btn btn-primary mt-2">Crear Plantilla</button>
      </div>
      <!-- Mensajes de respuesta -->
      <p v-if="mensaje" class="mt-3 alert alert-success">{{ mensaje }}</p>
      <p v-if="error" class="mt-3 alert alert-danger">{{ error }}</p>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import Swal from 'sweetalert2';
  
  export default {
    data() {
      return {
        plantillaName: '',
        plantillaSeleccionada: '', // Almacena el nombre de la plantilla seleccionada
        plantillas: [], // Almacena las plantillas existentes
        camposPlantilla: [], // Almacena los campos de la plantilla actual
        mensaje: '', // Mensaje de éxito
        error: '' // Mensaje de error
      };
    },
    created() {
      this.cargarPlantillas();
    },
    methods: {
      cargarPlantillas() {
        axios.get('/plantillas-predeterminadas')
          .then(response => {
            this.plantillas = response.data;
          })
          .catch(error => {
            console.error('Error al obtener las plantillas', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Ocurrió un error al cargar las plantillas. Por favor, inténtalo de nuevo.',
              confirmButtonColor: '#3085d6',
            });
          });
      },
      seleccionarPlantilla() {
        // Busca la plantilla seleccionada en el array de plantillas
        const plantillaSeleccionada = this.plantillas.find(plantilla => plantilla.nombre === this.plantillaSeleccionada);
  
        if (plantillaSeleccionada && plantillaSeleccionada.campos) {
          // Asigna los campos de la plantilla seleccionada a camposPlantilla
          this.camposPlantilla = plantillaSeleccionada.campos.map(campo => ({
            name: campo.nombre,
            type: campo.tipo,
            required: false,  // Define el valor de required según tus necesidades
            valor: ''         // Inicializa cualquier otro atributo necesario
          }));
        } else {
          this.camposPlantilla = [];
        }
      },
      agregarCampo() {
        // Agrega un nuevo campo al array camposPlantilla
        this.camposPlantilla.push({ name: '', type: 'string', required: false, valor: '' });
      },
      quitarCampo(index) {
        // Elimina un campo del array camposPlantilla
        this.camposPlantilla.splice(index, 1);
      },
      crearPlantilla() {
        // Validación básica
        if (!this.plantillaName) {
          this.error = 'El nombre de la plantilla es requerido.';
          return;
        }
  
        // Envía la solicitud para crear la plantilla al backend
        axios.post('/plantillas', {
          plantilla_name: this.plantillaName,
          fields: this.camposPlantilla // Asegúrate de enviar 'fields' correctamente al backend
        })
        .then(response => {
          // Muestra un mensaje de éxito utilizando SweetAlert2
          Swal.fire({
            title: 'Creada',
            text: 'La plantilla se ha creado exitosamente.',
            icon: 'success',
            confirmButtonColor: '#3085d6',
          }).then(() => {
            // Redirige al usuario después de hacer clic en "Aceptar" en la alerta
            this.$router.push({ path: '/admin/listar-plantillas' });
          });
  
          // Limpia los campos después de crear la plantilla
          this.plantillaName = '';
          this.plantillaSeleccionada = '';
          this.camposPlantilla = [];
          this.error = '';
        })
        .catch(error => {
          // Captura el error y muestra un mensaje de error
          this.error = error.response.data.error || 'Error al crear la plantilla.';
          this.mensaje = '';
        });
      }
    }
  };
  </script>
  
  <style scoped>
  .button-group {
    display: flex;
    margin-top: 10px; /* Separación entre botones y campos */
  }
  
  .button-group button:first-child {
    margin-right: 10px; /* Separación entre los dos botones */
  }
  
  .campo {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 10px; /* Separación entre campos */
  }
  </style>