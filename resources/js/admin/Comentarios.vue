<template>
    <div>
      <div class="container mt-4">
        <label for="vista" class="form-label">Seleccionar vista:</label>
        <select id="vista" class="form-select" v-model="vistaSeleccionada" @change="obtenerComentarios()" style="width: 300px;"> 
            <option value="pendientes">Comentarios Pendientes</option>
          <option value="aprobados">Comentarios Aprobados</option>
        </select>
  
        <div class="mt-4">
          <div v-if="vistaSeleccionada === 'pendientes'">
            <h3 class="mb-3">Comentarios Pendientes</h3>
            <div v-for="comentario in comentariosPendientes" :key="comentario._id" class="card mb-3">
              <div class="card-body">
                <p class="card-text"><strong>Nombre del usuario:</strong> {{ comentario.usuario.nombre }} {{ comentario.usuario.apellido_paterno }} {{ comentario.usuario.apellido_materno }}</p>
                <p class="card-text"><strong>Contenido del comentario:</strong> {{ comentario.contenido }}</p>
   
                <div v-for="(value, key) in comentario.documento" :key="key">
                   <!-- Excluir campos específicos -->
                  <template v-if="key !== 'created_at' && key !== 'updated_at' && key !== '_id' && key !== 'Recurso Digital'">
                    <p class="card-text"><strong>{{ key }}</strong>: {{ value }}</p>
                  </template>
                </div>
  
                <button class="btn btn-success btn-sm me-2" @click="aprobarComentario(comentario._id)"><i class="fas fa-check" style="color: #ffffff;"></i></button>
                <button class="btn btn-danger btn-sm" @click="denegarComentario(comentario._id)"><i class="fas fa-minus-circle" style="color: #ffffff;"></i></button>
              </div>
            </div>
          </div>
  
          <div v-if="vistaSeleccionada === 'aprobados'" class="mt-4">
            <h3 class="mb-3">Comentarios Aprobados</h3>
            <div v-for="comentario in comentariosAprobados" :key="comentario._id" class="card mb-3">
              <div class="card-body">
                <p class="card-text"><strong>Nombre del usuario:</strong> {{ comentario.usuario.nombre }} {{ comentario.usuario.apellido_paterno }} {{ comentario.usuario.apellido_materno }}</p>
                <p class="card-text"><strong>Contenido del comentario:</strong> {{ comentario.contenido }}</p>
  
                <div v-for="(value, key) in comentario.documento" :key="key">
                  <!-- Excluir campos específicos -->
                  <template v-if="key !== 'created_at' && key !== 'updated_at' && key !== '_id' && key !== 'Recurso Digital'">
                    <p class="card-text"><strong>{{ key }}</strong>: {{ value }}</p>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  

  



<script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
    name: 'Comentarios',
    data() {
        return {
            comentariosPendientes: [],
            comentariosAprobados: [],
            vistaSeleccionada: 'pendientes', // Inicialmente mostramos los comentarios pendientes
        };
    },
    methods: {
        async obtenerComentarios() {
            try {
                if (this.vistaSeleccionada === 'pendientes') {
                    const response = await axios.get('/comentarios/pendientes');
                    this.comentariosPendientes = response.data.comentariosPendientes;
                } else if (this.vistaSeleccionada === 'aprobados') {
                    const response = await axios.get('/comentarios/aprobados');
                    this.comentariosAprobados = response.data.comentariosAprobados;
                }
            } catch (error) {
                console.error('Error al obtener comentarios:', error);
                // Puedes manejar el error aquí, por ejemplo, mostrar un mensaje al usuario
            }
        },
        async aprobarComentario(comentarioId) {
            try {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'No podrás revertir esto.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aprobar comentario'
                });

                if (result.isConfirmed) {
                    await axios.put(`/comentarios/aprobar/${comentarioId}`);
                    await this.obtenerComentarios();
                    Swal.fire({
                        title: 'Aprobado',
                        text: 'El comentario ha sido aprobado.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                    });
                }
            } catch (error) {
                console.error('Error al aprobar comentario:', error);
                // Puedes manejar el error aquí, por ejemplo, mostrar un mensaje al usuario
            }
        },
        async denegarComentario(comentarioId) {
            try {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'No podrás revertir esto.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, denegar comentario'
                });

                if (result.isConfirmed) {
                    await axios.delete(`/comentarios/denegar/${comentarioId}`);
                    await this.obtenerComentarios();
                    Swal.fire({
                        title: 'Denegado',
                        text: 'El comentario ha sido denegado y eliminado con éxito.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                    });
                }
            } catch (error) {
                console.error('Error al denegar comentario:', error);
                // Puedes manejar el error aquí, por ejemplo, mostrar un mensaje al usuario
            }
        },
    },
    async mounted() {
        await this.obtenerComentarios();
    }
};
</script>

<style scoped>
.card {
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card-body {
    padding: 1.25rem;
}

.btn {
    font-size: 0.875rem;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.form-select {
    width: 200px;
}
</style>