<template>
  <div class="user-list">
    <div v-for="user in users" :key="user._id" class="user-card">
      <div class="user-info">
        <h4>{{ user.nombre }} {{ user.apellido_paterno }} {{ user.apellido_materno }}</h4>
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p><strong>Roles:</strong></p>
        <ul class="role-list">
          <li v-for="role in user.roles" :key="role" class="role">{{ role }}</li>
        </ul>
        <div class="button-group">
          <button @click="openEditModal(user)" class="btn-edit" data-toggle="modal" data-target="#editModal">
            <i class="fas fa-edit"></i> <!-- Icono de editar -->
          </button>
          <button @click="confirmDelete(user)" class="btn-delete">
            <i class="fas fa-minus-circle"></i> <!-- Icono de eliminar -->
          </button>
        </div>
      </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="confirmUpdate">
              <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input v-model="editedUser.nombre" type="text" id="nombre" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="apellido_paterno">Apellido Paterno:</label>
                <input v-model="editedUser.apellido_paterno" type="text" id="apellido_paterno" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="apellido_materno">Apellido Materno:</label>
                <input v-model="editedUser.apellido_materno" type="text" id="apellido_materno" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input v-model="editedUser.email" type="email" id="email" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="password">Contraseña:</label>
                <input v-model="editedUser.password" type="password" id="password" class="form-control" placeholder="••••••••">
              </div>
              <div class="form-group">
                <label>Roles:</label>
                <div class="form-check" v-for="role in availableRoles" :key="role">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    :value="role"
                    v-model="editedUser.roles"
                    :id="'edit-' + role"
                  >
                  <label class="form-check-label" :for="'edit-' + role">{{ role }}</label>
                </div>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </div>
            </form>
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
  data() {
    return {
      users: [], // Aquí almacenarás los datos de los usuarios
      editedUser: {
        _id: '',
        nombre: '',
        apellido_paterno: '',
        apellido_materno: '',
        email: '',
        password: '',
        roles: []
      },
      availableRoles: ['administrador', 'capturista', 'validador', 'plantillas', 'carrusel', 'onepiece'],
      originalUser: null // Para almacenar la copia original del usuario
    };
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    fetchUsers() {
      axios.get('/list-users')
        .then(response => {
          this.users = response.data;
        })
        .catch(error => {
          console.error('Error al obtener la lista de usuarios:', error);
        });
    },
    openEditModal(user) {
      // Guardar una copia del usuario original
      this.originalUser = { ...user };

      // Limpiar el campo de contraseña
      const { password, ...editedUser } = user;
      this.editedUser = editedUser;

      $('#editModal').modal('show'); // Mostrar el modal manualmente
    },
    confirmUpdate() {
      Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas guardar los cambios?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.submitEditForm();
        }
      });
    },
    submitEditForm() {
      if (!this.editedUser) {
        console.error('Error: editedUser no está definido');
        return;
      }

      const updatedUser = {
        _id: this.editedUser._id,
        nombre: this.editedUser.nombre,
        apellido_paterno: this.editedUser.apellido_paterno,
        apellido_materno: this.editedUser.apellido_materno,
        email: this.editedUser.email,
        roles: this.editedUser.roles,
      };

      if (this.editedUser.password) {
        updatedUser.password = this.editedUser.password;
      }

      // Imprimir el objeto actualizado para verificar los valores
      console.log("Valores antes de enviar la solicitud:", updatedUser);

      axios.put(`/users/${updatedUser._id}`, updatedUser)
        .then(response => {
          this.fetchUsers();
          $('#editModal').modal('hide');
          Swal.fire(
            'Actualizado',
            'El usuario ha sido actualizado exitosamente.',
            'success'
          );
        })
        .catch(error => {
          console.error('Error al actualizar el usuario:', error);
          Swal.fire(
            'Error',
            'Hubo un problema al actualizar el usuario.',
            'error'
          );
        });
    },
    confirmDelete(user) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: 'Una vez eliminado, no podrás recuperar este usuario.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(`/users/${user._id}`)
            .then(response => {
              Swal.fire(
                'Eliminado',
                'El usuario ha sido eliminado.',
                'success'
              );
              this.fetchUsers();
            })
            .catch(error => {
              console.error('Error al eliminar el usuario:', error);
              Swal.fire(
                'Error',
                'Hubo un problema al eliminar el usuario.',
                'error'
              );
            });
        }
      });
    }
  }
};
</script>

<style scoped>
/* Estilos personalizados, si es necesario */
.user-list {
  display: flex;
  flex-wrap: wrap;
}

.user-card {
  position: relative;
  width: calc(33.33% - 20px);
  margin: 10px;
  background-color: #ffffff;
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 20px;
  transition: all 0.3s ease;
}

.user-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.user-info {
  text-align: center;
}

.user-info h4 {
  margin-bottom: 10px;
}

.role-list {
  padding-left: 0;
  list-style-type: none;
}

.role {
  display: inline-block;
  margin-right: 5px;
  background-color: #791818;
  color: #fff;
  padding: 5px 10px;
  border-radius: 15px;
}

.button-group {
  position: absolute;
  top: 1px;
  right: 1px;
  background-color: transparent;
}

.btn-edit, .btn-delete {
  padding: 4px 8px;
  margin-right: 5px;
  border: none;
  background: none;
  cursor: pointer;
}

.btn-edit i, .btn-delete i {
  font-size: 16px;
}
</style>
