<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MongoDB\Client;  // Asegúrate de importar el cliente de MongoDB
use Illuminate\Support\Facades\Validator;
use ReflectionClass; // Importa ReflectionClass
use Illuminate\Support\Str; // Asegúrate de importar la clase Str
use setasign\Fpdi\TcpdfFpdi;
use setasign\Fpdi\Tcpdf\Fpdi;
use Smalot\PdfParser\Parser;



class DocumentoController extends Controller
{

    public function getPendientes()
    {
        // Obtener el cliente MongoDB
        $client = DB::connection('mongodb')->getMongoClient();

        // Obtener la lista de bases de datos
        $databases = $client->listDatabases();

        // Filtrar las colecciones que tienen el sufijo '_pendientes'
        $pendientesCollections = [];

        // Iterar sobre las bases de datos
        foreach ($databases as $database) {
            $db = $client->selectDatabase($database->getName());
            $collections = $db->listCollections(); // Obtener colecciones de cada base de datos

            // Iterar sobre las colecciones
            foreach ($collections as $collection) {
                $collectionName = $collection->getName();  // Obtener el nombre de la colección

                // Filtra las colecciones que terminan en '_pendientes'
                if (str_ends_with($collectionName, '_pendientes')) {
                    // Obtener los documentos de esa colección pendiente
                    $documents = DB::connection('mongodb')->collection($collectionName)->get();

                    // Agregar los documentos de esa colección al array
                    $pendientesCollections[] = [
                        'collection' => $collectionName,
                        'documents' => $documents
                    ];
                }
            }
        }

        // Retornar las colecciones pendientes
        return response()->json($pendientesCollections);
    }



    public function getDocumentbyid($plantillaName, $documentId)
    {
        // Obtiene un documento específico de la colección
        $document = DB::connection('mongodb')->collection($plantillaName)->find($documentId);

        if ($document) {
            return response()->json($document);
        } else {
            return response()->json(['error' => "Documento no encontrado"], 404);
        }
    }


    public function getforDocuments()
    {
        $collections = DB::connection('mongodb')->listCollections();

        $plantillas = [];

        foreach ($collections as $collection) {
            $plantillas[] = $collection->getName();
        }

        return response()->json($plantillas);
    }



    public function getAllDocuments($plantillaName)
    {
        // Verifica si la colección existe
        $collections = DB::connection('mongodb')->listCollections();
        $collectionExists = false;

        foreach ($collections as $collection) {
            if ($collection->getName() === $plantillaName) {
                $collectionExists = true;
                break;
            }
        }

        if ($collectionExists) {
            // Obtiene todos los documentos de la colección
            $documents = DB::connection('mongodb')->collection($plantillaName)->get();

            return response()->json($documents);
        } else {
            return response()->json(['error' => "La colección '{$plantillaName}' no existe."], 404);
        }
    }


    public function storeDocument(Request $request, $plantillaName)
    {
        $request->validate([
            'document_data' => 'required|array',
            'files.*' => 'nullable|file|mimes:pdf,jpg,png,mp4,mp3,wav,gif,tiff|max:20480',
        ]);
    
        $documentData = $request->input('document_data');
        $uploadedFilesWatermarked = [];
        $uploadedFilesOriginal = [];
    
        if ($request->hasFile('files')) {
            $files = $request->file('files');
    
            foreach ($files as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $uniqueName = uniqid();
                $filename = $uniqueName . '.' . $extension;
                $originalFilename = $uniqueName . '_original.' . $extension;
    
                // Guardar archivo con marca de agua (se sobrescribe después de aplicarla)
                $path = $file->storeAs('uploads', $filename, 'public');
                $fullPath = public_path("storage/{$path}");
    
                // Copiar archivo original con sufijo _original
                $originalPath = 'uploads/' . $originalFilename;
                $originalFullPath = public_path("storage/{$originalPath}");
                copy($fullPath, $originalFullPath);
    
                // Aplicar marca de agua sobre la versión principal
                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    $this->agregarMarcaDeAgua($fullPath, $extension);
                } elseif ($extension === 'pdf') {
                    $this->agregarMarcaDeAguaPDF($fullPath);
                }
    
                // Guardar rutas
                $uploadedFilesWatermarked[] = $path;
                $uploadedFilesOriginal[] = $originalPath;
            }
    
            if (count($uploadedFilesWatermarked) > 10) {
                return response()->json(['error' => 'No puedes subir más de 10 archivos.'], 400);
            }
    
            $documentData['Recurso Digital'] = $uploadedFilesWatermarked;
        }
    
        if (!is_array($documentData)) {
            $documentData = [$documentData];
        }
    
        $documentData['created_at'] = now();
        $documentData['updated_at'] = now();
    
        // Guardar en colección _pendientes (con marca de agua)
        $collectionPendientes = $plantillaName . '_pendientes';
        DB::connection('mongodb')->collection($collectionPendientes)->insert($documentData);
    
        // Guardar en colección _originales (con archivos originales)
        if (!empty($uploadedFilesOriginal)) {
            $originalData = $documentData;
            $originalData['Recurso Digital'] = $uploadedFilesOriginal;
    
            $collectionOriginales = $plantillaName . '_originales';
            DB::connection('mongodb')->collection($collectionOriginales)->insert($originalData);
        }
    
        return response()->json(['message' => 'Documento guardado con éxito'], 201);
    }
    
private function agregarMarcaDeAgua($imagePath, $extension)
{
    $fontPath = public_path('fonts/alfa.ttf'); // Asegúrate que exista
    $image = null;

    if ($extension === 'jpg' || $extension === 'jpeg') {
        $image = imagecreatefromjpeg($imagePath);
    } elseif ($extension === 'png') {
        $image = imagecreatefrompng($imagePath);
    }

    if (!$image) return;

    $color = imagecolorallocatealpha($image, 128, 0, 32, 75); 
    $text = "AGQROO";
    $angle = 45;
    $fontSize = 30;

    $width = imagesx($image);
    $height = imagesy($image);

    // Repetir marca cada 200px en X y Y
    for ($x = -$width; $x < $width * 2; $x += 200) {
        for ($y = -$height; $y < $height * 2; $y += 200) {
            imagettftext($image, $fontSize, $angle, $x, $y, $color, $fontPath, $text);
        }
    }

    if ($extension === 'jpg' || $extension === 'jpeg') {
        imagejpeg($image, $imagePath, 90);
    } elseif ($extension === 'png') {
        imagepng($image, $imagePath);
    }

    imagedestroy($image);
}

private function agregarMarcaDeAguaPDF($pdfPath)
{
    $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('AGQROO');
    $pdf->SetTitle('Documento con Marca de Agua');
    $pdf->SetAutoPageBreak(false); // Evita saltos de página automáticos

    $pageCount = $pdf->setSourceFile($pdfPath);
    $outputPath = $pdfPath;

    // Configurar fuente y color
    $pdf->SetFont('Helvetica', 'B', 15); // Tamaño reducido
    $pdf->SetTextColor(128, 0, 32); // Rojo vino
    $text = 'AGQROO'; // Marca de agua

    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($templateId);

        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);

        $pdf->SetAlpha(0.3); // Transparencia

        $textWidth = $pdf->GetStringWidth($text);
        $lineHeight = 10; // Altura entre líneas de texto

        // Espaciado para no saturar
        $stepX = $textWidth * 2;
        $stepY = $lineHeight * 2.5;

        for ($x = 0; $x < $size['width']; $x += $stepX) {
            for ($y = 0; $y < $size['height']; $y += $stepY) {
                $pdf->SetXY($x, $y);
                $pdf->Text($x, $y, $text);
            }
        }

        $pdf->SetAlpha(1); // Restaurar opacidad para siguientes elementos si se agregaran
    }

    $pdf->Output($outputPath, 'F');
}





    public function deleteDocument($plantillaName, $documentId)
    {
        // Obtener el documento de la colección MongoDB
        $documento = DB::connection('mongodb')->collection($plantillaName)->where('_id', $documentId)->first();

        if ($documento) {
            // Verificar si el documento tiene un archivo asociado y eliminarlo
            if (isset($documento['Recurso Digital']) && is_array($documento['Recurso Digital'])) {
                foreach ($documento['Recurso Digital'] as $filePath) {
                    // Asegurarse de que el archivo no tiene el prefijo "uploads/"
                    if (strpos($filePath, 'uploads/') === 0) {
                        $filePath = substr($filePath, strlen('uploads/'));
                    }

                    // Obtener la ruta relativa correcta al archivo en el almacenamiento público
                    $relativePath = 'uploads/' . $filePath;

                    // Verificar si el archivo existe en el almacenamiento local
                    if (Storage::disk('public')->exists($relativePath)) {
                        // Intentar eliminar el archivo del almacenamiento local
                        try {
                            Storage::disk('public')->delete($relativePath);
                            Log::info('Archivo eliminado: ' . $relativePath);
                        } catch (\Exception $e) {
                            Log::error('Error al eliminar archivo: ' . $relativePath . '. Error: ' . $e->getMessage());
                        }
                    } else {
                        Log::warning('Archivo no encontrado en almacenamiento local: ' . $relativePath);
                    }
                }
            }

            // Eliminar el documento de la colección MongoDB
            $result = DB::connection('mongodb')->collection($plantillaName)->where('_id', $documentId)->delete();

            if ($result) {
                return response()->json(['message' => 'Documento y archivos asociados eliminados con éxito']);
            } else {
                return response()->json(['error' => 'Error al eliminar el documento'], 500);
            }
        } else {
            return response()->json(['error' => 'Documento no encontrado'], 404);
        }
    }

    public function updateDocument(Request $request, $plantillaName, $documentId)
{
    $request->validate([
        'files.*' => 'nullable|file|mimes:pdf,jpg,png,mp4,mp3,wav,gif|max:20480',
        'delete_files' => 'nullable|array',
        'existing_files' => 'nullable|array'
    ]);

    Log::info('Datos recibidos para actualizar:', $request->all());

    $documento = DB::connection('mongodb')->collection($plantillaName)->where('_id', $documentId)->first();

    if (!$documento) {
        return response()->json(['error' => 'Documento no encontrado'], 404);
    }

    $updateData = $request->except(['files', 'delete_files', 'existing_files', '_id']);

    // 🔹 Obtener archivos actuales desde `existing_files` si se envían
    $archivosActuales = $request->input('existing_files', []);

    // 🔹 Manejo de eliminación de archivos
    if ($request->has('delete_files') && isset($documento['Recurso Digital'])) {
        foreach ($request->input('delete_files') as $filePath) {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                Log::info("Archivo eliminado: $filePath");
            }

            $archivosActuales = array_values(array_diff($archivosActuales, [$filePath]));
        }
    }

   // Manejo de nuevos archivos subidos
   if ($request->hasFile('files')) {
    $files = $request->file('files');
    foreach ($files as $file) {
        $filePath = $file->store('uploads', 'public');

        // Asegurarse de no agregar archivos duplicados
        if (!in_array($filePath, $archivosActuales)) {
            $archivosActuales[] = $filePath; // Agregar ruta de archivo al array si no existe ya
        }
    }
}
    // 🔹 Asegurar que solo exista 'Recurso Digital' y no 'Recurso_Digital'
    unset($documento['Recurso_Digital']);

    // Guardar la lista final de archivos
    $updateData['Recurso Digital'] = $archivosActuales;
    $updateData['updated_at'] = Carbon::now()->toDateTimeString();

    DB::connection('mongodb')->collection($plantillaName)
        ->where('_id', $documentId)
        ->update($updateData);

    return response()->json(['message' => 'Documento actualizado con éxito']);
}







public function obtenerUltimosDocumentos()
{
    $ultimosDocumentos = [];

    // Obtén todas las colecciones
    $colecciones = DB::connection('mongodb')->listCollections();

    foreach ($colecciones as $coleccion) {
        $nombreColeccion = $coleccion->getName();

        // Omitir colecciones específicas
        if (
            in_array($nombreColeccion, [
                'noticias_collection', 'carrousel_images', 'navbar_colors',
                'comentarios', 'migrations', 'users', 'personal_access_tokens',
                'plantillas_predeterminadas', 'visitas'
            ]) || str_ends_with($nombreColeccion, '_pendientes')
        ) {
            continue;
        }

        // Obtener el nombre del modelo dinámicamente basado en el nombre de la colección
        $modeloNombre = 'App\\Models\\' . ucfirst(Str::camel($nombreColeccion));

        if (class_exists($modeloNombre)) {
            $modelo = app($modeloNombre);

            // Obtener el valor de la propiedad estática $title utilizando reflexión
            $reflexion = new ReflectionClass($modelo);
            $title = $reflexion->getStaticPropertyValue('title', 'Título no definido');

            // Verificar si la colección tiene documentos
            $conteo = DB::connection('mongodb')->collection($nombreColeccion)->count();

            if ($conteo > 0) {
                // Obtener los últimos 4 documentos de la colección
                $documentos = DB::connection('mongodb')->collection($nombreColeccion)
                    ->orderBy('created_at', 'desc')
                    ->take(4)
                    ->get();

                // Agregar los documentos y el título a la lista final
                $ultimosDocumentos[$nombreColeccion] = [
                    'titulo' => $title,
                    'documentos' => $documentos->toArray()
                ];
            }
        }
    }

    Log::info('Enviado a Vue:', $ultimosDocumentos);

    return response()->json($ultimosDocumentos);
}



    public function approveDocument(Request $request, $collectionName, $documentId)
    {
        // Obtener el cliente MongoDB
        $client = DB::connection('mongodb')->getMongoClient();
        $nombreBaseDatos = 'proteo';  // El nombre de tu base de datos

        // Verificar que el documento ID es una cadena válida
        if (!$documentId) {
            return response()->json(['error' => 'El ID del documento es requerido.'], 400);
        }

        // Obtener el nombre de la colección definitiva sin el sufijo '_pendientes'
        $definitiveCollection = str_replace('_pendientes', '', $collectionName);

        // Buscar el documento en la colección '_pendientes'
        $document = DB::connection('mongodb')->collection($collectionName)->where('_id', $documentId)->first();

        // Si el documento no se encuentra
        if (!$document) {
            return response()->json(['error' => 'Documento no encontrado en la colección pendiente.'], 404);
        }

        // Eliminar el campo '_id' para evitar conflictos con MongoDB
        unset($document['_id']);  // MongoDB asignará un nuevo _id automáticamente

        // Insertar el documento en la colección definitiva (sin el sufijo '_pendientes')
        DB::connection('mongodb')->collection($definitiveCollection)->insert([$document]);

        // Eliminar el documento de la colección '_pendientes' usando delete()
        DB::connection('mongodb')->collection($collectionName)->where('_id', $documentId)->delete();

        return response()->json(['message' => 'Documento aprobado y movido a la colección definitiva.'], 200);
    }



public function storeDiarioOficial(Request $request)
{
    $request->validate([
        'document_data' => 'required|array',
        'files.*' => 'nullable|file|mimes:pdf,jpg,png',
    ]);

    $documentData = $request->input('document_data');
    $uploadedFilesWatermarked = [];
    $uploadedFilesOriginal = [];

    // Procesar archivos
    if ($request->hasFile('files')) {
        $files = $request->file('files');
        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $uniqueName = uniqid();
            $filename = $uniqueName . '.' . $extension;
            $originalFilename = $uniqueName . '_original.' . $extension;

            $path = $file->storeAs('uploads', $filename, 'public');
            $fullPath = public_path("storage/{$path}");

            $originalPath = 'uploads/' . $originalFilename;
            $originalFullPath = public_path("storage/{$originalPath}");
            copy($fullPath, $originalFullPath);

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $this->agregarMarcaDeAgua($fullPath, $extension);
            } elseif ($extension === 'pdf') {
                $this->agregarMarcaDeAguaPDF($fullPath);

                // 🔹 Extraer texto OCR
                $parser = new Parser();
                $pdf = $parser->parseFile($fullPath);
                $text = $pdf->getText() ?? '';

                // 🔹 Limpiar texto para UTF-8 válido
                $documentData['Ocr'] = $this->limpiarUTF8($text);
            }

            $uploadedFilesWatermarked[] = $path;
            $uploadedFilesOriginal[] = $originalPath;
        }
        $documentData['Recurso Digital'] = $uploadedFilesWatermarked;
    }

    if (!is_array($documentData)) {
        $documentData = [$documentData];
    }

    $documentData['created_at'] = now();
    $documentData['updated_at'] = now();

    // Guardar en MongoDB
    DB::connection('mongodb')->collection('DiarioOficial_pendientes')->insert($documentData);

    if (!empty($uploadedFilesOriginal)) {
        $originalData = $documentData;
        $originalData['Recurso Digital'] = $uploadedFilesOriginal;
        DB::connection('mongodb')->collection('DiarioOficial_originales')->insert($originalData);
    }

    return response()->json(['message' => 'Diario Oficial guardado con éxito y OCR generado'], 201);
}

// 🔹 Función para limpiar texto UTF-8 inválido
private function limpiarUTF8($texto)
{
    $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
    $texto = preg_replace('/[^\P{C}\n]+/u', '', $texto);
    return $texto;
}

}
