<template>
  <div class="container mt-3">
    <!-- Carrusel de Imágenes -->
    <div v-if="images.length > 0" id="carouselExample" class="carousel slide" data-ride="carousel" data-interval="5000">
      <div class="carousel-inner">
        <div v-for="(item, i) in images" :key="i" :class="{ 'active': i === 0 }" class="carousel-item">
          <!--<img :src="item.imagen" class="d-block w-100" alt="Slide Image" *@click="onImageClick(item)">-->
          <a :href="item.enlace" target="_blank" class="d-block w-100">
            <img :src="item.imagen" class="d-block w-100" alt="Slide Image">
          </a>
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

    <!-- Últimos Documentos -->
    <div v-if="Object.keys(ultimosDocumentos).length > 0" class="container mt-3">
      <h1 style="text-align: center;">Recursos subidos recientemente</h1>
      <div v-for="(coleccion, nombreColeccion) in ultimosDocumentos" :key="nombreColeccion">
        <h2>{{ coleccion.titulo || nombreColeccion }}</h2> <!-- Aquí usamos coleccion.titulo si está disponible -->
        <div class="row">
          <div v-for="(documento, index) in coleccion.documentos" :key="index" class="col-lg-3 col-md-4 col-sm-6 mt-3">
            <div class="card mb-3 same-height-card" @click="handleCardClick(documento._id, nombreColeccion, $event)">
              <div class="card-body d-flex flex-column">
                <div v-for="(value, key) in documento" :key="key">
                  <template
                    v-if="key !== '_id' && key !== 'created_at' && key !== 'updated_at' && key !== 'Recurso Digital' && key !== 'Serial'">
                    <p class="card-text mb-1 text-truncate">
                      <strong>{{ String(key).replace(/_/g, ' ') }}:</strong> {{ truncate(value, 80) }}
                    </p>

                  </template>
                </div>
                <!-- Contenedor de la miniatura -->
                <div class="mt-auto d-flex justify-content-center align-items-center">
                  <template v-if="documento['Recurso Digital'] && isArrayOfStrings(documento['Recurso Digital'])">
                    <template v-if="isImage(documento['Recurso Digital'][0])">
                      <img :src="getThumbnail(documento['Recurso Digital'][0])" class="card-img-top thumbnail"
                        alt="Miniatura">
                    </template>
                    <template v-else-if="isPdf(documento['Recurso Digital'][0])">
                      <img src="assets/icon-pdf.png" class="card-img-top thumbnail" alt="Pdf">
                    </template>
                    <template v-else-if="isAudio(documento['Recurso Digital'][0])">
                      <img src="assets/icon-audio.png" class="card-img-top thumbnail" alt="Audio">
                    </template>
                    <template v-else-if="isVideo(documento['Recurso Digital'][0])">
                      <img src="assets/icon-video.png" class="card-img-top thumbnail" alt="Video">
                    </template>
                  </template>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { onMounted, ref } from 'vue';
import { Fancybox } from "@fancyapps/ui";
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import Swal from 'sweetalert2';
import { useStore } from 'vuex'; // Importa el store de Vuex
import axios from 'axios';


export default {
  setup() {
    const images = ref([]);
    const store = useStore(); // Usa el store de Vuex
    const ultimosDocumentos = ref([]);
    const isAuthenticated = ref(false); // Estado de autenticación

    const getAllImagesForCarousel = () => {
      axios.get('/carrousel/all-images')
        .then(response => {
          console.log('Respuesta del servidor:', response.data);
          images.value = response.data.map(image => {
            // Construye la URL completa de la imagen para que se pueda mostrar correctamente
            return {
              ...image,
              imagen: `/storage/${image.imagen}`
            };
          });
        })
        .catch(error => {
          console.error('Error al obtener las imágenes del carrusel', error);
        });
    };

    const obtenerUltimosDocumentos = () => {
      axios.get('/ultimos-documentos')
        .then(response => {
          console.log('Ultimos documentos', response.data);
          ultimosDocumentos.value = response.data || [];
        })
        .catch(error => {
          console.error('Error al obtener los últimos documentos', error);
        });
    };

    const isArrayOfStrings = (value) => {
      return Array.isArray(value) && value.every(item => typeof item === 'string');
    };

    const isImage = (value) => {
      const extension = obtenerExtensionRecurso(value);
      const extensionesImagen = ['jpg', 'jpeg', 'png', 'gif'];
      return extensionesImagen.includes(extension.toLowerCase());
    };

    const isPdf = (value) => {
      const extension = obtenerExtensionRecurso(value);
      return extension.toLowerCase() === 'pdf';
    };

    const isAudio = (value) => {
      const extension = obtenerExtensionRecurso(value);
      const extensionesAudio = ['mp3', 'wav', 'ogg'];
      return extensionesAudio.includes(extension.toLowerCase());
    };

    const isVideo = (value) => {
      const extension = obtenerExtensionRecurso(value);
      const extensionesVideo = ['mp4', 'avi', 'mkv'];
      return extensionesVideo.includes(extension.toLowerCase());
    };

    const getThumbnail = (relativePath) => {
      const baseUrl = 'http://localhost:8000/storage';
      return `${baseUrl}/${encodeURIComponent(relativePath)}`;
    };

    const obtenerExtensionRecurso = (rutaRecurso) => {
      const ruta = Array.isArray(rutaRecurso) ? rutaRecurso[0] : rutaRecurso;
      if (ruta && typeof ruta === 'string') {
        const partesRuta = ruta.split('.');
        return partesRuta.length > 1 ? partesRuta[partesRuta.length - 1] : '';
      }
      return '';
    };

    const truncate = (value, length) => {
      if (typeof value === 'string') {
        return value.length > length ? value.slice(0, length) + '...' : value;
      }
      return value;
    };

    const openFancybox = (documento, value) => {
      if (Array.isArray(value) && value.length > 0) {
        const items = value.map((path, index) => ({
          src: getThumbnail(path),
          opts: {
            caption: `Previsualización de ${obtenerExtensionRecurso(path).toUpperCase()}`,
            contextMenu: false // Deshabilitar el menú contextual (clic derecho)
          }
        }));

        // Configuración de FancyBox
        const fancyboxOptions = {
          // Opciones adicionales de FancyBox
        };

        // Mostrar FancyBox con los items y las opciones configuradas
        Fancybox.show(items, fancyboxOptions);

        // Agregar un listener para bloquear el clic derecho
        document.addEventListener('contextmenu', blockContextMenu);
      } else {
        console.error('El valor pasado a openFancybox no es un array válido:', value);
      }
    };

    // Función para bloquear el menú contextual (clic derecho)
    const blockContextMenu = (event) => {
      event.preventDefault();
    };

    // Restaurar el comportamiento predeterminado del clic derecho después de cerrar FancyBox
    Fancybox.bind("[data-fancybox]", {
      afterClose: () => {
        document.removeEventListener('contextmenu', blockContextMenu);
      }
    });

    const handleCardClick = (documento, nombreColeccion, event) => {
      if (event && event.target) {
        if (event.target.classList.contains('thumbnail')) {
          // Si se hizo clic en la miniatura, abrir Fancybox
          const recursoDigital = documento && documento['Recurso Digital'];

          if (Array.isArray(recursoDigital) && recursoDigital.length > 0) {
            openFancybox(documento, recursoDigital);
          }
        } else {
          // Redirigir directamente a la página de detalles del documento
          let documentId = documento._id || documento.$oid || documento.id || (documento._id && documento._id.$oid);

          if (documentId) {
            const urlParams = new URLSearchParams();
            urlParams.append('id', documentId);
            urlParams.append('nombre_plantilla', nombreColeccion);
            window.location.href = `/detallesdocumento?${urlParams.toString()}`;
          } else {
            console.error('No se pudo obtener el ID del documento');
          }
        }
      } else {
        console.error('Evento de clic no válido:', event);
      }
    };




    const onImageClick = (item) => {
      const link = item.link || '/noticiaspublic';
      window.location.href = link;
    };

    onMounted(() => {
      getAllImagesForCarousel();
      obtenerUltimosDocumentos();

      Fancybox.bind("[data-fancybox]", {
        // Opciones de FancyBox aquí si es necesario
      });
    });

    return {
      images,
      ultimosDocumentos,
      isArrayOfStrings,
      isImage,
      isPdf,
      isAudio,
      isVideo,
      getThumbnail,
      truncate,
      openFancybox,
      handleCardClick,
      onImageClick

    };
  }
};
</script>



<style scoped>
.same-height-card {
  height: 100%;
  /* Ajusta la altura de la tarjeta según tus necesidades */
  display: flex;
  flex-direction: column;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  transition: box-shadow 0.3s ease;
}

.same-height-card:hover {
  box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
}

.card-body {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  background-color: #f7f6f5;
}

.card-img-top {
  height: 150px;
  /* Ajusta la altura deseada para las miniaturas */
  width: 100%;
  object-fit: cover;
  border-bottom: 1px solid #ddd;
}

.thumbnail {
  width: auto;
  /* Permite que la imagen mantenga su proporción */
  max-height: 150px;
  /* Ajusta la altura deseada para las miniaturas */
  object-fit: cover;
  border-radius: 8px;
}


.mt-auto {
  margin-top: auto;
  /* Empuja la miniatura hacia abajo */
}

/* Asegura que el texto se mantenga dentro de la tarjeta */
.card-text {
  word-wrap: break-word;
  overflow-wrap: break-word;
  white-space: pre-wrap;
  /* Ajusta para permitir que el texto se ajuste a nuevas líneas */
}

/* Asegura que el contenido no se salga horizontalmente */
.text-truncate {
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  /* Asegura que el texto se ajuste al ancho del contenedor */
}
</style>
