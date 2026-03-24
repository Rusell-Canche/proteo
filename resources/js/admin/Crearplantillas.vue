<template>
  <div class="container mt-4">
    <label>Nombre de la Plantilla</label>
    <input v-model="plantillaName" class="form-control mb-2" />

    <h3>Campos de la Plantilla:</h3>
    <div v-for="(campo, index) in camposPlantilla" :key="index" class="campo mb-3">

      <button type="button" @click="quitarCampo(index)" class="btn btn-danger btn-sm campo-boton-quitar"><i class="fas fa-minus-circle" style="color: #6b0505;"></i></button>

      <label>Nombre del Campo</label>
      <input v-model="campo.name" class="form-control mb-2" />

      <label>Tipo del Campo</label>
      <select v-model="campo.type" class="form-control mb-2">
        <option value="string">String</option>
        <option value="number">Number</option>
        <option value="file">File</option>
        <option value="date">Date</option>
      </select>

      <div class="form-check mb-2">
        <input type="checkbox" class="form-check-input" v-model="campo.required" />
        <label class="form-check-label">Campo Obligatorio</label>
      </div>

    </div>

    <div class="button-group">
      <button type="button" @click="agregarCampo" class="btn btn-success me-2">Agregar Campo</button>
      <button type="button" @click="crearPlantilla" class="btn btn-primary">Crear Plantilla</button>
    </div>

    <div v-if="mensaje" class="alert alert-success mt-3">{{ mensaje }}</div>
    <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      plantillaName: '',
      camposPlantilla: [
        { name: '', type: 'string', required: false }
      ],
      mensaje: '',
      error: ''
    };
  },
  methods: {
    agregarCampo() {
      this.camposPlantilla.push({ name: '', type: 'string', required: false });
    },
    quitarCampo(index) {
      this.camposPlantilla.splice(index, 1);
    },
    async crearPlantilla() {
      try {
        const response = await axios.post('/plantillas', {
          plantilla_name: this.plantillaName,
          fields: this.camposPlantilla
        });
        this.mensaje = response.data.message;
        this.error = '';
        // Limpiar el formulario
        this.plantillaName = '';
        this.camposPlantilla = [{ name: '', type: 'string', required: false }];
      } catch (error) {
        if (error.response) {
          this.error = error.response.data.error || 'Error al crear la plantilla.';
        } else {
          this.error = 'Error de red o servidor.';
        }
        this.mensaje = '';
      }
    }
  }
};
</script>

<style scoped>
.campo {
  border: 1px solid #ddd;
  padding: 15px;
  border-radius: 5px;
  position: relative; /* Esto permite posicionar el botón en la parte superior derecha */
}

.campo-boton-quitar {
  position: absolute;
  top: 5px;
  right: 5px;
  font-size: 18px;
  padding: 2px 5px;
  background-color: #ffffff; /* Color rojo */
  color: white;
  border: none;
  cursor: pointer;
}

.campo-boton-quitar:hover {
  background-color: rgb(224, 171, 171); /* Color rojo oscuro al pasar el cursor */
}
.button-group {
  display: flex;
}
</style>
