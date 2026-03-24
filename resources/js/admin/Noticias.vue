<template>
  <div class="container">
    <h1 class="text-center">NOTICIAS ADMINISTRATIVO</h1>
    <nav class="text-center mb-3">
      <ul class="nav nav-pills justify-content-center">
        <li class="nav-item" v-for="tab in tabs" :key="tab.value">
          <a class="nav-link" :class="{ active: selectedTab === tab.value }" @click="selectTab(tab.value)">
            {{ tab.label }}
          </a>
        </li>
      </ul>
    </nav>
    <!-- Vista previa -->
    <div class="mt-5">
      <h1 class="text-center">Vista Previa</h1>
      <div v-for="tab in tabs" v-show="selectedTab === tab.value" :key="tab.value" class="text-center">
        <h2>{{ tabContent[selectedTab].title }}</h2>
        <img :src="tabContent[selectedTab].image" :style="{ width: tabContent[selectedTab].imageWidth + 'px' }"
          alt="Image" class="img-fluid">
        <div class="preview-content">
          <p>{{ tabContent[selectedTab].date }}</p>
          <p v-for="(paragraph, index) in getParagraphs(tabContent[selectedTab].content)" :key="index">
            {{ paragraph }}
           </p>
        </div>
      </div>
    </div>
    <div class="mt-5">
       <div v-for="tab in tabs" v-show="selectedTab === tab.value" :key="tab.value">      
        <h2>{{ tab.label }}</h2>
        <div class="mb-3">
          <label for="title">Título</label>
          <input type="text" id="title" v-model="tabContent[selectedTab].title" class="form-control">
        </div>
        <div class="mb-3">
          <label for="date">Fecha</label>
          <input type="date" id="date" v-model="tabContent[selectedTab].date" class="form-control">
        </div>
        <div class="mb-3"> 
          <label for="image">Imagen</label> 
          <input type="file" id="image" @change="onImageChange" class="form-control">
        </div>
        <div class="mb-3">
          <label for="imageWidth">Tamaño de la Imagen (px)</label>
          <input type="number" id="imageWidth" v-model.number="tabContent[selectedTab].imageWidth" class="form-control">
        </div>
        <div class="mb-3" v-if="selectedTab === 'noticias'">
          <label for="num_noticia">Número de Noticia</label>
          <select id="num_noticia" v-model.number="tabContent[selectedTab].num_noticia" class="form-control">
            <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="content">Contenido</label>
          <textarea id="content" v-model="tabContent[selectedTab].content" class="form-control"></textarea>
        </div>
        <button class="btn btn-primary mt-3" @click="saveContent">Guardar</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const selectedTab = ref('pieza');
    const tabs = ref([
      { value: 'pieza', label: 'Pieza del Mes' },
      { value: 'sabias', label: '¿Sabías Que?' },
      { value: 'noticias', label: 'Noticias' },
    ]);

    const tabContent = ref({
      pieza: { title: '', date: '', image: '', content: '', imageWidth: 300 },
      sabias: { title: '', date: '', image: '', content: '', imageWidth: 300 },
      noticias: { title: '', date: '', image: '', content: '', imageWidth: 300, num_noticia: 1 },
    });

    const selectTab = (tab) => {
      selectedTab.value = tab;
    };

    const onImageChange = (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          tabContent.value[selectedTab.value].image = e.target.result;
          tabContent.value[selectedTab.value].imageFile = file; // Guarda el archivo para enviarlo
        };
        reader.readAsDataURL(file);
      } else {
        tabContent.value[selectedTab.value].image = ''; // Limpiar la im agen si no hay archivo
      }
    };

    const saveContent = async () => {
      const content = tabContent.value[selectedTab.value];
      const formData = new FormData(); 
      formData.append('tab', selectedTab.value);
      formData.append('title', content.title);
      formData.append('date', content.date);
      formData.append('content', content.content);
      formData.append('imageWidth', content.imageWidth);

      if (content.imageFile) {
        formData.append('image', content.imageFile);
      }

      // Solo añadir num_noticia si es la sección 'noticias'
      if (selectedTab.value === 'noticias' && content.num_noticia) {
        formData.append('num_noticia', content.num_noticia);
      }

      try {
        await axios.post('/guardarnoticias', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        alert('Contenido guardado');
      } catch (error) {
        console.error('Error al guardar el contenido:', error);
        alert('Hubo un error al guardar el contenido');
      }
    };

    const getParagraphs = (text) => {
      // Split the content into paragraphs based on line breaks
      return text.split(/\n\n+/).filter(p => p.trim() !== '');
    };

    return {
      selectedTab,
      tabs,
      tabContent,
      selectTab,
      onImageChange,
      saveContent,
      getParagraphs,
    };
  },
};
</script>

<style scoped>
.nav-pills .nav-link.active {
  background-color: #007bff;
}

.preview-content {
  text-align: justify;
}

.preview-content p {
  margin: 0 0 10px;
}
</style>
