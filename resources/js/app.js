import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';


import 'lightbox2/dist/css/lightbox.css';
/*
import $ from 'jquery';
window.jQuery = $;
window.$ = $;
*/
import store from './store';

import 'lightbox2/dist/js/lightbox-plus-jquery';
import { createApp } from 'vue';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css'; // Asegúrate de que esta ruta sea correcta


const app = createApp({});




import Dashboard from './admin/Dashboard.vue';
app.component('dashboard-vue', Dashboard);

import Createuser from './admin/Createuser.vue';
app.component('createuser-vue', Createuser);

import Listuser from './admin/Listuser.vue';
app.component('listuser-vue', Listuser);

import Consularplantillas from './admin/Consultarplantillas.vue';
app.component('consultarplantillas-vue', Consularplantillas);

import Crearplantillas from './admin/Crearplantillas.vue';
app.component('crearplantillas-vue', Crearplantillas);

import Predeterminadasplantillas from './admin/Predeterminadasplantillas.vue';
app.component('predeterminadasplantillas-vue', Predeterminadasplantillas);

import Creardocumentos from './admin/Creardocumentos.vue';
app.component('creardocumentos-vue', Creardocumentos);

import Consultardocumentos from './admin/Consultardocumentos.vue';
app.component('consultardocumentos-vue', Consultardocumentos);

import Busquedaglobal from './admin/Busquedaglobal.vue';
app.component('busquedaglobal-vue', Busquedaglobal);

import Comentarios from './admin/Comentarios.vue';
app.component('comentarios-vue', Comentarios);

import Carrusel from './admin/Carrusel.vue';
app.component('carrusel-vue', Carrusel);

import Respaldo from './admin/Respaldo.vue';
app.component('respaldo-vue', Respaldo);

import Estadisticas from './admin/Estadisticas.vue';
app.component('estadisticas-vue', Estadisticas);

import Adminpublic from './admin/Adminpublic.vue';
app.component('adminpublic-vue', Adminpublic);

import Noticiasadmin from './admin/Noticias.vue';
app.component('noticiasadmin-vue', Noticiasadmin);

import Investigadores from './admin/Investigadores.vue';
app.component('investigadores-vue', Investigadores);

import Validardocumentos from './admin/Validardocumentos.vue';
app.component('validardocumentos-vue', Validardocumentos);




import Home from './public/Home.vue';
app.component('home-vue', Home);

import BusquedaAvanzada from './public/BusquedaAvanzada.vue';
app.component('busquedaavanzada-vue', BusquedaAvanzada);

import Navbar from './components/Navbar.vue';
app.component('app-navbar', Navbar);

import BusquedaSencilla from './public/BusquedaSencilla.vue';
app.component('busquedasencilla-vue', BusquedaSencilla);

import DetallesDocumento from './public/DetallesDocumento.vue';
app.component('detallesdocumento-vue', DetallesDocumento);

import BusquedaSelect from './public/BusquedaSelect.vue';
app.component('busquedaselect-vue', BusquedaSelect);

import Register from './public/Register.vue';
app.component('register-vue', Register);


import LoginUser from './public/LoginUser.vue';
app.component('loginuser-vue', LoginUser);

import Perfil from './public/Perfil.vue';
app.component('perfil-vue',Perfil);

import Noticias from './public/Noticias.vue';
app.component('noticias-vue', Noticias);

import Elegirbusqueda from './public/Elegirbusqueda.vue';
app.component('elegirbusqueda-vue', Elegirbusqueda);

import Consultarocr from './admin/Consultarocr.vue';
app.component('consultarocr-vue', Consultarocr);

import Buscarconocr from './admin/Buscarconocr.vue';
app.component('buscarconocr-vue', Buscarconocr);


    app.use(store);
app.use(ElementPlus);

app.mount('#app');

