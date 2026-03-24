<template>
  <div class="container mt-3">
    <form class="d-flex mb-4" @submit.prevent="buscar">
      <input v-model="palabraClave" class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">
      <button class="btn btn-outline-success" type="submit">Buscar</button>
    </form>

    <div v-if="resultados.length">
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col" v-for="(documento, index) in paginatedResultados" :key="documento._id.$oid">
          <div class="card h-100">
            <!-- Contenedor de íconos en la esquina superior derecha -->
            <div class="icon-container">
              <button class="btn-edit">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn-delete" @click="eliminarDocumento(documento._id.$oid, documento.tipo_coleccion)">
                <i class="fas fa-minus-circle"></i>
              </button>
            </div>
            <div class="card-body">
              <p class="card-text" v-for="(value, key) in documento" :key="key">
                <template v-if="!shouldExcludeField(key)">
                  <strong>{{ formatFieldName(key) }}:</strong>
                  <span v-if="key === 'Recurso Digital'">
                    <a @click.prevent="openFancybox(documento)" href="#" :data-fancybox="'gallery-' + index">
                      <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                    </a> 
                  </span>
                  <span v-else>{{ value }}</span>
                </template>
              </p>
            </div>
          </div>
        </div>
      </div> 
      <!-- Paginador -->
      <div class="pagination-container text-center mt-4">
        <button v-for="page in totalPages" :key="page" @click="changePage(page)" class="btn btn-sm" :class="{ active: currentPage === page }">
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { Fancybox } from "@fancyapps/ui";
import '@fancyapps/ui/dist/fancybox/fancybox.css';

export default {
  data() {
    return {
      palabraClave: '',
      resultados: [],
      currentPage: 1,
      itemsPerPage: 6
    };
  },
  computed: {
    paginatedResultados() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.resultados.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.resultados.length / this.itemsPerPage);
    }
  },
  methods: {
    buscar() {
      axios.post('/searchAdmin', { palabra_clave: this.palabraClave })
        .then(response => {
          this.resultados = response.data;
          this.currentPage = 1; // Reiniciar a la primera página cuando se realiza una nueva búsqueda
        })
        .catch(error => {
          console.error('Error al realizar la búsqueda:', error);
        });
    },
    shouldExcludeField(key) {
      const excludedFields = ['_id'];
      return excludedFields.includes(key);
    },
    formatFieldName(fieldName) {
      const fieldMap = {
        created_at: 'Fecha de creación',
        updated_at: 'Última actualización'
      };
      return fieldMap[fieldName] || fieldName.replace(/_/g, ' ').replace(/\w\S*/g, (word) => word.charAt(0).toUpperCase() + word.substr(1).toLowerCase());
    },
    eliminarDocumento(documentoId, tipo_coleccion) {
      console.log(`Intentando eliminar documento con ID: ${documentoId} y tipo de colección: ${tipo_coleccion}`);
      Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Quieres eliminar este documento?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(`/documentos/${tipo_coleccion}/${documentoId}`)
            .then(response => {
              const index = this.resultados.findIndex(doc => doc._id.$oid === documentoId);
              if (index !== -1) {
                // Eliminar el documento del array resultados
                this.resultados.splice(index, 1);
                // Forzar la reactividad
                this.resultados = [...this.resultados];
              }
              Swal.fire('Eliminado', 'El documento ha sido eliminado.', 'success');
            })
            .catch(error => {
              console.error('Error al eliminar el documento:', error);
              Swal.fire('Error', 'Hubo un problema al eliminar el documento.', 'error');
            });
        }
      });
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    openFancybox(documento) {
      const items = documento['Recurso Digital'].map((image, index) => ({
        src: this.getAbsoluteFileUrl(image),
        opts: {
          caption: documento['Recurso Digital'][index]
        }
      }));

      Fancybox.show(items);
    },
    getAbsoluteFileUrl(relativePath) {
      const baseUrl = 'http://localhost:8000/storage';
      return `${baseUrl}/${encodeURIComponent(relativePath)}`;
    }
  },
  mounted() {
    Fancybox.bind("[data-fancybox^='gallery']", {
      // Opciones adicionales de FancyBox si es necesario
    });
  }
};
</script>

<style>
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
  font-size: 16px;
  color: #000;
}

.btn-edit i:hover {
  color: #007bff;
}

.btn-delete i:hover {
  color: #dc3545;
}

.pagination-container {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.pagination-container .btn {
  padding: 5px 10px;
}

.pagination-container .btn.active {
  background-color: #007bff;
  color: white;
}

.card {
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.card-body {
  padding-top: 40px; /* Ajusta el padding superior para evitar superposición con los íconos */
}
</style>
