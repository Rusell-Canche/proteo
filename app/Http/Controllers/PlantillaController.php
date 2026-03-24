<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\PlantillaPredeterminada;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

use ReflectionClass;

class PlantillaController extends Controller
{

    public function getModelosforEdit()
{
    $modelosPath = app_path('Models');
    $modelos = [];

    // Lista de modelos a excluir (sin la extensión .php)
    $excluidos = [
        'CarrouselImage',
        'Comentario',
        'NavbarColor',
        'PlantillaPredeterminada',
        'Noticia',
        'User'
    ];

    $archivos = File::allFiles($modelosPath);

    foreach ($archivos as $archivo) {
        $nombreClase = 'App\\Models\\' . basename($archivo, '.php');

        // Verificar si la clase existe y si es un modelo MongoDB
        if (class_exists($nombreClase)) {
            $reflexion = new ReflectionClass($nombreClase);

            if ($reflexion->isSubclassOf('MongoDB\Laravel\Eloquent\Model')) {
                $nombreBase = class_basename($nombreClase);

                // Excluir modelos específicos
                if (!in_array($nombreBase, $excluidos)) {
                    $modeloInfo = [
                        'nombre' => $nombreBase,
                        'campos' => $this->getCamposDeModelo($nombreClase),
                    ];
                    $modelos[] = $modeloInfo;
                }
            }
        }
    }

    return response()->json($modelos);
}

    
public function getModelos()
{
    $modelosPath = app_path('Models');
    $modelos = [];

    $archivos = File::allFiles($modelosPath);

    foreach ($archivos as $archivo) {
        $nombreClase = 'App\\Models\\' . basename($archivo, '.php');

        if (class_exists($nombreClase)) {
            $reflexion = new ReflectionClass($nombreClase);

            if ($reflexion->isSubclassOf('MongoDB\Laravel\Eloquent\Model')) {
                // Obtener la propiedad estática $title si existe
                $title = property_exists($nombreClase, 'title') ? $nombreClase::$title : class_basename($nombreClase);

                $modeloInfo = [
                    'nombre' => class_basename($nombreClase),
                    'title' => $title, // Agregar el campo 'title'
                    'campos' => $this->getCamposDeModelo($nombreClase),
                ];
                $modelos[] = $modeloInfo;
            }
        }
    }

    return response()->json($modelos);
}


    // Método para obtener los campos de un modelo dado
    private function getCamposDeModelo($modeloClass)
    {
        $campos = [];
        try {
            // Acceder a las propiedades estáticas de la clase
            $reflection = new ReflectionClass($modeloClass);
            if ($reflection->hasProperty('fieldTypes')) {
                $campos['tipos'] = $modeloClass::$fieldTypes ?? [];
            }
            if ($reflection->hasProperty('fieldAliases')) {
                $campos['alias'] = $modeloClass::$fieldAliases ?? [];
            }
            if ($reflection->hasProperty('requiredFields')) {
                $campos['requeridos'] = $modeloClass::$requiredFields ?? [];
            }
        } catch (\Exception $e) {
            // Manejar excepciones si alguna propiedad no existe o no se puede acceder
            $campos = [];
        }

        return $campos;
    }


    
public function getFieldsFromModel($plantillaName)
{
    // 1. Obtén el nombre del modelo basado en el nombre de la plantilla
    $modelName = 'App\\Models\\' . Str::studly($plantillaName);

    // 2. Verifica que la clase exista
    if (class_exists($modelName)) {
        // 3. Utiliza la información estática del modelo para obtener los campos y sus tipos
        $fields = [];

        // Asegúrate de que el modelo tenga las propiedades necesarias para los campos
        $model = new $modelName();

        $fieldTypes = $model::$fieldTypes ?? [];
        $fieldAliases = $model::$fieldAliases ?? [];
        $requiredFields = $model::$requiredFields ?? [];

        foreach ($fieldTypes as $fieldName => $fieldType) {
            $fieldAlias = $fieldAliases[$fieldName] ?? $fieldName;
            $isRequired = in_array($fieldName, $requiredFields);

            $fields[] = [
                'name' => $fieldName,
                'type' => $fieldType,
                'alias' => $fieldAlias,
                'required' => $isRequired,
            ];
        }

        return response()->json($fields);
    } else {
        return response()->json(['message' => 'Clase no encontrada'], 404);
    }
}



public function create(Request $request)
{
    $request->validate([
        'plantilla_name' => 'required|string',
        'fields' => 'required|array',
    ]);

    $plantillaName = str_replace([' ', '_'], '', $request->input('plantilla_name'));
    $pendientesName = "{$plantillaName}_pendientes";

    // Verificar si las colecciones ya existen
    $collections = DB::connection('mongodb')->listCollections();
    foreach ($collections as $collection) {
        if ($collection->getName() === $plantillaName || $collection->getName() === $pendientesName) {
            return response()->json(['error' => "La colección '{$plantillaName}' o su versión pendiente ya existen."], 400);
        }
    }

    // Crear las colecciones accediendo a ellas (MongoDB las creará automáticamente cuando se inserten documentos)
    DB::connection('mongodb')->collection($plantillaName);
    DB::connection('mongodb')->collection($pendientesName);

    // Generar el modelo de la colección principal (pero no para _pendientes, ya que solo almacena temporalmente)
    $modelCode = $this->generateModelCode($plantillaName, $request->input('fields'), $request->input('plantilla_name'));
    $modelFileName = app_path("Models/{$plantillaName}.php");
    file_put_contents($modelFileName, $modelCode);

    return response()->json(['message' => "Colección '{$plantillaName}' y '{$pendientesName}' creadas correctamente."], 201);
}




    


private function generateModelCode($plantillaName, $fields, $originalName)
{
    $modelName = Str::studly($plantillaName);

    $modelCode = "<?php

namespace App\Models;

use MongoDB\\Laravel\\Eloquent\\Model;

class {$modelName} extends Model
{   
    protected \$connection = 'mongodb';

    protected \$collection = '{$plantillaName}_pendientes'; // Establece el nombre de la colección

    protected \$guarded = ['_id', ";

    foreach ($fields as $field) {
        $modelCode .= "'{$field['name']}', ";
    }

    $modelCode .= "];

    // Define los tipos de datos de los campos de manera estática
    public static \$fieldTypes = [
        ";
    
    foreach ($fields as $field) {
        $modelCode .= "'{$field['name']}' => '{$field['type']}', ";
    }

    $modelCode .= "
    ];

    // Define alias para los campos
    public static \$fieldAliases = [
        ";
    
    foreach ($fields as $field) {
        $alias = Str::title(str_replace('_', ' ', $field['name']));
        $modelCode .= "'{$field['name']}' => '{$alias}', ";
    }

    $modelCode .= "
    ];

    // Define los campos obligatorios
    public static \$requiredFields = [
        ";
    
    foreach ($fields as $field) {
        if (isset($field['required']) && $field['required']) {
            $modelCode .= "'{$field['name']}', ";
        }
    }

    $modelCode .= "
    ];

    // Define el nombre con espacios
    public static \$title = '" . addslashes($originalName) . "'; // Nombre original de la plantilla con espacios

}";

    return $modelCode;
}




public function delete($plantillaName)
{
    // Verifica si la colección principal o '_pendientes' existe
    $collections = DB::connection('mongodb')->listCollections();
    $collectionExists = false;

    // Colección principal
    $collectionName = $plantillaName;
    // Colección de pendientes
    $collectionNamePendientes = $plantillaName . '_pendientes';

    foreach ($collections as $collection) {
        if ($collection->getName() === $collectionName || $collection->getName() === $collectionNamePendientes) {
            $collectionExists = true;
            break;
        }
    }

    // Eliminar las colecciones si existen
    if ($collectionExists) {
        // Elimina la colección principal
        if ($this->collectionExists($collectionName)) {
            DB::connection('mongodb')->getCollection($collectionName)->drop();
        }

        // Elimina la colección '_pendientes'
        if ($this->collectionExists($collectionNamePendientes)) {
            DB::connection('mongodb')->getCollection($collectionNamePendientes)->drop();
        }
    }

    // Eliminamos siempre el modelo, independientemente de la existencia de las colecciones
    $modelPath = app_path("Models/{$plantillaName}.php");
    if (file_exists($modelPath)) {
        unlink($modelPath);
        // Solo retornamos el mensaje cuando el archivo es efectivamente eliminado
        return response()->json(['message' => 'Modelo y colecciones eliminados con éxito'], 200);
    } else {
        // Si el archivo del modelo no se encuentra, se reporta la eliminación de colecciones
        return response()->json(['message' => 'Modelo no encontrado, pero las colecciones han sido eliminadas.'], 404);
    }
}

// Función auxiliar para verificar si la colección existe en MongoDB
private function collectionExists($collectionName)
{
    $collections = DB::connection('mongodb')->listCollections();
    foreach ($collections as $collection) {
        if ($collection->getName() === $collectionName) {
            return true;
        }
    }
    return false;
}




    public function update(Request $request, $plantillaName)
    {

        log::info($request);
        $request->validate([
            'fields' => 'required|array',
        ]);

        $fields = $request->input('fields');

 

        $this->updateModel($plantillaName, $fields);

        return response()->json(['message' => 'Migración de actualización creada y ejecutada con éxito'], 200);
    }

    


    private function updateModel($plantillaName, $fields)
{
    // Obtén la ruta del modelo
    $modelPath = app_path("Models/{$plantillaName}.php");

    // Verifica si el archivo del modelo existe
    if (file_exists($modelPath)) {
        // Lee el contenido actual del modelo
        $currentModelCode = file_get_contents($modelPath);

        // Busca la línea que contiene el array $guarded
        preg_match('/protected\s+\$guarded\s*=\s*\[[^\]]*\];/i', $currentModelCode, $matchesGuarded);

        // Busca la línea que contiene el array $fieldTypes
        preg_match('/public\s+static\s+\$fieldTypes\s*=\s*\[[^\]]*\];/i', $currentModelCode, $matchesFieldTypes);

        // Busca la línea que contiene el array $requiredFields
        preg_match('/public\s+static\s+\$requiredFields\s*=\s*\[[^\]]*\];/i', $currentModelCode, $matchesRequiredFields);

        // Busca la línea que contiene el array $fieldAliases
        preg_match('/public\s+static\s+\$fieldAliases\s*=\s*\[[^\]]*\];/i', $currentModelCode, $matchesFieldAliases);

        if ($matchesGuarded && $matchesFieldTypes && $matchesRequiredFields && $matchesFieldAliases) {
            // Obtiene las líneas encontradas y elimina los caracteres no deseados
            $guardedLine = trim($matchesGuarded[0], " \t\n\r\0\x0B");
            $fieldTypesLine = trim($matchesFieldTypes[0], " \t\n\r\0\x0B");
            $requiredFieldsLine = trim($matchesRequiredFields[0], " \t\n\r\0\x0B");
            $fieldAliasesLine = trim($matchesFieldAliases[0], " \t\n\r\0\x0B");

            // Construye el nuevo array de campos protegidos
            $newGuarded = "protected \$guarded = ['_id', " . implode(', ', array_map(function ($field) {
                return "'{$field['name']}'";
            }, $fields)) . "];";

            // Construye el nuevo array de tipos de campos
            $newFieldTypes = "public static \$fieldTypes = [\n            " . implode(', ', array_map(function ($field) {
                return "'{$field['name']}' => '{$field['type']}'";
            }, $fields)) . ",\n        ];";

            // Construye el nuevo array de campos obligatorios
            $requiredFields = array_filter($fields, function ($field) {
                return isset($field['required']) && $field['required'];
            });

            $newRequired = "public static \$requiredFields = ['" . implode("', '", array_map(function ($field) {
                return $field['name'];
            }, $requiredFields)) . "'];";

            // Construye el nuevo array de alias para los campos
            $newAliases = "public static \$fieldAliases = [\n            " . implode(', ', array_map(function ($field) {
                // Usa title_case para generar alias más legibles
                $alias = Str::title(str_replace('_', ' ', $field['name']));
                return "'{$field['name']}' => '{$alias}'";
            }, $fields)) . ",\n        ];";

            // Reemplaza el array de campos protegidos en el código actual del modelo
            $newModelCode = str_replace($guardedLine, $newGuarded, $currentModelCode);

            // Reemplaza el array de tipos de campos en el código actual del modelo
            $newModelCode = str_replace($fieldTypesLine, $newFieldTypes, $newModelCode);

            // Reemplaza el array de campos obligatorios en el código actual del modelo
            $newModelCode = str_replace($requiredFieldsLine, $newRequired, $newModelCode);

            // Reemplaza el array de alias para los campos en el código actual del modelo
            $newModelCode = str_replace($fieldAliasesLine, $newAliases, $newModelCode);

            // Guarda el nuevo código en el archivo del modelo
            file_put_contents($modelPath, $newModelCode);
        }
    }
}



}
 
