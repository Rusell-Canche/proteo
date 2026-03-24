<template>
  <div>
    <h1>Subir PDF para extraer texto</h1>
    <input type="file" @change="subirPDF" />
    <pre>{{ texto }}</pre>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      texto: ''
    }
  },
  methods: {
    async subirPDF(event) {
      const archivo = event.target.files[0];
      const formData = new FormData();
      formData.append('pdf', archivo);

      const { data } = await axios.post('/procesar-pdf', formData);
      this.texto = data.texto_extraido;
    }
  }
}
</script>
