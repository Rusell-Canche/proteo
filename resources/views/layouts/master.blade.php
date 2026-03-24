<!DOCTYPE html>

<html lang="en">

<head>
    @vite('resources/js/app.js', 'resources/css/css.js')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/cropped-color1.png" type="image/x-icon">

    <title>@yield('title')</title> <!-- Si no se define un título específico en la vista, se usará "AGQROO" por defecto -->

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Estilo hover para los elementos del menú */
        .navbar-nav .nav-item:hover {
            background-color: #e7e7ff;
            transition: background-color 0.3s ease;
        }

        .navbar-nav .nav-item:hover .nav-link span {
            color: white;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-item:hover .nav-link i {
            color: white;
            transition: color 0.3s ease;
        }

        /* Bloquear el hover en el ítem activo */
        .navbar-nav .nav-item.active:hover {
            background-color: #e7e7ff !important;
            cursor: default;
        }


        .nav-item.active>a.nav-link {
            background-color: #e7e7ff;
            /* Color de fondo para el elemento activo */
            color: #6969f4 !important;
            /* Color del texto */
        }

        .nav-item.active i {
            color: #6969f4 !important;
            /* Color del ícono */
        }

        .nav-item.active>a.nav-link {
            background-color: #e7e7ff;
            color: #6969f4 !important;
        }

        .nav-item.active>a.nav-link span {
            color: #6969f4 !important;
        }

        .nav-item.active>a.nav-link i {
            color: #6969f4 !important;
        }
    </style>

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        @if(Auth::check() && Auth::user()->hasRole(['administrador', 'plantillas', 'capturista', 'validador', 'carrusel', 'onepiece']))
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-icon rotate-n-15">
                </div>
                <div class="sidebar-brand-logo">
                    <img src="assets/logo.png" alt="Logo" style="max-width: 100%; height: auto; max-height: 60px;">
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="/dashboard">
                    <i class="fas fa-home" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">PANEL DE CONTROL</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            @if(Auth::check() && Auth::user()->hasRole(['plantillas', 'onepiece']))
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item {{ Request::is('consultarplantillas', 'crearplantillas', 'predeterminadasplantillas') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria -controls="collapseTwo">
                    <i class="fas fa-folder-open" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Plantillas</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Acciones:</h6>
                        <a class="collapse-item" href="/consultarplantillas">Consultar plantillas</a>
                        <a class="collapse-item" href="/crearplantillas">Crear nueva plantilla</a>
                     <!--   <a class="collapse-item text-truncate" href="/predeterminadasplantillas">Crear con una existente</a> -->
                    </div>
                </div>
            </li>
            @endif

            @if(Auth::check() && Auth::user()->hasRole(['administrador',  'onepiece']))
            <li class="nav-item {{ Request::is('consultardocumentos', 'consultardocumentosglobal', 'creardocumentos') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-file-alt" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Documentos</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Acciones:</h6>
                        <a class="collapse-item" href="/consultardocumentos">Consultar Documentos</a>
                        <a class="collapse-item" href="/consultardocumentosglobal">Busqueda global</a>
                        <a class="collapse-item" href="/validardocumentos">Validar documentos</a>
                        <a class="collapse-item" href="/creardocumentos">Crear documentos</a>
                    </div>
                </div>
            </li>
            @endif

            @if(Auth::check() && Auth::user()->hasRole([ 'capturista']))
            <li class="nav-item {{ Request::is('consultardocumentos', 'consultardocumentosglobal', 'creardocumentos') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-file-alt" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Documentos</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Acciones:</h6>
                     
                        <a class="collapse-item" href="/creardocumentos">Crear documentos</a>
                    </div>
                </div>
            </li>
            @endif

            @if(Auth::check() && Auth::user()->hasRole([ 'validador']))
            <li class="nav-item {{ Request::is('consultardocumentos', 'consultardocumentosglobal', 'creardocumentos') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-file-alt" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Documentos</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Acciones:</h6>
                     
                        <a class="collapse-item" href="/validardocumentos">Validar documentos</a>
                    </div>
                </div>
            </li>
            @endif

            <!--
            @if(Auth::check() && Auth::user()->hasRole(['administrador', 'validador' ,'onepiece']))
            <li class="nav-item {{ Request::is('comentarios') ? 'active' : '' }}">
                <a class="nav-link" href="/comentarios">
                    <i class="fas fa-comments" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Comentarios</span></a>
            </li>
            @endif
    -->


            @if(Auth::check() && Auth::user()->hasRole(['carrusel', 'onepiece']))
            <li class="nav-item {{ Request::is('carrusel') ? 'active' : '' }}">
                <a class="nav-link" href="/carrusel">
                    <i class="far fa-images" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Carrusel</span>
                </a>
            </li>

            @endif

            @if(Auth::check() && Auth::user()->hasRole(['administrador', 'onepiece']))
            <li class="nav-item {{ Request::is('respaldo') ? 'active' : '' }}">
                <a class="nav-link" href="/respaldo">
                    <i class="fas fa-download" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Respaldo</span></a>
            </li>
            @endif

            @if(Auth::check() && Auth::user()->hasRole(['administrador' ,'onepiece']))
            <li class="nav-item {{ Request::is('estadisticas') ? 'active' : '' }}">
                <a class="nav-link" href="/estadisticas">
                    <i class="fas fa-chart-bar" style="color: #6b7990;"></i></i>
                    <span style="color: #848B96;">Estadisticas</span></a>
            </li>
            @endif

            <!--
            @if(Auth::check() && Auth::user()->hasRole(['administrador']))
            <li class="nav-item">
                <a class="nav-link" href="/noticiasadmin">
                    <i class="fas fa-newspaper"></i>
                    <span>Noticias</span></a>
            </li> 
            @endif
-->

            @if(Auth::check() && Auth::user()->hasRole(['onepiece']))
            <li class="nav-item {{ Request::is('adminpublic') ? 'active' : '' }}">
                <a class="nav-link" href="/adminpublic">
                    <i class="fas fa-magic" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Vista publica</span></a>
            </li>
            @endif

            <!--
            @if(Auth::check() && Auth::user()->hasRole(['administrador']))
            <li class="nav-item">
                <a class="nav-link" href="/investigadores">
                    <i class="fas fa-user-plus"></i> <span>Investigadores</span></a>
            </li>
            @endif

-->
            @if(Auth::check() && Auth::user()->hasRole(['onepiece']))
            <li class="nav-item {{ Request::is('Createuser', 'list-user') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-users" style="color: #6b7990;"></i>
                    <span style="color: #848B96;">Usuarios</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Acciones:</h6>
                        <a class="collapse-item" href="/Createuser">Crear usuario</a>
                        <a class="collapse-item" href="/list-user">Listar usuarios</a>
                    </div>
                </div>
            </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Topbar Navbar -->
                    <style>
                        .navbar-brand {
                            margin-left: 700px;
                            /* Mover hacia la derecha */
                            display: block;
                        }
                    </style>
                    <h1 class="navbar-brand">@yield('page-title')</h1>

                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->nombre }} {{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }}</span>
                                <i class="fas fa-caret-down"></i> </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
                <div id="swup">

                    <!-- Begin Page Content -->
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                    <!-- End of Main Content -->

                </div>

            </div>
        </div>
        <!-- End of Content Wrapper -->

        @else

        <!-- Mostrar contenido alternativo para usuarios con rol "user" -->

        <div class="lock"></div>
        <div class="message">
            <h1>El acceso a esta pagina esta restingido</h1>
            <p>Por favor comunicate con el administrador si consideras que hay un error</p>
            <a href="http://192.169.1.92:8000/home" type="button" class="btn btn-danger">Salir</a>
        </div>

        <style>
            @import url('https://fonts.googleapis.com/css?family=Lato');

            * {
                position: relative;
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Lato', sans-serif;
            }



            body {
                height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                background: linear-gradient(to bottom right, #EEE, #AAA);
            }

            h1 {
                margin: 40px 0 20px;
            }

            p {
                text-align: center;
            }

            .lock {
                border-radius: 5px;
                width: 65px;
                height: 45px;
                background-color: #333;
                animation: dip 1s;
                animation-delay: 1.5s;

            }

            .lock::before,
            .lock::after {
                content: '';
                position: absolute;
                border-left: 5px solid #333;
                height: 20px;
                width: 15px;
                left: calc(50% - 12.5px);
            }

            .lock::before {
                top: -30px;
                border: 5px solid #333;
                border-bottom-color: transparent;
                border-radius: 15px 15px 0 0;
                height: 30px;
                animation: lock 2s, spin 2s;
            }

            .lock::after {
                top: -10px;
                border-right: 5px solid transparent;
                animation: spin 2s;


            }

            .message {
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
            }




            @keyframes lock {
                0% {
                    top: -45px;
                }

                65% {
                    top: -45px;
                }

                100% {
                    top: -30px;
                }
            }

            @keyframes spin {
                0% {
                    transform: scaleX(-1);
                    left: calc(50% - 30px);
                }

                65% {
                    transform: scaleX(1);
                    left: calc(50% - 12.5px);
                }
            }

            @keyframes dip {
                0% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(10px);
                }

                100% {
                    transform: translateY(0px);
                }
            }
        </style>


        @endif
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Estás listo para irte?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "Cerrar sesión" a continuación si está listo para finalizar su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <form id="logout-form" action="{{ route('users.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button class="btn btn-primary" type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar sesión</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap core JavaScript-->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="assets/vendor/chart.js/Chart.min.js"></script>

</body>

</html>