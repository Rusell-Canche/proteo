<template>
  <div>
    <div class="plantilla-list">
      <!-- Listado de plantillas -->
      <div v-for="plantilla in plantillasFiltradas" :key="plantilla" class="plantilla-card">
        <div class="plantilla-info">
          <h4>{{ plantilla }}</h4>
          <div class="button-group">
            <!-- Botón para abrir modal de edición -->
            <button @click="openEditModal(plantilla)" class="btn-edit" data-toggle="modal" data-target="#editModal">
              <i class="fas fa-edit"></i> <!-- Icono de editar -->
            </button>
            <!-- Botón para eliminar plantilla -->
            <button @click="eliminarPlantilla(plantilla)" class="btn-delete">
              <i class="fas fa-minus-circle"></i> <!-- Icono de eliminar -->
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para editar plantilla -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Editar Plantilla: {{ nombrePlantilla }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Formulario de edición de plantilla -->
            <form @submit.prevent="submitEditForm">
              <div v-for="(campo, index) in camposPlantilla" :key="index" class="form-group">
                <label :for="'campoNombre_' + index">Nombre del Campo</label>
                <input v-model="camposPlantilla[index].name" :name="'campoNombre_' + index" class="form-control" />

                <label :for="'campoTipo_' + index">Tipo del Campo</label>
                <select v-model="camposPlantilla[index].type" :name="'campoTipo_' + index" class="form-control">
                  <option value="string">String</option>
                  <option value="number">Number</option>
                  <option value="file">File</option>
                  <option value="date">Date</option>
                  <!-- Agrega más opciones según los tipos de dato que puedas tener -->
                </select>

                <div class="form-check mt-2">
                  <input type="checkbox" class="form-check-input" :id="'campoRequerido_' + index"
                    v-model="camposPlantilla[index].required" :name="'campoRequerido_' + index" />
                  <label class="form-check-label" :for="'campoRequerido_' + index">Campo Obligatorio</label>
                </div>

                <button type="button" @click="eliminarCampo(index)" class="btn btn-danger mt-2">Eliminar</button>
              </div>

              <button type="button" @click="agregarCampo()" class="btn btn-success mt-2">Agregar Campo</button>

              <button type="submit" class="btn btn-primary mt-2">Actualizar Plantilla</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      plantillas: [], // Lista de plantillas obtenidas del servidor
      nombrePlantilla: '', // Nombre de la plantilla seleccionada para edición
      camposPlantilla: [], // Campos de la plantilla seleccionada para edición
      originalCamposPlantilla: [], // Para mantener una copia de los campos originales
      nuevoCampo: {  // Nuevo campo que se agregará dinámicamente
        name: '',
        type: '',
        alias: '',
        required: false
      }
    } ;
  },
  computed: {
    plantillasFiltradas() {
      // Excluir plantillas específicas
      const excluidas = [
        'failed_jobs',
        'migrations',
        'password_reset_tokens',
        'personal_access_tokens',
        'plantillas_predeterminadas',
        'users',
        'carrousel_images',
        'navbar_colors',
        'comentarios', 'noticias_collection'
      ];
      return this.plantillas.filter(plantilla => !excluidas.includes(plantilla));
    }
  },
  methods: {
    async fetchPlantillas() {
      try {
        const response = await axios.get('/plantillas');
        this.plantillas = response.data;
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al obtener las plantillas.',
        });
      }
    },
    async fetchCamposPlantilla(plantillaName) {
      try {
        const response = await axios.get(`/plantillas/${plantillaName}/fields`);
        this.nombrePlantilla = plantillaName;
        this.camposPlantilla = response.data;
        this.originalCamposPlantilla = JSON.parse(JSON.stringify(response.data)); // Copia profunda de los campos originales
        $('#editModal').modal('show'); // Mostrar modal de edición
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al obtener los campos de la plantilla.',
        });
      }
    },
    async submitEditForm() {
      const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres actualizar la plantilla?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
      });

      if (result.isConfirmed) {
        try {
          const updatedFields = {
            fields: this.camposPlantilla  // Asegurarse de que los campos están dentro de un objeto "fields"
          };
          const response = await axios.put(`/plantillas/${this.nombrePlantilla}`, updatedFields);
          Swal.fire({
            icon: 'success',
            title: 'Actualizado',
            text: 'La plantilla ha sido actualizada exitosamente.',
          });
          $('#editModal').modal('hide');
        } catch (error) {
          console.error('Error al actualizar la plantilla:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al actualizar la plantilla.',
          });
        }
      }
    },
    eliminarCampo(index) {
      // Implementar lógica para eliminar un campo de la plantilla
      this.camposPlantilla.splice(index, 1);
    },
    agregarCampo() {
      // Agregar el nuevo campo al arreglo de campos de la plantilla
      this.camposPlantilla.push({ ...this.nuevoCampo });

      // Limpiar el objeto nuevoCampo para futuras adiciones
      this.nuevoCampo = {
        name: '',
        type: '',
        alias: '',
        required: false
      };
    },
    openEditModal(plantillaName) {
      // Método para abrir el modal de edición con los campos de la plantilla
      this.fetchCamposPlantilla(plantillaName);
    },
    async eliminarPlantilla(plantilla) {
      const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Estás seguro de que deseas eliminar la plantilla ${plantilla}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, cancelar',
      });

      if (result.isConfirmed) {
        try {
          const response = await axios.delete(`/plantillas/${plantilla}`);
          Swal.fire({
            icon: 'success',
            title: 'Eliminado',
            text: response.data.message,
          });
          this.fetchPlantillas(); // Refrescar la lista de plantillas
        } catch (error) {
          Swal.fire({ 
            icon: 'error', 
            title: 'Error',
            text: `Error al eliminar la plantilla: ${plantilla}`,
          });
        }
      }
    },
  },
  created() {
    this.fetchPlantillas();
  }
};
</script>


<style scoped>
/* Estilos personalizados, si es necesario */
.plantilla-list {
  display: flex;
  flex-wrap: wrap;
}

.plantilla-card {
  position: relative;
  width: calc(33.33% - 20px);
  margin: 10px;
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 20px;
  transition: all 0.3s ease;
}

.plantilla-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.plantilla-info {
  text-align: center;
}

.plantilla-info h4 {
  margin-bottom: 10px;
}

.button-group {
  position: absolute;
  top: 1px;
  right: 1px;
  background-color: transparent;
}

.btn-edit,
.btn-delete {
  padding: 4px 8px;
  margin-right: 5px;
  border: none;
  background: none;
  cursor: pointer;
}

.btn-edit i,
.btn-delete i {
  font-size: 16px;
}
</style>