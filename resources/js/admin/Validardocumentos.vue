<template>
  <div class="container mt-4">
    <h1 class="text-center mb-4">Documentos Pendientes</h1>

    <!-- Buscador -->
    <div class="mb-4 d-flex justify-content-center">
      <input
        v-model="searchTerm"
        type="text"
        placeholder="Buscar documentos..."
        class="form-control w-50"
        @input="currentPage = 1"
      />
    </div>

    <!-- SELECT PARA FILTRAR POR COLECCIÓN -->
    <div class="mb-4 d-flex justify-content-center">
      <select class="form-control w-50" v-model="selectedPlantilla" @change="currentPage = 1">
        <option value="">Mostrar todas las colecciones</option>
        <option v-for="col in plantillas" :key="col" :value="col">
          {{ cleanCollectionName(col) }}
        </option>
      </select>
    </div>

    <!-- Mensaje cuando no hay documentos -->
    <p v-if="documentosFiltrados.length === 0" class="text-center">No hay documentos pendientes.</p>

    <!-- Lista de documentos filtrados y paginados -->
    <div
      v-for="(doc, index) in documentosPaginados"
      :key="index"
      class="documento mb-3 p-3 border rounded shadow-sm"
    >
      <h5 class="text-primary">Fondo al que pertenece: {{ cleanCollectionName(doc._coleccion) }}</h5>

      <div
        v-for="field in Object.keys(doc).filter(field => !isHiddenField(field))"
        :key="field"
        class="mb-2"
      >
        <p>
          <strong>{{ formatFieldName(field) }}:</strong>
          <span v-if="field === 'Recurso Digital' && Array.isArray(doc[field])">
            <a
              v-for="(image, imgIndex) in doc[field]"
              :key="imgIndex"
              :href="getImageUrl(image)"
              :data-fancybox="'gallery-' + index"
            >
              <i class="fas fa-external-link-alt"></i>
            </a>
          </span>
          <span v-else>{{ doc[field] }}</span>
        </p>
      </div>

      <div class="d-flex justify-content-start">
        <button
          @click="aprobarDocumento(doc._coleccion, doc._id?.['$oid'] || doc._id)"
          class="btn btn-success me-2"
        >
          Aprobar
        </button>
        <button
          @click="eliminarDocumento(doc._coleccion, doc._id?.['$oid'] || doc._id)"
          class="btn btn-danger"
        >
          Denegar
        </button>
      </div>
    </div>

    <!-- Paginador -->
    <div v-if="totalPages > 1" class="d-flex justify-content-center mt-4">
      <nav>
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" @click="currentPage--" :disabled="currentPage === 1">
              Anterior
            </button>
          </li>

          <li
            class="page-item"
            v-for="page in totalPages"
            :key="page"
            :class="{ active: currentPage === page }"
          >
            <button class="page-link" @click="currentPage = page">{{ page }}</button>
          </li>

          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button
              class="page-link"
              @click="currentPage++"
              :disabled="currentPage === totalPages"
            >
              Siguiente
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

export default {
  data() {
    return {
      pendientes: [],
      searchTerm: "",
      selectedPlantilla: "",
      plantillas: [],
      currentPage: 1,
      itemsPerPage: 3,
    };
  },

  mounted() {
    this.obtenerPendientes();
  },

  computed: {
    documentosFiltrados() {
      const docsConContenido = this.pendientes.filter((p) => p.documents.length > 0);

      let todosDocumentos = docsConContenido.flatMap((p) =>
        p.documents.map((doc) => ({
          ...doc,
          _coleccion: p.collection,
        }))
      );

      // FILTRO POR COLECCIÓN
      if (this.selectedPlantilla) {
        todosDocumentos = todosDocumentos.filter(
          (d) => d._coleccion === this.selectedPlantilla
        );
      }

      // FILTRO POR TEXTO
      if (!this.searchTerm) return todosDocumentos;

      const searchLower = this.searchTerm.toLowerCase();

      return todosDocumentos.filter((doc) =>
        Object.values(doc).some((val) => {
          if (typeof val === "string") {
            return val.toLowerCase().includes(searchLower);
          }
          if (Array.isArray(val)) {
            return val.join(" ").toLowerCase().includes(searchLower);
          }
          return false;
        })
      );
    },

    documentosPaginados() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.documentosFiltrados.slice(start, end);
    },

    totalPages() {
      return Math.ceil(this.documentosFiltrados.length / this.itemsPerPage);
    },
  },

  methods: {
    async obtenerPendientes() {
      try {
        const response = await axios.get("/documentos-pendientes");
        this.pendientes = response.data;

        // Generar lista de colecciones
        this.plantillas = [...new Set(this.pendientes.map((p) => p.collection))];

        this.$nextTick(() => {
          Fancybox.bind("[data-fancybox]");
        });
      } catch (error) {
        console.error("Error al obtener los documentos pendientes:", error);
      }
    },

    async aprobarDocumento(collection, docId) {
      try {
        const response = await axios.post(`/aprobar-documentos/${collection}/${docId}`);
        if (response.data.message) {
          this.obtenerPendientes();
          alert("Documento aprobado con éxito");
        }
      } catch (error) {
        console.error("Error al aprobar el documento:", error);
      }
    },

    async eliminarDocumento(collection, docId) {
      try {
        const response = await axios.post("/api/documentos/eliminar", {
          collection,
          docId,
        });
        if (response.data.success) {
          this.obtenerPendientes();
          alert("Documento eliminado con éxito");
        }
      } catch (error) {
        console.error("Error al eliminar el documento:", error);
      }
    },

    formatFieldName(field) {
      return field.charAt(0).toUpperCase() + field.slice(1).replace(/_/g, " ");
    },

    isHiddenField(field) {
      return ["_id", "created_at", "updated_at", "_coleccion"].includes(field);
    },

    cleanCollectionName(collection) {
      return collection.replace(/_pendientes$/, "");
    },

    getImageUrl(image) {
      return `http://localhost:8000/storage/${encodeURIComponent(image)}`;
    },
  },
};
</script>


<style scoped>
.documento {
  background-color: #f9f9f9;
}
.pagination {
  flex-wrap: wrap !important;
}

</style>
