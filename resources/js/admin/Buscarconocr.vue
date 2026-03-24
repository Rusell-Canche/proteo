<template>
  <div class="container mt-3">
    <h1>Buscar con OCR</h1>

    <form @submit.prevent="buscarDocumentos">
      <div class="form-group">
        <label>Fecha desde:</label>
        <input type="date" v-model="filtros.fechaDesde" class="form-control" />
      </div>

      <div class="form-group">
        <label>Fecha hasta:</label>
        <input type="date" v-model="filtros.fechaHasta" class="form-control" />
      </div>

      <div class="form-group">
        <label>Tipo de Publicación:</label>
        <input type="text" v-model="filtros.tipoPublicacion" class="form-control" placeholder="Ej. Decreto" />
      </div>

      <div class="form-group">
        <label>Época:</label>
        <input type="text" v-model="filtros.epoca" class="form-control" placeholder="Ej. Décima" />
      </div>

      <div class="form-group">
        <label>Palabras clave:</label>
        <input type="text" v-model="filtros.palabrasClave" class="form-control" placeholder="Separar por espacios" />
      </div>

      <button type="submit" class="btn btn-primary mt-2">Buscar</button>
    </form>

    <hr />

    <div v-if="resultados.length">
      <h2>Resultados ({{ resultados.length }})</h2>
      <ul class="list-group">
        <li class="list-group-item" v-for="(doc, index) in resultados" :key="index">
          <strong>{{ doc['Tipo Publicación'] }}</strong> - {{ doc.Epoca }} - {{ doc['Día'] }}/{{ doc.Mes }}/{{ doc.Año }}
          <p>{{ doc.Ocr.substring(0, 300) }}...</p>
          <a v-if="doc['Recurso Digital'] && doc['Recurso Digital'].length" 
             :href="getArchivoUrl(doc['Recurso Digital'][0])" target="_blank">Ver PDF / Imagen</a>
        </li>
      </ul>
    </div>

    <div v-else-if="buscando">
      <p>Buscando documentos...</p>
    </div>

    <div v-else>
      <p>No hay resultados</p>
    </div>
  </div>
</template>


<script>
import axios from "axios";

export default {
  data() {
    return {
      filtros: {
        fechaDesde: "",
        fechaHasta: "",
        tipoPublicacion: "",
        epoca: "",
        palabrasClave: ""
      },
      resultados: [],
      buscando: false
    };
  },
  methods: {
    async buscarDocumentos() {
      try {
        this.buscando = true;
        const response = await axios.post("/busquedaconocr", this.filtros);
        this.resultados = response.data;
      } catch (error) {
        console.error(error);
        alert("Error al realizar la búsqueda");
      } finally {
        this.buscando = false;
      }
    },
    getArchivoUrl(path) {
      // Ajusta según tu almacenamiento
      return `/storage/${path}`;
    }
  }
};
</script>
