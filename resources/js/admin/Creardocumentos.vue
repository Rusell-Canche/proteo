<template>
    <div class="container mt-3">
        <h2>Crear Documento</h2>

        <!-- Selector de Plantillas -->
        <div class="form-group">
            <select id="plantillaSelector" class="form-control" v-model="selectedPlantilla"
                @change="onPlantillaSelected">
                <option value="" disabled selected>Selecciona una plantilla</option>
                <option v-for="plantilla in filteredPlantillas" :key="plantilla.nombre" :value="plantilla.nombre">
                    {{ plantilla.title || plantilla.nombre }}
                </option>

            </select>

        </div>


        <div v-if="camposPlantilla.length > 0">
            <h4>Campos de la Plantilla</h4>
            <form ref="form" @submit.prevent="onSubmit" enctype="multipart/form-data">
                <div v-for="campo in camposPlantilla" :key="campo.name">
                    <div v-if="campo.name !== '_id'">
                        <label :for="campo.name">{{ campo.alias || campo.name }}:</label>

                        <!-- Genera campos de entrada según el tipo -->
                        <input v-if="campo.type === 'file'" type="file" class="form-control" :id="campo.name"
                            :name="campo.name" @change="onFileChange($event, campo.name)" multiple />
                        <input v-else-if="campo.type === 'number'" type="number" class="form-control" :id="campo.name"
                            v-model="documentData[campo.name]" :required="campo.required" />
                        <input v-else-if="campo.type === 'date'" type="date" class="form-control" :id="campo.name"
                            v-model="documentData[campo.name]" :required="campo.required" />
                        <input v-else type="text" class="form-control" :id="campo.name"
                            v-model="documentData[campo.name]" :required="campo.required" />

                        <div v-if="campo.type === 'file' && files[campo.name]">
                            <h5>Archivos Seleccionados:</h5>
                            <div class="file-thumbnail" v-for="(file, index) in files[campo.name]" :key="index">
                                <img v-if="isImageFile(file)" :src="getThumbnailUrl(file)" alt="Miniatura">
                                <span v-else>{{ file.name }}</span>
                                <button type="button" class="btn btn-link delete-button"
                                    @click="removeFile(campo.name, index)">
                                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <small v-if="campo.required" class="campo-obligatorio-leyenda">* Obligatorio</small>
                        <small v-else class="campo-opcional-leyenda">Opcional</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Crear Documento</button>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            plantillas: [], // Listado de plantillas (modelo)
            selectedPlantilla: null, // Plantilla seleccionada
            camposPlantilla: [], // Campos de la plantilla seleccionada
            documentData: {}, // Datos del formulario
            files: {}, // Archivos por nombre de campo
        };
    },
    computed: {
        filteredPlantillas() {
            // Excluir plantillas innecesarias (si es necesario)
            const excludedPlantillas = ['CarrouselImage', 'Comentario', 'NavbarColor', 'Noticia', 'User', 'PlantillaPredeterminada', 'plantillas_predeterminadas', 'users', 'comentarios', 'carrousel_images', 'visitas'];
            return this.plantillas.filter(plantilla => !excludedPlantillas.includes(plantilla.nombre));
        }
    },
    methods: {
        async fetchPlantillas() {
            try {
                // Aquí se hace una llamada a la API que te devuelve las plantillas y sus estructuras
                const response = await axios.get('/plantillas');
                this.plantillas = response.data;
                console.log(this.plantillas);

            } catch (error) {
                console.error('Error obteniendo plantillas', error);
            }
        },
        async onPlantillaSelected() {
            if (this.selectedPlantilla) {
                try {
                    // Realiza una solicitud al backend para obtener los campos
                    const response = await axios.get(`/plantillas/${this.selectedPlantilla}/fields`);
                    if (response.data) {
                        this.camposPlantilla = response.data;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron obtener los campos de la plantilla seleccionada.',
                        });
                    }
                } catch (error) {
                    console.error('Error al obtener los campos de la plantilla:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al obtener los campos de la plantilla.',
                    });
                }
            }
        },
        onFileChange(event, fieldName) {
            const fileList = event.target.files;
            if (fileList.length > 0) {
                this.files = {
                    ...this.files,
                    [fieldName]: Array.from(fileList)
                };
            }
        },
        isImageFile(file) {
            return file.type.startsWith('image/');
        },
        getThumbnailUrl(file) {
            if (this.isImageFile(file)) {
                return URL.createObjectURL(file);
            }
            return null;
        },
        removeFile(fieldName, index) {
            this.files[fieldName].splice(index, 1);
            if (this.files[fieldName].length === 0) {
                const { [fieldName]: omit, ...rest } = this.files;
                this.files = rest;
            } else {
                this.files = {
                    ...this.files,
                    [fieldName]: this.files[fieldName]
                };
            }
        },
        async onSubmit() {
            // Validación de campos requeridos
            const camposRequeridosVacios = this.camposPlantilla
                .filter(campo => campo.required)
                .some(campo => {
                    if (campo.type === 'file') {
                        return campo.required && (!this.files[campo.name] || this.files[campo.name].length === 0);
                    } else {
                        return campo.required && !this.documentData[campo.name];
                    }
                });

            if (camposRequeridosVacios) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos Obligatorios',
                    text: 'Por favor, completa todos los campos obligatorios.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Mostrar datos antes de enviar
            console.log('Datos del documento antes de crear:', this.documentData);
            console.log('Archivos adjuntos:', this.files);

            const formData = new FormData();

            // Añadir datos al FormData
            for (const campo of this.camposPlantilla) {
                if (campo.type !== 'file') {
                    formData.append(`document_data[${campo.name}]`, this.documentData[campo.name] || '');
                }
            }

            // Añadir archivos
            for (const campo of this.camposPlantilla) {
                if (campo.type === 'file') {
                    if (this.files[campo.name] && this.files[campo.name].length > 0) {
                        this.files[campo.name].forEach(file => {
                            formData.append(`files[]`, file);
                        });
                    }
                }
            }

            try {
                // 🔹 Detectar si es DiarioOficial
                let url = `/documentos/${this.selectedPlantilla}`;
                if (this.selectedPlantilla === 'DiarioOficial') {
                    url = '/storediariooficial'; // Nuevo endpoint para OCR
                }

                const response = await axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                Swal.fire({
                    title: 'Éxito',
                    text: 'El documento se ha creado exitosamente.',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });

                // Limpiar formulario
                this.documentData = {};
                this.files = {};
                this.$refs.form.reset();
            } catch (error) {
                console.error('Error al crear el documento', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un problema al crear el documento. Por favor, inténtalo de nuevo.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        }

    },
    mounted() {
        this.fetchPlantillas();
    }
};

</script>

<style scoped>
.campo-obligatorio-leyenda {
    color: red;
}

.campo-opcional-leyenda {
    color: grey;
}

.file-thumbnail {
    display: flex;
    align-items: center;
}

.file-thumbnail img {
    width: 50px;
    height: 50px;
    margin-right: 10px;
}

.delete-button {
    color: red;
    cursor: pointer;
}
</style>
