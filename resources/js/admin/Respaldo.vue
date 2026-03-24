<template>
  <div class="container mt-4">
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-database"></i> Crear Respaldo</h5>
            <button class="btn btn-primary" @click="confirmBackup" :disabled="isBackingUp">
              <span v-if="isBackingUp" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              <span v-if="isBackingUp">Creando...</span>
              <span v-else>Crear Respaldo</span>
            </button>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-history"></i> Último Respaldo</h5>
            <p>{{ lastBackupDate || 'No se ha realizado ningún respaldo' }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="card-title"><i class="fas fa-list"></i> Lista de Respaldos</h5>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Nombre del Archivo</th>
              <th>Fecha de Respaldos</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="backup in backupList" :key="backup.filename">
              <td>{{ backup.filename }}</td>
              <td>{{ backup.backupDate }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title"><i class="fas fa-file-upload"></i> Realizar MongoRestore</h5>
        <div class="mb-3">
          <input type="file" class="form-control" @change="handleFileUpload" />
        </div>
        <button class="btn btn-primary" @click="confirmRestore">Restaurar Respaldo</button>
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
      message: '',
      lastBackupDate: null,
      backupList: [],
      selectedFile: null,
      restoreMessage: '',
      isBackingUp: false,
    };
  },
  methods: {
    confirmBackup() {
      Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres crear un respaldo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.backup();
        }
      });
    },
    backup() {
      this.isBackingUp = true;

      Swal.fire({
        title: 'Creando respaldo...',
        html: 'Por favor, espera un momento.',
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
        },
        willClose: () => {
          axios.get('/backup/create')
            .then(response => {
              this.isBackingUp = false;
              this.fetchBackups();
              Swal.fire({
                icon: 'success',
                title: 'Respaldo creado con éxito',
                showClass: {
                  popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                  popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
              });
            })
            .catch(error => {
              this.isBackingUp = false;
              Swal.fire({
                icon: 'error',
                title: 'Error al crear el respaldo',
                text: 'Hubo un problema al crear el respaldo.',
              });
              console.error(error);
            });
        }
      });
    },
    confirmRestore() {
      Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres restaurar el respaldo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.restoreBackup();
        }
      });
    },
    restoreBackup() {
      if (!this.selectedFile) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Por favor, selecciona un archivo de respaldo primero.',
        });
        return;
      }

      Swal.fire({
        title: 'Restaurando respaldo...',
        html: 'Por favor, espera un momento.',
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
        },
        willClose: () => {
          const formData = new FormData();
          formData.append('backupFile', this.selectedFile);

          axios.post('/backup/restore', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
            .then(response => {
              Swal.fire({
                icon: 'success',
                title: 'Respaldo restaurado con éxito',
                showClass: {
                  popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                  popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
              });
            })
            .catch(error => {
              Swal.fire({
                icon: 'error',
                title: 'Error al restaurar el respaldo',
                text: 'Hubo un problema al restaurar el respaldo.',
              });
              console.error(error);
            });
        }
      });
    },
    abrirApp() {
      axios.get('/backup/open')
        .then(response => {
          console.log(response.data.message);
        })
        .catch(error => {
          console.error('Error al abrir la carpeta de respaldos', error);
        });
    },
    fetchBackups() {
      axios.get('/backup/list')
        .then(response => {
          this.backupList = response.data.backupList;
          return axios.get('/backup/last');
        })
        .then(response => {
          this.lastBackupDate = response.data.lastBackupDate;
        })
        .catch(error => {
          console.error('Error al obtener la lista de respaldos', error);
        });
    },
    handleFileUpload(event) {
      this.selectedFile = event.target.files[0];
    }
  },
  mounted() {
    this.fetchBackups();
  }
};
</script>

<style scoped>
/* Estilos personalizados si es necesario */
</style>
