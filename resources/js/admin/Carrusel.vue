<template>
  <div class="container mt-3">
    <h2>Subir nueva imagen al carrusel</h2>

    <div class="mb-3">
      <label for="startDate" class="form-label">Fecha de Inicio:</label>
      <input type="date" class="form-control" id="startDate" v-model="startDate" name="startDate" required />
    </div>

    <div class="mb-3">
      <label for="endDate" class="form-label">Fecha de Fin:</label>
      <input type="date" class="form-control" id="endDate" v-model="endDate" name="endDate" required />
    </div>

    <div class="mb-3">
      <label for="enlace" class="form-label">Enlace:</label>
      <input type="url" class="form-control" id="enlace" v-model="enlace" name="enlace" placeholder="https://example.com"
        required />
    </div>


    <div class="mb-3">
      <label for="image" class="form-label">Seleccionar Imagen:</label>
      <input type="file" class="form-control" id="image" @change="onFileChange" name="image" accept="image/*"
        required />
    </div>

    <button class="btn btn-primary" @click="uploadImage">Subir Imagen</button>
  </div>

  <div class="container mt-3">
    <h2>Datos de las imágenes del Carrusel</h2>

    <table class="table">
      <thead>
        <tr>
          <th scope="col">Imagen</th>
          <th scope="col">Enlace</th>

          <th scope="col">Fecha de Inicio</th>
          <th scope="col">Fecha de Fin</th>
          
          <th scope="col">Acciones</th>
          
        </tr>
      </thead>
      <tbody>
        <tr v-for="imageData in allImagesData" :key="imageData.id">
          <td @click="abrirImagen(imageData.imagen)" style="cursor: pointer;">
            <i class="fas fa-image" aria-hidden="true"></i>
          </td>
          <td>{{ imageData.fecha_inicio }}</td>
          <td>{{ imageData.fecha_fin }}</td>
          <td>{{ imageData.enlace }}</td>

          <td>
            <button class="btn btn-danger" @click="eliminarImagen(imageData._id)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      startDate: '',
      endDate: '',
      selectedFile: null,
      enlace: '',
      allImagesData: []
    };
  },
  mounted() {
    this.getAllImages();
  },
  methods: {
    onFileChange(event) {
      this.selectedFile = event.target.files[0];
    },
    uploadImage() {
      if (!this.selectedFile || !this.startDate || !this.endDate || !this.enlace) {
        Swal.fire({
          icon: 'warning',
          title: 'Campos requeridos',
          text: 'Por favor, completa todos los campos antes de subir la imagen.',
          confirmButtonColor: '#3085d6'
        });
        return;
      }

      let formData = new FormData();
      formData.append('imagen', this.selectedFile);
      formData.append('fecha_inicio', this.startDate);
      formData.append('fecha_fin', this.endDate);
      formData.append('enlace', this.enlace);


      axios.post('/carrousel/store', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
        .then(response => {
          Swal.fire({
            icon: 'success',
            title: 'Imagen subida exitosamente',
            showConfirmButton: false,
            timer: 1500,
            confirmButtonColor: '#3085d6'
          });
          this.getAllImages();
        })
        .catch(error => {
          console.error('Error al subir la imagen', error);
          Swal.fire({
            icon: 'error',
            title: 'Error al subir la imagen',
            text: 'Por favor, inténtalo nuevamente',
            confirmButtonColor: '#3085d6'
          });
        });
    },
    getAllImages() {
      axios.get('/carrousel/images')
        .then(response => {
          this.allImagesData = response.data;
        })
        .catch(error => {
          console.error('Error al obtener las imágenes del carrusel', error);
        });
    },
    eliminarImagen(id) {
      console.log('Intentando eliminar imagen con ID:', id);

      Swal.fire({
        title: '¿Estás seguro?',
        text: 'La imagen se eliminará permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      })
        .then((result) => {
          if (result.isConfirmed) {
            axios.delete(`/carrousel/delete/${id}`)
              .then(response => {
                Swal.fire({
                  icon: 'success',
                  title: 'Imagen eliminada exitosamente',
                  showConfirmButton: false,
                  timer: 1500,
                  confirmButtonColor: '#3085d6'
                });
                this.getAllImages();
              })
              .catch(error => {
                console.error('Error al eliminar la imagen', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error al eliminar la imagen',
                  text: 'Por favor, inténtalo nuevamente',
                  confirmButtonColor: '#3085d6'
                });
              });
          }
        });
    },
    abrirImagen(rutaImagen) {
      window.open(rutaImagen, '_blank');
    }
  }
};
</script>

<style scoped>
/* Estilos opcionales si es necesario */
</style>