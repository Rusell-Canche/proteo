<template>
  <div class="container mt-4">
    <!-- Card de Recursos Digitales (arriba, ancho completo) -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card bg-light w-100">
          <div class="card-body">
            <div v-if="documento">
              <div class="contenedor-recursos" v-html="renderizarRecursoDigital(buscarRecursosDigitales(documento))"
                style="max-width: 100%; max-height: 600px; overflow-y: auto; overflow-x: hidden;"></div>
            </div>
            <div v-else>
              <p>Cargando imágenes...</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card de Detalles del Documento (abajo) -->
    <div class="row">
      <div class="col-12">
        <div class="card bg-light w-100">
          <div class="card-body">
            <h2>Detalles del documento</h2>
            <div v-if="documento">
              <div v-for="(value, key) in documento" :key="key" class="mt-3">
                <p v-if="!esArrayYContieneRecursoDigital(value) && !esRecursoDigital(value) &&
                  key !== '_id' && key !== 'created_at' && key !== 'updated_at' && key !== 'Serial'">
                  <strong>{{ key.replace(/_/g, ' ') }}:</strong>
                  <span>{{ value }}</span>
                </p>
              </div>
              <button v-if="isAuthenticated" @click="descargarConMarcaAgua(documento)" class="btn btn-success mt-3">
                Descargar Recurso
              </button>
            </div>
            <div v-else>
              <p>Cargando detalles del documento...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>



<script setup>
import { ref, onMounted, nextTick, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { Fancybox } from "@fancyapps/ui";
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import { useStore } from 'vuex'; // Importa el store de Vuex

const store = useStore(); // Usa el store de Vuex
const documento = ref(null);
const documentId = new URLSearchParams(window.location.search).get('id');
const plantillaName = new URLSearchParams(window.location.search).get('nombre_plantilla');
const comentario = ref('');
const comentariosAprobados = ref([]);

const isAuthenticated = computed(() => store.getters.isAuthenticated); // Computed para verificar autenticación


const obtenerDetallesDocumento = async () => {
  try {
    const response = await axios.get(`/documentos/${plantillaName}/${documentId}`);
    if (response.data) {
      documento.value = response.data;
      nextTick(() => {
        inicializarFancybox(); // Inicializar Fancybox después de que el DOM se haya actualizado
      });
    } else {
      console.error('Respuesta inesperada al obtener los detalles del documento', response);
    }
  } catch (error) {
    console.error('Error al obtener los detalles del documento', error);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';

  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('es-ES', options);
};

const esRecursoDigital = (url) => {
  if (typeof url !== 'string') return false;
  const extension = obtenerExtension(url);
  return ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'pdf', 'mp3', 'wav'].includes(extension);
};

const esArrayYContieneRecursoDigital = (value) => {
  return Array.isArray(value) && value.some(val => esRecursoDigital(val));
};

const buscarRecursosDigitales = (documento) => {
  let recursos = [];
  for (let key in documento) {
    if (Array.isArray(documento[key])) {
      recursos = recursos.concat(documento[key].filter(esRecursoDigital));
    } else if (typeof documento[key] === 'string' && esRecursoDigital(documento[key])) {
      recursos.push(documento[key]);
    }
  }
  return recursos;
};

const renderizarRecursoDigital = (urls) => {
  if (!urls || urls.length === 0) return '';

  const contenido = urls.map((url, index) => {
    const urlCorregida = corregirUrl(url);
    const extension = obtenerExtension(urlCorregida);
    const tipo = obtenerTipoRecurso(extension);

    if (index === 0 && tipo === 'Imagen') {
      // Imagen principal más grande
      return `<div class="contenedor-recurso-principal">
                <a href="${urlCorregida}" class="fancybox" data-fancybox="gallery" data-caption="Imagen ${index + 1}">
                  <img src="${urlCorregida}" alt="Imagen ${index + 1}" class="imagen-principal">
                </a>
              </div>`;
    }

    switch (tipo) {
      case 'Imagen':
        return `<div class="contenedor-recurso-miniatura">
                  <a href="${urlCorregida}" class="fancybox" data-fancybox="gallery" data-caption="Imagen ${index + 1}">
                    <img src="${urlCorregida}" alt="Imagen ${index + 1}" class="miniatura">
                  </a>
                </div>`;
      case 'Video':
        return `<div class="contenedor-recurso-video">
                  <a href="${urlCorregida}" class="fancybox" data-fancybox="gallery" data-caption="Video ${index + 1}">
                    <video controls  controlsList="nodownload" class="fancy-video">
                      <source src="${urlCorregida}" type="video/${extension}">
                      Tu navegador no soporta la etiqueta de video.
                    </video>
                  </a>
                </div>`;
      case 'PDF':
        return `<div class="contenedor-recurso-pdf">
            <iframe src="${urlCorregida}" type="application/pdf" class="fancy-pdf"></iframe>
          </div>`;

      case 'Audio':
        return `<div class="contenedor-recurso-audio">
                  <a href="${urlCorregida}" class="fancybox"  controlsList="nodownload" data-fancybox="gallery" data-caption="Audio ${index + 1}">
                    <audio controls class="fancy-audio">
                      <source src="${urlCorregida}" type="audio/${extension}">
                      Tu navegador no soporta la etiqueta de audio.
                    </audio>
                  </a>
                </div>`;
      default:
        return `<p>Recurso no soportado: ${urlCorregida}</p>`;
    }
  });

  // Agrupar la imagen principal y las miniaturas
  return contenido.slice(0, 1).concat(`<div class="miniaturas">${contenido.slice(1).join('')}</div>`).join('');
};

const corregirUrl = (url) => {
  // Asegurar que la URL comience con "/storage/"
  if (typeof url === 'string' && !url.startsWith('/storage/')) {
    url = `/storage/${url}`;
  }
  return url;
};

const obtenerExtension = (url) => {
  if (typeof url !== 'string') return 'desconocido';
  // Obtener la extensión del archivo
  const match = url.match(/\.(jpg|jpeg|png|gif|mp4|avi|mov|pdf|mp3|wav)$/i);
  if (match) {
    return match[1].toLowerCase(); // Retorna la extensión (en minúsculas)
  }
  return 'desconocido';
};

const obtenerTipoRecurso = (extension) => {
  switch (extension) {
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'gif':
      return 'Imagen';
    case 'mp4':
    case 'avi':
    case 'mov':
      return 'Video';
    case 'pdf':
      return 'PDF';
    case 'mp3':
    case 'wav':
      return 'Audio';
    default:
      return 'Desconocido';
  }
};

const inicializarFancybox = () => {
  Fancybox.bind("[data-fancybox]", {});
  // Deshabilitar el clic derecho en los elementos de Fancybox
  document.querySelectorAll("[data-fancybox]").forEach(element => {
    element.addEventListener('contextmenu', (event) => {
      event.preventDefault();
    });
  });
};

/*
const enviarComentario = () => {
  if (!store.getters.isAuthenticated) {
    Swal.fire({
      icon: 'warning',
      title: 'No estas autenticado',
      text: 'Por favor, inicia sesión para enviar un comentario.',
      confirmButtonText: 'Aceptar'
    });
    return;
  }

  const nuevoComentario = {
    contenido: comentario.value,
    documento_id: documentId,
    usuario_id: store.getters.getUser._id, // Aquí se obtiene el ID del usuario autenticado desde el store
  };

  axios.post('/comentarios', nuevoComentario)
    .then(response => {
      Swal.fire({
        icon: 'success',
        title: 'Comentario enviado',
        text: 'Tu comentario ha sido enviado y está pendiente de aprobación.',
        confirmButtonText: 'Aceptar'
      });
      comentario.value = '';
      obtenerComentarios(); // Volver a cargar los comentarios aprobados
    })
    .catch(error => {
      console.error('Error al enviar el comentario', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Hubo un problema al enviar tu comentario. Por favor, intenta nuevamente.',
        confirmButtonText: 'Aceptar'
      });
    });
};


const obtenerComentarios = async () => {
  try {
    const response = await axios.get(`/comentarios/documento/${documentId}`);
    if (response.data) {
      comentariosAprobados.value = response.data.comentariosAprobados; // Asigna la lista de comentarios
      console.log('Comentarios cargados:', comentariosAprobados.value);
    } else {
      console.error('Respuesta inesperada al obtener los comentarios', response);
    }
  } catch (error) {
    console.error('Error al obtener los comentarios', error);
  }
};
*/
const descargarConMarcaAgua = async (documento) => {
  try {
    const plantillaName = new URLSearchParams(window.location.search).get('nombre_plantilla');
    const documentId = documento._id.$oid || documento._id; // Asegúrate de obtener el ID correcto

    console.log('Nombre de la plantilla:', plantillaName);
    console.log('Id del documento:', documentId);

    if (!documentId) {
      console.error('No se encontró el ID del documento.');
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se encontró el ID del documento.',
      });
      return;
    }

    // Construye la URL de descarga
    const urlDescarga = `http://localhost:8000/watermark/${plantillaName}/${documentId}`;

    // Realiza la solicitud de descarga
    const response = await axios.get(urlDescarga, {
      responseType: 'blob'
    });

    // Verificar el Blob antes de descargarlo
    console.log('Tamaño del Blob:', response.data.size);
    console.log('Tipo MIME del Blob:', response.data.type);
    console.log('Primeros 100 bytes del Blob:', response.data.slice(0, 100));

    // Verificar el tipo MIME
    if (response.data.type !== 'application/zip') {
      console.error('El tipo MIME recibido no es application/zip.');
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'El archivo descargado no es un archivo ZIP válido.',
      });
      return;
    }

    // Crea un objeto Blob y lo descarga como un archivo
    const blob = new Blob([response.data], { type: 'application/zip' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'recursos_digitales_marca_de_agua.zip';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    Swal.fire({
      icon: 'success',
      title: 'Descarga iniciada',
      text: 'Tu descarga con marca de agua está en progreso.',
    });
  } catch (error) {
    console.error('Error al iniciar la descarga con marca de agua', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Ocurrió un error al iniciar la descarga con marca de agua.',
    });
  }
};




onMounted(() => {
  obtenerDetallesDocumento();
  obtenerComentarios();

});
</script>

<style>
.imagen-principal {
  max-width: 100%;
  max-height: 600px;
  object-fit: cover;
  border-radius: 8px;
  margin: 5px auto;
  display: block;
}

.miniatura {
  max-width: 100%;
  height: auto;
  object-fit: cover;
  border-radius: 8px;
  margin: 5px;
}

.contenedor-imagenes {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
  align-items: center;
}

.contenedor-recurso-principal,
.contenedor-recurso-miniatura,
.contenedor-recurso-video,
.contenedor-recurso-audio,
.contenedor-recurso-pdf {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
  text-align: center;
}

.contenedor-recurso-principal {
  max-width: 600px;
  max-height: 600px;
}

.contenedor-recurso-miniatura {
  max-width: 100px;
  max-height: 100px;
}

.contenedor-recurso-video {
  max-width: 400px;
  max-height: 400px;
}

.contenedor-recurso-audio {
  max-width: 300px;
  max-height: 300px;
}

.contenedor-recurso-pdf {
  width: 70%;
  height: 700px;
  /* o lo que quieras como altura fija */
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  margin: 0 auto;
  padding: 0;
}




.fancy-video,
.fancy-audio {
  width: 100%;
  height: 100%;
}

.fancy-pdf {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border: none;
}


.fancy-video {
  height: auto;
}

.fancy-audio {
  height: auto;
}

.contenedor-recursos {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  text-align: center;
}

.contenedor-recursos .miniaturas {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
  align-items: center;
}
</style>
