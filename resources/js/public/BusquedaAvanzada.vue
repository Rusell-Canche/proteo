<template>
  <div class="container mt-3">
    <h2 class="title">Bienvenid@, selecciona una colección para poder realizar tu búsqueda</h2>

    <div class="container mt-3">
      <label for="coleccion">Selecciona una colección:</label>
      <select id="coleccion" class="form-control" v-model="selectedColeccion" @change="onColeccionChange">
        <option value="" disabled>Selecciona una colección</option>
        <option v-for="coleccion in filteredColecciones" :key="coleccion.nombre" :value="coleccion.nombre">
          {{ coleccion.title }}
        </option>
      </select>

      <div v-if="selectedCampos.length > 0" class="mt-3">
        <h4>Campos disponibles:</h4>
        <ul>
          <li v-for="campo in selectedCampos" :key="campo.name" class="list-group-item">
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" :id="campo.name" v-model="campo.enviar"
                @change="onCheckboxChange(campo)">
              <label class="form-check-label" :for="campo.name">
                {{ capitalizeFirstLetter(campo.name) }}
              </label>
              <component :is="getComponentType(campo.type)" v-model="campo.valor"
                :class="{ 'form-control': true, 'readonly': !campo.enviar }" :disabled="!campo.enviar"
                @input="onInputChange(campo, $event)" />
            </div>
          </li>
        </ul>

        <div style="text-align: center; margin-top: 20px;">
          <!-- Botón centrado -->
          <button type="button" @click="buscarAvanzado" class="btn btn-primary mt-3">Buscar</button>
        </div>

        <div v-if="mostrarResultados" class="mt-3">
          <h4 class="text-center">Resultados:</h4>
          <ul class="list-group">
            <li v-for="resultado in resultados" :key="resultado._id.$oid" class="list-group-item"
              :class="{ 'mouse-hovered': mouseSobreResultado === resultado }"
              @mouseenter="mouseSobreResultado = resultado" @mouseleave="mouseSobreResultado = null"
              @click="verDocumento(resultado)">
              <div v-for="(value, key) in resultado" :key="key">
                <span v-if="shouldShowKeyInResults(key)">
                  <strong>{{ key }}:</strong> {{ value }}
                </span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      colecciones: [],
      selectedColeccion: '',
      selectedCampos: [],
      resultados: [],
      mostrarResultados: false,
      mouseSobreResultado: null
    };
  },
  async mounted() {
    try {
      const response = await axios.get('/plantillas');
      this.colecciones = response.data.filter(coleccion => this.shouldShowCollection(coleccion));
    } catch (error) {
      console.error('Error al obtener las colecciones:', error);
    }
  },
  computed: {
    filteredColecciones() {
      return this.colecciones.filter(coleccion => this.shouldShowCollection(coleccion));
    }
  },
  methods: {
    async onColeccionChange() {
      if (this.selectedColeccion) {
        try {
          const response = await axios.get(`/plantillas/${this.selectedColeccion}/fields`);
          this.selectedCampos = response.data
            .filter(campo => this.shouldShowKey(campo.name) && campo.type !== 'file') // Filtrar campos de tipo 'file'
            .map(campo => ({
              name: campo.name,
              type: campo.type,
              enviar: false,
              valor: ''
            }));
        } catch (error) {
          console.error('Error al obtener campos de colección:', error);
          this.selectedCampos = [];
        }
      } else {
        this.selectedCampos = [];
      }
    },
    onCheckboxChange(campo) {
      console.log(`Checkbox cambiado: ${campo.name}, enviar: ${campo.enviar}`);
    },
    onInputChange(campo, event) {
      campo.valor = event.target.value;
    },
    async buscarAvanzado() {
      const camposParaEnviar = this.selectedCampos
        .filter(campo => campo.enviar)
        .map(campo => campo.valor);  // Crear un array con los valores de los campos seleccionados

      console.log('Datos enviados al controlador:', {
        nombre_coleccion: this.selectedColeccion,
        palabras_clave: camposParaEnviar
      });

      try {
        const response = await axios.post('/busqueda-avanzada', {
          nombre_coleccion: this.selectedColeccion,
          palabras_clave: camposParaEnviar
        });
        this.resultados = response.data;
        this.mostrarResultados = true;
      } catch (error) {
        console.error('Error al realizar la búsqueda avanzada:', error);
      }
    },
    capitalizeFirstLetter(value) {
      if (!value) return '';
      const stringValue = String(value);
      return stringValue.charAt(0).toUpperCase() + stringValue.slice(1);
    },
    getComponentType(type) {
      switch (type) {
        case 'text':
        case 'number':
          return 'input';
        case 'textarea':
          return 'textarea';
        // Añade más casos según necesites
        default:
          return 'input';
      }
    },
    shouldShowCollection(coleccion) {
      const excludedCollections = ['CarrouselImage', 'Comentario', 'NavbarColor', 'Noticia', 'User', 'PlantillaPredeterminada', 'plantillas_predeterminadas', 'users', 'comentarios', 'carrousel_images', 'visitas'];
      return !excludedCollections.includes(coleccion.nombre);
    },
    shouldShowKey(key) {
      const excludedKeys = ['_id', 'Recurso_Digital', 'created_at', 'updated_at', 'tipo_coleccion', 'Serial'];
      return !excludedKeys.includes(key);
    },
    shouldShowKeyInResults(key) {
      const excludedKeys = ['_id', 'Recurso Digital', 'created_at', 'updated_at', 'tipo_coleccion', 'Serial'];
      return !excludedKeys.includes(key);
    },
    verDocumento(resultado) {
      const urlParams = new URLSearchParams();
      urlParams.append('id', resultado._id.$oid);
      urlParams.append('nombre_plantilla', this.selectedColeccion);
      window.location.href = `/detallesdocumento?${urlParams.toString()}`;
    }
  }
};
</script>

<style scoped>
.title {
  text-align: center;
}
.mouse-hovered {
  background-color: #f0f0f0; /* Cambiar color de fondo cuando se hace hover */
  cursor: pointer;
}
</style>
