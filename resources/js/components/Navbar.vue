  <template>
    <nav class="navbar navbar-expand-lg" :style="{ backgroundColor: navbarColor }">
      <div class="container-fluid">
        <a class="navbar-brand" href="/home">
          <img class="navbar-logo2" :src="secondaryLogoSrc" alt="Logo Secundario">
          <img class="navbar-logo" :src="logoSrc" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link btn-custom" href="/elegirbusqueda">
                <div class="nav-icon">
                  <i class="fas fa-search"></i> <!-- Cambia la clase del ícono según el ícono que desees usar -->
                </div>
                Búsqueda avanzada
              </a>
            </li>
            <!--
            <li class="nav-item">
              <a class="nav-link btn-custom" href="/noticiaspublic">Noticias</a>
            </li> 
          -->
          </ul>
          <form class="d-flex" @submit.prevent="buscar">
            <input v-model="palabraClave" class="form-control me-2" type="search" placeholder="Buscar"
              aria-label="Search">
            <button class="btn btn-search" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </form>
          <ul class="navbar-nav ms-auto">
            <li v-if="!isAuthenticated" class="nav-item">
              <a class="btn btn-login mr-2" href="/loginUser">Iniciar Sesión</a>
            </li>
            <li v-if="!isAuthenticated" class="nav-item">
              <a class="btn btn-register" href="/register">Registrarse</a>
            </li>
            <li v-if="isAuthenticated" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                Bienvenido, {{ user.nombre }}
              </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="/perfil">Perfil</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" @click="logout">Cerrar sesión</a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Modal -->
    <div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="searchResultsModalLabel">Resultados de la búsqueda</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="resultados.length">
              <ul class="list-group">
                <li v-for="(resultado, index) in resultados" :key="index" class="list-group-item list-group-item-action"
                  @click="verDocumento(resultado)">
                  <template v-for="(value, key) in resultado">
                    <template v-if="!shouldExcludeField(key)">
                      <div>
                        <strong>{{ key }}:</strong> {{ value }}
                      </div>
                    </template>
                  </template>
                </li>
              </ul>
            </div>
            <div v-else>
              No se encontraron resultados.
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

<script>
import axios from 'axios';
import { mapGetters, mapActions } from 'vuex';

export default {
  data() {
    return {
      navbarColor: '#FFFFFF',
      palabraClave: '',
      logoSrc: '/assets/AGQROO-1024x576.png',
      secondaryLogoSrc: '/assets/ESCUDO_final-1024x576.png', // Ruta al segundo logo
      resultados: [],
      selectedColeccion: '' // Añade esta línea para almacenar la colección seleccionada
    };
  },
  computed: {
    ...mapGetters(['isAuthenticated', 'getUser']),
    user() {
      return this.getUser;
    }
  },
  methods: {
    ...mapActions(['logout']),
    buscar() {
      axios.post('/search', { palabra_clave: this.palabraClave })
        .then(response => {
          this.resultados = response.data;
          const modal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
          modal.show();
        })
        .catch(error => {
          console.error('Error al realizar la búsqueda:', error);
        });
    },
    verDocumento(resultado) {
      const id = resultado._id && resultado._id.$oid ? resultado._id.$oid : resultado._id;
      if (id && resultado.tipo_coleccion) {
        const urlParams = new URLSearchParams();
        urlParams.append('id', id);
        urlParams.append('nombre_plantilla', resultado.tipo_coleccion);
        window.location.href = `/detallesdocumento?${urlParams.toString()}`;
      } else {
        console.error('El documento no tiene un _id válido o tipo_coleccion:', resultado);
      }
    },
    fetchNavbarColor() {
      axios.get('/get-navbar-color')
        .then(response => {
          this.navbarColor = response.data.color;
        })
        .catch(error => {
          console.error('Error al obtener el color del navbar:', error);
        });
    },
    vistaPrevia() {
      this.navbarColor = this.selectedColor;
    },
    confirmarCambios() {
      axios.post('/cambiar-color-navbar', { color: this.navbarColor })
        .then(response => {
          console.log('Color cambiado correctamente:', this.navbarColor);
        })
        .catch(error => {
          console.error('Error al cambiar el color del navbar:', error);
        });
    },
    shouldExcludeField(key) {
      const excludedFields = ['_id', 'Recurso Digital', 'created_at', 'updated_at', 'tipo_coleccion'];
      return excludedFields.includes(key);
    }
  },
  created() {
    this.fetchNavbarColor();
  }
};
</script>

<style scoped>
.form-control {
  width: 300px;
}

.btn-login {
  margin-left: 200px;
}

.modal-lg {
  max-width: 80%;
}

.list-group-item {
  cursor: pointer;
  transition: background-color 0.2s;
  padding: 10px 15px;
  /* Ajuste de padding para separación */
  margin-bottom: 10px;
  /* Espacio entre elementos */
}

.list-group-item:hover {
  background-color: #e4e4e4;
}

.list-group-item div {
  margin-bottom: 5px;
  /* Espacio entre líneas de texto */
}

.navbar-logo {
  height: 150px;
  /* Ajusta el tamaño según lo necesario */
  width: auto;
  margin: 0;
  /* Elimina márgenes para que los logos estén pegados */
}

.navbar-logo2 {
  height: 100px;
  /* Ajusta el tamaño según lo necesario */
  width: auto;
  margin: 0;
  /* Elimina márgenes para que los logos estén pegados */
}

/* Asegúrate de que los logos estén alineados horizontalmente */
.navbar-brand {
  display: flex;
  align-items: center;
  /* Centra verticalmente los logos */
}



.nav-link, .nav-text {
  font-family: 'Montserrat', sans-serif; /* Aplica la fuente Montserrat */
}
.nav-link {
  display: flex;
  flex-direction: column; /* Alinea el contenido en columna (ícono arriba y texto abajo) */
  align-items: center; /* Centra el contenido horizontalmente */
  text-align: center; /* Centra el texto debajo del ícono */
  padding: 10px; /* Ajusta el padding según sea necesario */
}

.nav-icon {
  font-size: 24px; /* Tamaño del ícono, ajusta según sea necesario */
  margin-bottom: 5px; /* Espacio entre el ícono y el texto */
  display: flex;
  justify-content: center; /* Centra horizontalmente el ícono dentro del contenedor */
  align-items: center; /* Centra verticalmente el ícono dentro del contenedor */
}

.btn-custom {
  padding: 10px;
  color: #000; /* Color del texto del botón */
  text-decoration: none; /* Elimina el subrayado del enlace */
  display: flex;
  flex-direction: column; /* Alinea el contenido en columna */
  align-items: center; /* Centra horizontalmente todo el contenido */
}

.nav-link:hover .nav-icon {
  color: #ac123f; /* Cambia el color del ícono al pasar el ratón sobre el enlace */
}

/* Botón de iniciar sesión */
.btn-login {
  background-color: #ac123f; /* Verde sólido */
  color: white; /* Texto blanco */
  border-radius: 10px; /* Bordes redondeados */
  padding: 10px 20px; /* Espaciado interno */
  border: none; /* Elimina el borde por defecto */
  transition: background-color 0.3s ease; /* Transición suave al pasar el ratón */
}

.btn-login:hover {
  background-color: #530e0e; /* Color más oscuro al hacer hover */
}

/* Botón de registrarse */
.btn-register {
  background-color: #ac123f; /* Azul sólido */
  color: white; /* Texto blanco */
  border-radius: 10px; /* Bordes redondeados */
  padding: 10px 20px; /* Espaciado interno */
  border: none; /* Elimina el borde por defecto */
  transition: background-color 0.3s ease; /* Transición suave al pasar el ratón */
}

.btn-register:hover {
  background-color: #530e0e; /* Color más oscuro al hacer hover */
}

.btn-search {
  background-color: #3d3d3d; /* Verde sólido */
  color: rgb(255, 255, 255); /* Texto blanco */
  border-radius: 10px; /* Bordes redondeados */
  border: none; /* Elimina el borde por defecto */
  transition: background-color 0.3s ease; /* Transición suave al pasar el ratón */
}

.btn-search:hover {
  background-color: #ac123f; /* Color más oscuro al hacer hover */

}

.navbar {
  padding-top: 0px; /* Ajusta el padding superior */
  padding-bottom: 0px; /* Ajusta el padding inferior */
  border-bottom: 4px solid #a48830; /* Línea de color debajo del navbar */
}
</style>
