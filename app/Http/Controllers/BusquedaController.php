<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MongoDB\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class BusquedaController extends Controller
{
    public function buscarClave(Request $request)
    {
        // Validar la entrada
        $validator = Validator::make($request->all(), [
            'palabra_clave' => 'required|string',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obtener la palabra clave validada
        $palabraClave = $validator->validated()['palabra_clave'];
        $nombreBaseDatos = 'proteo';

        // Conectar a MongoDB
        $client = new Client();

        // Obtener todas las colecciones en la base de datos
        $colecciones = $client->$nombreBaseDatos->listCollections();

        $resultados = [];

        $coleccionesOmitir = ['navbar_colors','carrousel_images', 'migrations', 'personal_access_tokens', 'plantillas_predeterminadas', 'users', 'comentarios', 'noticias_collection'];

        $camposOmitir = ['_id', 'created_at', 'updated_at', 'Recurso Digital'];

        // Iterar sobre cada colección y realizar la búsqueda
        foreach ($colecciones as $coleccion) {
            $nombreColeccion = $coleccion->getName();

            // Verificar si la colección debe ser omitida
            if (in_array($nombreColeccion, $coleccionesOmitir)) {
                continue; // Saltar a la siguiente iteración
            }

            // Realizar la búsqueda en la colección actual en todos los campos
            $resultadosColeccion = $client->$nombreBaseDatos->$nombreColeccion
                ->find([
                    '$or' => $this->construirExpresionesDeBusqueda($palabraClave, $nombreColeccion, $camposOmitir)
                ])
                ->toArray();

            // Agregar el nombre de la colección como un campo adicional a cada documento
            foreach ($resultadosColeccion as &$documento) {
                $documento['tipo_coleccion'] = $nombreColeccion;
            }

            // Verificar si hay resultados antes de agregar al array
            if ($resultadosColeccion) {
                $resultados = array_merge($resultados, $resultadosColeccion);
            }
        }

        return response()->json($resultados);
    }



    
    // Construir expresiones de búsqueda para todos los campos en la colección
    private function construirExpresionesDeBusqueda($palabraClave, $nombreColeccion, $camposOmitir)
    {
        $expresiones = [];

        // Obtener todos los documentos en la colección
        $client = new Client();
        $colecciones = $client->selectDatabase('proteo')->selectCollection($nombreColeccion);
        $documentos = $colecciones->find();

        foreach ($documentos as $documento) {
            foreach ($documento as $campo => $valor) {
                // Verificar si el campo debe ser omitido
                if (in_array($campo, $camposOmitir)) {
                    continue; // Saltar a la siguiente iteración
                }

                $expresiones[] = [$campo => ['$regex' => $palabraClave]];
            }
        }

        // Verificar si hay expresiones antes de devolver el resultado
        if (empty($expresiones)) {
            // Devolver una expresión predeterminada (puedes ajustar según tus necesidades)
            $expresiones = [['_id' => ['$exists' => true]]];
        }

        return $expresiones;
    }







    public function avanzadaBusqueda(Request $request)
    {
    
        
        $palabrasClave = $request->input('palabras_clave');
        $nombreColeccion = $request->input('nombre_coleccion');
        $nombreBaseDatos = 'proteo';

        Log::info("Datos recibidos de Vue", ['palabras_clave' => $palabrasClave, 'nombre_coleccion' => $nombreColeccion]);


        // Conectar a MongoDB
        $client = new Client();

        // Obtener todas las colecciones en la base de datos
        $colecciones = $client->$nombreBaseDatos->listCollections();
 
        $resultados = [];

        // Iterar sobre cada colección y realizar la búsqueda
        foreach ($colecciones as $coleccion) {
            $nombreColeccionActual = $coleccion->getName();

            // Realizar la búsqueda en la colección actual
            if ($nombreColeccionActual === $nombreColeccion) {
                // Obtener los campos del modelo asociado a la colección
                $campos = $this->getFieldsForSearch($nombreColeccionActual);

                // Si no hay palabras clave, obtener todos los documentos de la colección
                if (empty($palabrasClave)) {
                    $resultadosColeccion = $client->$nombreBaseDatos->$nombreColeccionActual
                        ->find()
                        ->toArray();
                } else {
                    // Construir un arreglo de condiciones AND para cada palabra clave en cada campo
                    $condicionesAnd = [];
                    foreach ($palabrasClave as $palabra) {
                        $condicionesPalabra = [];
                        foreach ($campos as $campo) {
                            if (is_array($campo)) {
                                foreach ($campo as $campoSub) {
                                    if (!empty($campoSub)) {
                                        $condicionesPalabra[] = [$campoSub['name'] => ['$regex' => $palabra]];
                                    }
                                }
                            } else {
                                if (!empty($campo)) {
                                    $condicionesPalabra[] = [strval($campo) => ['$regex' => $palabra]];
                                }
                            }
                        }
                        // Agregar las condiciones de la palabra clave actual al conjunto AND
                        if (!empty($condicionesPalabra)) {
                            $condicionesAnd[] = ['$or' => $condicionesPalabra];
                        }
                    }

                    // Realizar la búsqueda en la colección actual solo si hay condiciones AND
                    if (!empty($condicionesAnd)) {
                        $resultadosColeccion = $client->$nombreBaseDatos->$nombreColeccionActual
                            ->find(['$and' => $condicionesAnd])
                            ->toArray();
                    } else {
                        // Si no hay condiciones AND, mostrar todos los documentos de la colección
                        $resultadosColeccion = $client->$nombreBaseDatos->$nombreColeccionActual
                            ->find()
                            ->toArray();
                    }
                }

                // Agregar el nombre de la colección como un campo adicional a cada documento
                foreach ($resultadosColeccion as &$documento) {
                    $documento['tipo_coleccion'] = $nombreColeccionActual;
                }

                // Agregar los resultados de la colección actual al resultado final
                $resultados = array_merge($resultados, $resultadosColeccion);
            }
        }

        // Retorna los resultados como una respuesta JSON
        return response()->json($resultados);
    }

    public function getFieldsForSearch($plantillaName)
    {
        // 1. Obtén el nombre de la colección y el modelo relacionado
        $modelName = 'App\\Models\\' . Str::studly($plantillaName);

        // 2. Verifica que la clase exista
        if (class_exists($modelName)) {
            // 3. Utiliza la información estática del modelo para obtener los campos y sus tipos
            $fields = [];

            foreach ($modelName::$fieldTypes as $fieldName => $fieldType) {
                $fieldAlias = $modelName::$fieldAliases[$fieldName] ?? $fieldName;

                $fields[] = [
                    'name' => $fieldName,
                    'type' => $fieldType,
                    'alias' => $fieldAlias,
                    'required' => in_array($fieldName, $modelName::$requiredFields),
                ];
            }

            return response()->json($fields);
        } else {
            return response()->json(['message' => 'Clase no encontrada'], 404);
        }
    }

    public function getFieldsWithSelectValues($plantillaName)
{
    $modelName = 'App\\Models\\' . Str::studly($plantillaName);

    if (!class_exists($modelName)) {
        return response()->json(['message' => 'Clase no encontrada'], 404);
    }

    $model = new $modelName();

    $fieldTypes = $model::$fieldTypes ?? [];
    $fieldAliases = $model::$fieldAliases ?? [];
    $requiredFields = $model::$requiredFields ?? [];

    $fields = [];
    $selectFields = [];

    foreach ($fieldTypes as $fieldName => $fieldType) {
        $fieldAlias = $fieldAliases[$fieldName] ?? $fieldName;
        $isRequired = in_array($fieldName, $requiredFields);

        $fields[$fieldName] = [
            'name' => $fieldName,
            'type' => $fieldType,
            'alias' => $fieldAlias,
            'required' => $isRequired,
            'values' => []
        ];

        // Solo considerar como "select" los campos string, number o date
        if (in_array($fieldType, ['string', 'number', 'date'])) {
            $selectFields[] = $fieldName;
        }
    }

    if (!empty($selectFields)) {
        try {
            foreach ($selectFields as $campo) {
                // CONSULTA CORREGIDA: Usar `raw()` para obtener valores únicos correctamente
                $resultado = DB::connection('mongodb')
                    ->getMongoDB()
                    ->selectCollection($plantillaName)
                    ->distinct($campo, []);

         
                // Si `distinct()` devuelve un array, asignarlo
                $valoresUnicos = is_array($resultado) ? array_values($resultado) : [];

                // Asignar valores al campo
                $fields[$campo]['values'] = $valoresUnicos;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la consulta a MongoDB', 'message' => $e->getMessage()], 500);
        }
    }

    return response()->json(array_values($fields));
}



public function Buscarconocr(Request $request)
{
    $query = DB::connection('mongodb')->collection('DiarioOficial_originales');

    // 🔹 Mapa de meses para convertir texto a número
    $meses = [
        'Enero'=>1,'Febrero'=>2,'Marzo'=>3,'Abril'=>4,'Mayo'=>5,'Junio'=>6,
        'Julio'=>7,'Agosto'=>8,'Septiembre'=>9,'Octubre'=>10,'Noviembre'=>11,'Diciembre'=>12
    ];

    // 🔹 Filtrar por fecha desde (YYYY-MM-DD)
    if ($request->filled('fechaDesde')) {
        $fechaDesde = explode('-', $request->fechaDesde); 
        $yearDesde = (int)$fechaDesde[0];
        $monthDesde = (int)$fechaDesde[1];
        $dayDesde = (int)$fechaDesde[2];

        $query->where(function($q) use ($yearDesde, $monthDesde, $dayDesde, $meses) {
            $q->where('Año', '>', $yearDesde)
              ->orWhere(function($q2) use ($yearDesde, $monthDesde, $dayDesde, $meses) {
                  $mesesValidos = array_keys(array_slice($meses, $monthDesde-1, null, true));
                  $q2->where('Año', $yearDesde)
                     ->whereIn('Mes', $mesesValidos)
                     ->where('Día', '>=', $dayDesde);
              });
        });
    }

    // 🔹 Filtrar por fecha hasta
    if ($request->filled('fechaHasta')) {
        $fechaHasta = explode('-', $request->fechaHasta); 
        $yearHasta = (int)$fechaHasta[0];
        $monthHasta = (int)$fechaHasta[1];
        $dayHasta = (int)$fechaHasta[2];

        $query->where(function($q) use ($yearHasta, $monthHasta, $dayHasta, $meses) {
            $q->where('Año', '<', $yearHasta)
              ->orWhere(function($q2) use ($yearHasta, $monthHasta, $dayHasta, $meses) {
                  $mesesValidos = array_keys(array_slice($meses, 0, $monthHasta, true));
                  $q2->where('Año', $yearHasta)
                     ->whereIn('Mes', $mesesValidos)
                     ->where('Día', '<=', $dayHasta);
              });
        });
    }

    // 🔹 Filtrar por Tipo Publicación
    if ($request->filled('tipoPublicacion')) {
        $tipo = $request->tipoPublicacion;
        $query->where('Tipo Publicación', 'regex', new \MongoDB\BSON\Regex($tipo, 'i'));
    }

    // 🔹 Filtrar por Época
    if ($request->filled('epoca')) {
        $epoca = $request->epoca;
        $query->where('Epoca', 'regex', new \MongoDB\BSON\Regex($epoca, 'i'));
    }

    // 🔹 Buscar palabras clave en el campo OCR (ignorando acentos y mayúsculas)
    if ($request->filled('palabrasClave')) {
        $palabras = explode(' ', $request->palabrasClave);
        $query->where(function($q) use ($palabras) {
            foreach ($palabras as $palabra) {
                $palabraNorm = Str::ascii(strtolower($palabra));
                $q->where('Ocr', 'regex', new \MongoDB\BSON\Regex($palabraNorm, 'i'));
            }
        });
    }

    $resultados = $query->get();

    return response()->json($resultados);
}

}
