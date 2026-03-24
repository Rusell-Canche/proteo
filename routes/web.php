    <?php

    use App\Http\Controllers\Adminpublic;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\UsersController;
    use App\Http\Controllers\PlantillaController;
    use App\Http\Controllers\CarrouselController;
    use App\Http\Controllers\DocumentoController;
    use App\Http\Controllers\BusquedaController;
    use App\Http\Controllers\ComentariosController;
    use App\Http\Controllers\RespaldoController;
    use App\Http\Controllers\DescargaController;
    use App\Http\Controllers\NoticiaController;
    use App\Http\Controllers\PdfController;


    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "web" middleware group. Make something great!
    |
    */

    Route::post('/storediariooficial', [DocumentoController::class, 'storeDiarioOficial']);

    Route::post('/busquedaconocr', [BusquedaController::class, 'Buscarconocr']);




    Route::post('/procesar-pdf', [PdfController::class, 'procesarPDF']);

    
    Route::get('/watermark/{plantillaName}/{documentId}', [DescargaController::class, 'descargarConMarcaAgua']);

    //Buscar por campos especificos 
    Route::post('/busqueda-avanzada', [BusquedaController::class, 'avanzadaBusqueda']);
    Route::get('/busquedaselect/{plantillaName}', [BusquedaController::class, 'getFieldsWithSelectValues']);

    Route::post('/search', [BusquedaController::class, 'buscarClave']);
    Route::post('/searchAdmin', [BusquedaController::class, 'buscarClaveAdmin']);


    //VISTAS PUBLICAS
    Route::get('/home', function () {
        return view('publicas.home');
    });

    Route::get('/noticiaspublic', function () {
        return view('publicas.noticias');
    });



    Route::get('/perfil', function () {
        return view('publicas.perfil');
    });

    Route::get('/busquedaavanzada', function () {
        return view('publicas.busquedaavanzada');
    });

    Route::get('/busquedasencilla', function () {
        return view('publicas.busquedasencilla');
    });

    Route::get('/busquedaselect', function () {
        return view('publicas.busquedaselect');
    });

    Route::get('/elegirbusqueda', function () {
        return view('publicas.elegirbusqueda');
    });


    Route::get('/detallesdocumento', function () {
        return view('publicas.detallesdocumento');
    });


    Route::get('/register', function () {
        return view('publicas.register');
    });


    Route::get('/loginUser', function () {
        return view('publicas.loginUser');
    });

    Route::get('/consultarocr', function () {
        return view('administrativas.consultarocr');
    });



    Route::post('/cambiar-color-navbar', [Adminpublic::class, 'cambiarColor']);
    Route::get('/get-navbar-color', [Adminpublic::class, 'getColor']);


    //VISTAS ADMIN
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');


    Route::get('/dashboard', [UsersController::class, 'dashboard'])->name('dashboard')->middleware('auth');

    Route::get('/Createuser', function () {
        return view('administrativas.createuser');
    });

    Route::get('/list-user', function () {
        return view('administrativas.listuser');
    });

    Route::get('/consultarplantillas', function () {
        return view('administrativas.consultarplantillas');
    });

    Route::get('/crearplantillas', function () {
        return view('administrativas.crearplantillas');
    });

    Route::get('/predeterminadasplantillas', function () {
        return view('administrativas.predeterminadasplantillas');
    });

    Route::get('/consultardocumentos', function () {
        return view('administrativas.consultardocumentos');
    });

    Route::get('/consultardocumentosglobal', function () {
        return view('administrativas.busquedaglobal');
    });
    Route::get('/creardocumentos', function () {
        return view('administrativas.creardocumentos');
    });

    Route::get('/comentarios', function () {
        return view('administrativas.comentarios');
    });

    Route::get('/carrusel', function () {
        return view('administrativas.carrusel');
    });

    Route::get('/respaldo', function () {
        return view('administrativas.respaldo');
    });

    Route::get('/estadisticas', function () {
        return view('administrativas.estadisticas');
    });

    Route::get('/adminpublic', function () {
        return view('administrativas.adminpublic');
    });


    Route::get('/noticiasadmin', function () {
        return view('administrativas.noticias');
    });


    Route::get('/investigadores', function () {
        return view('administrativas.investigadores');
    });

    Route::get('/validardocumentos', function () {
        return view('administrativas.validardocumentos');
    });

  Route::get('/busquedaocr', function () {
        return view('administrativas.buscarconocr');
    });





    Route::post('/loginUser', [UsersController::class, 'loginUser'])->name('users.loginUser');


    Route::post('/registeruser', [UsersController::class, 'userRegister']);

    //RUTAS PARA USUARIOS ADMINISTRATIVOS
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::post('/login', [UsersController::class, 'login'])->name('users.login');
    Route::post('/logout', [UsersController::class, 'logout'])->name('users.logout');

    Route::get('/list-users', [UsersController::class, 'listUsers'])->name('users.list');
    Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{id}', [UsersController::class, 'update'])->name('users.update');


    //PLANTILLAS


    Route::get('/plantillas-predeterminadas', [PlantillaController::class, 'obtenerPlantillasPredeterminadas']);
    Route::get('/plantillas', [PlantillaController::class, 'getModelos']);
    Route::get('/plantillasEdit', [PlantillaController::class, 'getModelosforEdit']);

    Route::post('/plantillas', [PlantillaController::class, 'create']);
    Route::delete('/plantillas/{plantillaName}', [PlantillaController::class, 'delete']);
    Route::put('/plantillas/{plantillaName}', [PlantillaController::class, 'update']);
    Route::get('/plantillas/{plantillaName}/fields', [PlantillaController::class, 'getFieldsFromModel']);



    // DOCUMENTOS
    Route::get('/documentos/{plantillaName}/{documentId}', [DocumentoController::class, 'getDocumentbyid']);
    Route::get('/documentos/plantillas', [DocumentoController::class, 'getforDocuments']);
    Route::get('/documentos/{plantillaName}', [DocumentoController::class, 'getAllDocuments']);
    Route::post('/documentos/{plantillaName}', [DocumentoController::class, 'storeDocument']);
    Route::delete('/documentos/{plantillaName}/{documentId}', [DocumentoController::class, 'deleteDocument']);
    Route::post('/documentos/{plantillaName}/{documentId}', [DocumentoController::class, 'updateDocument']);
    Route::get('/ultimos-documentos', [DocumentoController::class, 'obtenerUltimosDocumentos']);

    Route::get('/documentos-pendientes', [DocumentoController::class, 'getPendientes']);
    Route::post('/aprobar-documentos/{plantillaName}/{documentId}', [DocumentoController::class, 'approveDocument']);
    Route::delete('/denegar-documentos/{plantillaName}/{documentId}', [DocumentoController::class, 'denyDocument']);

    //CARRUSEL
    Route::post('/carrousel/store', [CarrouselController::class, 'store']);
    Route::get('carrousel/images', [CarrouselController::class, 'getImagesForCarousel']);
    Route::get('carrousel/all-images', [CarrouselController::class, 'getAllCarrouselImages']);
    Route::delete('/carrousel/delete/{id}', [CarrouselController::class, 'eliminarImagen']);

    //RESPALDO



    // Rutas para gestionar respaldos
    Route::get('/backup/create', [RespaldoController::class, 'createBackup'])->name('backup.create');
    Route::get('/backup/last', [RespaldoController::class, 'fechaUltimoRespaldo'])->name('backup.last');
    Route::get('/backup/list', [RespaldoController::class, 'listaRespaldos'])->name('backup.list');
    Route::get('/backup/open', [RespaldoController::class, 'abrirApp'])->name('backup.open');
    Route::post('/backup/restore', [RespaldoController::class, 'restore'])->name('backup.restore');


    //BUSQUEDA

    //DESCARGA CON MARCA DE AGUA

    //COMENTARIOS 
    // Ruta para almacenar un nuevo comentario
    Route::post('/comentarios', [ComentariosController::class, 'store']);

    // Ruta para obtener comentarios aprobados por ID de documento
    Route::get('/comentarios/documento/{documentoId}', [ComentariosController::class, 'comentariosAprobados']);

    // Ruta para aprobar un comentario
    Route::put('/comentarios/aprobar/{id}', [ComentariosController::class, 'aprobarComentario']);

    // Ruta para denegar (y eliminar) un comentario
    Route::delete('/comentarios/denegar/{id}', [ComentariosController::class, 'denegarComentario']);

    // Ruta para obtener todos los comentarios pendientes
    Route::get('/comentarios/pendientes', [ComentariosController::class, 'obtenerComentariosPendientes']);

    // Ruta para obtener todos los comentarios aprobados
    Route::get('/comentarios/aprobados', [ComentariosController::class, 'obtenerComentariosAprobados']);


    Route::post('/guardarnoticias', [NoticiaController::class, 'store']);

    Route::get('/obtenernoticias', [NoticiaController::class, 'index']);
