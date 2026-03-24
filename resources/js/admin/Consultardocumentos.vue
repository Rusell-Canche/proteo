<template>
  <div class="container mt-3">
    <div class="form-group mb-4">
      <label for="coleccionSelector" class="form-label">Selecciona una colección:</label>
      <select id="coleccionSelector" class="form-select" v-model="selectedColeccion" @change="onColeccionSelected">
        <option v-for="coleccion in coleccionesFiltradas" :key="coleccion" :value="coleccion">
          {{ coleccion }}
        </option>
      </select>
    </div>

    <div v-if="documentos.length">
      <form class="d-flex mb-4" @submit.prevent="buscar">
        <input v-model="palabraClave" class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">
      </form>

      <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col" v-for="(documento, index) in paginatedDocumentos" :key="documento._id.$oid">
          <div class="card h-100">
            <div class="icon-container">
              <button class="btn-edit" @click="editarDocumento(documento)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn-delete" @click="eliminarDocumento(documento._id.$oid)">
                <i class="fas fa-minus-circle"></i>
              </button>
            </div>
            <div class="card-body">
  <p class="card-text" v-for="campo in camposDocumento" :key="campo">
    <strong>{{ formatFieldName(campo) }}:</strong>
    <span v-if="campo === 'Recurso Digital'">
      <!-- Verificar si 'Recurso Digital' tiene archivos -->
      <div v-if="documento[campo] && documento[campo].length">
        <!-- Mostrar miniatura solo del primer archivo -->
        <template v-if="isImage(documento[campo][0])">
          <a href="#" @click.prevent="openFancybox(documento, documento[campo])">
            <!-- Utilizar directamente el archivo para la miniatura -->
            <img src="assets/8344917.png" class="card-img-top thumbnail" alt="Pdf" style="width: 50px; cursor: pointer;">
          </a>
        </template>
        <template v-else-if="isPDF(documento[campo][0])">
          <a href="#" @click.prevent="openFancybox(documento, documento[campo])">
            <img src="assets/icon-pdf.png" class="card-img-top thumbnail" alt="Pdf" style="width: 50px; cursor: pointer;">
          </a>
        </template>
        <template v-else-if="isAudio(documento[campo][0])">
          <a href="#" @click.prevent="openFancybox(documento, documento[campo])">
            <img src="assets/icon-audio.png" class="card-img-top thumbnail" alt="Audio" style="width: 50px; cursor: pointer;">
          </a>
        </template>
        <template v-else-if="isVideo(documento[campo][0])">
          <a href="#" @click.prevent="openFancybox(documento, documento[campo])">
            <img src="assets/icon-video.png" class="card-img-top thumbnail" alt="Video" style="width: 50px; cursor: pointer;">
          </a>
        </template>
      </div>
    </span>
    <span v-else>{{ documento[campo] }}</span>
  </p>
</div>

          </div>
        </div>
      </div>

      <div class="pagination-container text-center mt-4">
        <button v-for="page in totalPages" :key="page" @click="changePage(page)" class="btn btn-sm"
          :class="{ active: currentPage === page }">
          {{ page }}
        </button>
      </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal" tabindex="-1" v-if="isModalOpen" style="display: block;">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Documento</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitEdit">
              <div v-for="campo in camposDocumento" :key="campo" class="mb-3">
                <label :for="campo" class="form-label">{{ formatFieldName(campo) }}</label>
                <input v-if="campo === 'Fecha'" type="date" class="form-control" v-model="documentoEdit[campo]"
                  :id="campo">
                <input v-else-if="campo !== 'Recurso Digital'" type="text" class="form-control"
                  v-model="documentoEdit[campo]" :id="campo">
              </div>

              <!-- Sección de Archivos Multimedia -->
              <div class="mb-3">
                <label class="form-label">Recurso Digital:</label>
                <input type="file" class="form-control" @change="handleFileUpload" multiple
                  accept="image/*,video/*,audio/*,.pdf">
              </div>

              <!-- Vista previa de archivos -->
              <div v-if="documentoEdit['Recurso Digital'] && documentoEdit['Recurso Digital'].length">
                <h6>Archivos Adjuntos:</h6>
                <div class="d-flex flex-wrap">
                  <div class="file-preview" v-for="(file, index) in documentoEdit['Recurso Digital']" :key="index"
                    style="position: relative;">
                    <template v-if="isImage(file)">
                      <img :src="getFileUrl(file)" alt="Imagen" class="img-thumbnail" style="width: 100px;">
                      <button type="button" class="btn-remove" @click="removeFile(index)"
                        style="position: absolute; top: 5px; right: 5px; background-color: rgba(255, 255, 255, 0.7); border: none; border-radius: 50%; font-size: 16px; color: red; padding: 5px;">✖</button>
                    </template>
                    <template v-else-if="isVideo(file)">
                      <video controls style="width: 100px;">
                        <source :src="getFileUrl(file)" type="video/mp4">
                        Tu navegador no soporta videos.
                      </video>
                      <button type="button" class="btn-remove" @click="removeFile(index)"
                        style="position: absolute; top: 5px; right: 5px; background-color: rgba(255, 255, 255, 0.7); border: none; border-radius: 50%; font-size: 16px; color: red; padding: 5px;">✖</button>
                    </template>
                    <template v-else-if="isAudio(file)">
                      <audio controls style="width: 100px;">
                        <source :src="getFileUrl(file)" type="audio/mp3">
                        Tu navegador no soporta audio.
                      </audio>
                      <button type="button" class="btn-remove" @click="removeFile(index)"
                        style="position: absolute; top: 5px; right: 5px; background-color: rgba(255, 255, 255, 0.7); border: none; border-radius: 50%; font-size: 16px; color: red; padding: 5px;">✖</button>
                    </template>
                    <template v-else-if="isPDF(file)">
                      <a :href="getFileUrl(file)" target="_blank" class="btn btn-sm btn-secondary">Ver PDF</a>
                      <button type="button" class="btn-remove" @click="removeFile(index)"
                        style="position: absolute; top: 5px; right: 5px; background-color: rgba(255, 255, 255, 0.7); border: none; border-radius: 50%; font-size: 16px; color: red; padding: 5px;">✖</button>
                    </template>
                  </div>
                </div>
              </div>


              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref, computed } from 'vue';
import { Fancybox } from "@fancyapps/ui";
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  setup() {
    const colecciones = ref([]);
    const excludedCollections = ref([
      'failed_jobs', 'navbar_colors', 'password_reset_tokens', 'migrations',
      'personal_access_tokens', 'plantillas_predeterminadas', 'noticias_collection',
      'users', 'comentarios', 'carrousel_images'
    ]);
    const coleccionesFiltradas = computed(() => {
      return colecciones.value.filter(coleccion => !excludedCollections.value.includes(coleccion));
    });
    const selectedColeccion = ref(null);
    const documentos = ref([]);
    const camposDocumento = ref([]);
    const plantillaName = ref(null);
    const palabraClave = ref('');
    const currentPage = ref(1);
    const itemsPerPage = ref(6);
    const isModalOpen = ref(false);
    const uploadedFiles = ref([]); // Archivos seleccionados
    const documentoEdit = ref({}); // Datos del documento a editar
    const nuevoArchivo = ref(null); // Archivo nuevo
    const camposPlantilla = ref([]); // Inicialización correcta
    const getFileUrl = (file) => `http://localhost:8000/storage/${encodeURIComponent(file)}`;

    const isImage = (file) => file.match(/\.(jpeg|jpg|png|gif)$/i);
    const isVideo = (file) => file.match(/\.(mp4|mov|avi)$/i);
    const isAudio = (file) => file.match(/\.(mp3|wav|ogg)$/i);
    const isPDF = (file) => file.match(/\.pdf$/i);

    
    // Obtener colecciones
    const getColecciones = async () => {
      try {
        const response = await axios.get('/documentos/plantillas');
        colecciones.value = response.data.filter((coleccion) => !coleccion.endsWith('_pendientes'));
      } catch (error) {
        console.error('Error obteniendo colecciones', error);
      }
    };

    
    const onColeccionSelected = async () => {
      if (selectedColeccion.value) {
        plantillaName.value = selectedColeccion.value;
        console.log('Nombre de la plantilla seleccionada:', plantillaName.value);

        try {
          const response = await axios.get(`/documentos/${selectedColeccion.value}`);
          documentos.value = response.data;
          currentPage.value = 1;

          if (documentos.value.length > 0) {
            const documentoConMasCampos = documentos.value.reduce((prev, current) =>
              (Object.keys(current).length > Object.keys(prev).length) ? current : prev
            );
            camposDocumento.value = Object.keys(documentoConMasCampos).filter(campo => campo !== '_id');
          } else {
            camposDocumento.value = [];
          }

        } catch (error) {
          console.error('Error obteniendo documentos o plantilla', error);
          documentos.value = [];
          camposDocumento.value = [];
        }
      } else {
        plantillaName.value = null;
        documentos.value = [];
        camposDocumento.value = [];
      }
    };





    const formatFieldName = (fieldName) => {
      const fieldMap = {
        created_at: 'Fecha de creación',
        updated_at: 'Última actualización'
      };
      return fieldMap[fieldName] || fieldName.replace(/_/g, ' ').replace(/\w\S*/g, (word) => word.charAt(0).toUpperCase() + word.substr(1).toLowerCase());
    };

    const editarDocumento = (documento) => {
      console.log("Editando documento:", documento);
      documentoEdit.value = { ...documento }; // Clonar el documento
      isModalOpen.value = true;
    };

    const closeModal = () => {
      isModalOpen.value = false;
    };

    // Eliminar documento
    const eliminarDocumento = async (documentoId) => {
      try {
        const result = await Swal.fire({
          title: '¿Estás seguro?',
          text: '¿Quieres eliminar este documento?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, eliminarlo'
        });

        if (result.isConfirmed) {
          await axios.delete(`/documentos/${plantillaName.value}/${documentoId}`);
          documentos.value = documentos.value.filter(doc => doc._id.$oid !== documentoId);
          Swal.fire('Eliminado', 'El documento se ha eliminado exitosamente.', 'success');
        }
      } catch (error) {
        console.error('Error al eliminar el documento', error);
        Swal.fire('Error', 'Hubo un problema al eliminar el documento. Por favor, inténtalo de nuevo.', 'error');
      }
    };

    // Mostrar imágenes con Fancybox
    const openFancybox = (documento) => {
      const items = documento['Recurso Digital'].map((image) => ({
        src: `http://localhost:8000/storage/${encodeURIComponent(image)}`
      }));
      Fancybox.show(items);
    };

    const totalPages = computed(() => Math.ceil(documentos.value.length / itemsPerPage.value));

    const paginatedDocumentos = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return documentos.value.slice(start, start + itemsPerPage.value);
    });

    const changePage = (page) => {
      currentPage.value = page;
    };

    const removeImage = (index) => {
      documentoEdit.value['Recurso Digital'].splice(index, 1);
    };

    const handleFileUpload = (event) => {
      const files = event.target.files;
      if (files.length) {
        for (const file of files) {
          uploadedFiles.value.push(file);
        }
      }
    };


    const removeFile = (index) => {
      documentoEdit.value['Recurso Digital'].splice(index, 1);
    };

    // Mover la función submitEdit a los métodos


    const submitEdit = async () => {
      try {
        const formData = new FormData();

        // Agregar todos los campos excepto 'Recurso Digital'
        Object.keys(documentoEdit.value).forEach((key) => {
          if (key !== 'Recurso Digital') {
            formData.append(key, documentoEdit.value[key]);
          }
        });

        // 🔹 Agregar archivos ya existentes en 'Recurso Digital'
        if (documentoEdit.value['Recurso Digital']) {
          documentoEdit.value['Recurso Digital'].forEach((existingFile) => {
            formData.append('existing_files[]', existingFile);
          });
        }

        // 🔹 Agregar archivos nuevos si existen
        uploadedFiles.value.forEach((file) => {
          formData.append('files[]', file); // Usa 'files[]' para que coincida con el backend
        });


        await axios.post(`/documentos/${plantillaName.value}/${documentoEdit.value._id.$oid}`, formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        Swal.fire('Éxito', 'Documento actualizado correctamente', 'success');
        closeModal();
        onColeccionSelected(); // Recargar la lista de documentos
      } catch (error) {
        console.error('Error actualizando documento', error);
        Swal.fire('Error', 'Hubo un problema al actualizar el documento.', 'error');
      }
    };

    

    onMounted(() => {
  getColecciones();
  Fancybox.bind("[data-fancybox]", {
        // Opciones de FancyBox aquí si es necesario
      });
  
  
});
    return {
      colecciones,
      coleccionesFiltradas,
      selectedColeccion,
      documentos,
      camposDocumento,
      plantillaName,
      excludedCollections,
      palabraClave,
      currentPage,
      itemsPerPage,
      isModalOpen,
      documentoEdit,
      getColecciones,
      onColeccionSelected,
      formatFieldName,
      editarDocumento,
      closeModal,
      submitEdit,
      eliminarDocumento,
      openFancybox,
      totalPages,
      paginatedDocumentos,
      changePage,
      removeImage,
      handleFileUpload,
      nuevoArchivo, getFileUrl, isImage, isVideo, isAudio, isPDF, removeFile
    };
  }
};
</script>






<style scoped>
/* Agrega aquí tus estilos personalizados */
.icon-container {
  position: relative;
  display: flex;
  justify-content: flex-end;
}

.btn-edit,
.btn-delete {
  background: none;
  border: none;
  cursor: pointer;
  color: #007bff;
  /* Color de los iconos */
}

.btn-delete {
  color: #dc3545;
  /* Color rojo para el botón de eliminar */
}

.thumbnail {
  position: relative;
}

.thumbnail .btn-remove {
  position: absolute;
  top: 0;
  right: 0;
  background: none;
  border: none;
  color: #dc3545;
  /* Color rojo para eliminar */
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.thumbnail {
  position: relative;
}

.icon-container {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  gap: 5px;
  z-index: 1;
}

.btn-edit,
.btn-delete {
  padding: 4px 8px;
  border: none;
  background: none;
  cursor: pointer;
}

.btn-edit i,
.btn-delete i {
  font-size: 18px;
  color: #333;
}

.btn-edit i:hover {
  color: #007bff;
}

.btn-delete i:hover {
  color: #dc3545;
}

.btn-remove {
  position: absolute;
  top: 0;
  right: 0;
  background: none;
  border: none;
  color: red;
  cursor: pointer;
  font-size: 18px;
}

.pagination-container {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.pagination-container .btn {
  margin: 0 5px;
  padding: 5px 10px;
}

.pagination-container .btn.active {
  background-color: #007bff;
  color: #fff;
}

.card {
  position: relative;
}

.card-body {
  padding-top: 3rem;
  /* Asegúrate de que el contenido de la tarjeta no se superponga a los íconos */
}

@media (max-width: 767.98px) {
  .pagination-container {
    flex-wrap: wrap;
  }

  .pagination-container .btn {
    flex: 1 1 auto;
    margin-bottom: 5px;
  }
}
</style>
