<template>
  <h1 class="text-center">NOTICIAS</h1>
  <div class="container">
    <nav class="text-center mb-3">
      <ul class="nav nav-pills justify-content-center">
        <li class="nav-item">
          <a class="nav-link" :class="{ active: selectedTab === 'pieza' }" @click="changeTab('pieza')">Pieza del Mes</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" :class="{ active: selectedTab === 'sabias' }" @click="changeTab('sabias')">¿Sabías
            Que?</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" :class="{ active: selectedTab === 'noticias' }"
            @click="changeTab('noticias')">Noticias</a>
        </li>
      </ul>
      <ul v-if="selectedTab === 'noticias'" class="nav nav-pills justify-content-center mt-2">
        <li class="nav-item" v-for="num in [1, 2, 3, 4, 5]" :key="num">
          <a class="nav-link title-link" :class="{ active: selectedNewsTab === num }" @click="changeNewsTab(num)">
            <span class="title-text">{{ truncateTitle(getNewsTitle(num)) }}</span>
          </a>
        </li>
      </ul>
    </nav> 
    <div class="mt-5" >
      <div v-if="selectedTab && selectedTabContent">
        <h2>{{ selectedTabContent.title }}</h2>
        <p class="text-muted" style="font-size: 0.9em;">
          {{ formatDate(selectedTabContent.date) }}
        </p>
        <img v-if="selectedTabContent.image" :src="'/storage/' + selectedTabContent.image"
          :style="{ width: selectedTabContent.imageWidth + 'px' }" alt="Image" class="img-fluid">
        <br>
        <br>
        <div class="preview-content">
          <p v-for="(paragraph, index) in getParagraphs(selectedTabContent.content)" :key="index">
            {{ paragraph }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const selectedTab = ref('pieza');
    const selectedNewsTab = ref(1);
    const newsContent = ref({});

    const fetchNews = async () => {
      try {
        const response = await axios.get('/obtenernoticias'); // Ajusta el endpoint según corresponda
        newsContent.value = response.data;
      } catch (error) {
        console.error('Error al obtener las noticias:', error);
      }
    };

    const changeTab = (tab) => {
      selectedTab.value = tab;
      if (tab !== 'noticias') {
        selectedNewsTab.value = 1;
      }
    };

    const changeNewsTab = (num_noticia) => {
      selectedNewsTab.value = num_noticia;
    };

    const getNewsTitle = (num) => {
      const key = `noticias_${num}`;
      const noticiasArray = newsContent.value[key];
      return noticiasArray && noticiasArray.length > 0 ? noticiasArray[0].title : 'Sin título';
    };

    const getParagraphs = (text) => {
      if (!text) return []; // Verificación para manejar casos de undefined o null
      return text.split(/\r?\n\r?\n/).filter(p => p.trim() !== '');
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'Fecha no disponible'; // Manejo del caso en que dateString sea undefined

      // Crear una fecha a partir del string YYYY-MM-DD sin zona horaria
      const [year, month, day] = dateString.split('-').map(Number);
      const date = new Date(year, month - 1, day); // Meses en JavaScript son 0-indexados

      return new Intl.DateTimeFormat('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }).format(date);
    };


    const truncateTitle = (title) => {
      const maxLength = 15; // Ajusta la longitud máxima según sea necesario
      return title.length > maxLength ? title.slice(0, maxLength) + '...' : title;
    };

    const selectedTabContent = computed(() => {
      if (selectedTab.value === 'noticias') {
        const noticiasKey = `noticias_${selectedNewsTab.value}`;
        const noticiasArray = Array.isArray(newsContent.value[noticiasKey]) ? newsContent.value[noticiasKey] : [];
        return noticiasArray.length > 0 ? noticiasArray[0] : {};
      }
      return Array.isArray(newsContent.value[selectedTab.value]) ? newsContent.value[selectedTab.value][0] : {};
    });

    onMounted(() => {
      fetchNews();
    });

    return {
      selectedTab,
      selectedNewsTab,
      newsContent,
      changeTab,
      changeNewsTab,
      getParagraphs,
      truncateTitle,
      selectedTabContent,
      getNewsTitle,
      formatDate,
    };
  },
};
</script>


<style scoped>
.nav-pills .nav-link.active {
  background-color: #007bff;
}

.title-link {
  display: block;
  position: relative;
  overflow: hidden;
  max-width: 150px;
  /* Ajusta el ancho según sea necesario */
  white-space: nowrap;
  text-overflow: ellipsis;
  transition: max-width 0.5s ease, padding 0.5s ease;
}

.title-text {
  display: inline-block;
}

.title-link:hover {
  max-width: 500px;
  /* Ajusta el ancho expandido según sea necesario */
  padding-right: 1rem;
  text-align: left;
}

.title-link:hover .title-text {
  white-space: normal;
  overflow: visible;
  display: block;
}

.preview-content {
  text-align: justify;
}

.preview-content p {
  margin: 0 0 10px;
}
</style>
