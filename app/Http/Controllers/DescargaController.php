<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DescargaController extends Controller
{
    public function descargarConMarcaAgua($plantillaName, $documentId)
    {
        // Obtiene el documento de la colección
        $document = DB::connection('mongodb')->collection($plantillaName)->find($documentId);

        if (!$document) {
            return response()->json(['error' => "Documento no encontrado"], 404);
        }

        // Ruta donde se almacenan los archivos en tu servidor
        $archivosPath = storage_path('app/public/');
        $zipFileName = 'recursos_digitales_marca_de_agua.zip';

        // Crea un archivo ZIP
        $zip = new ZipArchive;
        if ($zip->open($archivosPath . $zipFileName, ZipArchive::CREATE) === true) {

            // Ruta de la marca de agua
            $marcaDeAguaPath = public_path('/assets/images/tu-marca-de-agua.png');

            // Verifica si la marca de agua existe
            if (!file_exists($marcaDeAguaPath)) {
                return response()->json(['error' => 'Marca de agua no encontrada'], 404);
            }

            // Itera sobre cada archivo en el documento y aplica la marca de agua
            foreach ($document['Recurso Digital'] as $index => $archivo) {
                $archivoPath = $archivosPath . $archivo;

                // Verifica si el archivo existe
                if (file_exists($archivoPath)) {
                    // Carga la imagen principal usando Intervention Image
                    $imagenConMarcaAgua = Image::make($archivoPath);

                    // Carga la marca de agua
                    $marcaDeAgua = Image::make($marcaDeAguaPath);

                    // Ajusta el tamaño de la marca de agua al tamaño de la imagen principal
                    $marcaDeAgua->fit($imagenConMarcaAgua->width(), $imagenConMarcaAgua->height());

                    // Calcula la posición central para la marca de agua
                    $posicionX = intval(($imagenConMarcaAgua->width() - $marcaDeAgua->width()) / 2);
                    $posicionY = intval(($imagenConMarcaAgua->height() - $marcaDeAgua->height()) / 2);

                    // Aplica la marca de agua a la imagen
                    $imagenConMarcaAgua->insert($marcaDeAgua, 'top-left', $posicionX, $posicionY);

                    // Guarda la imagen con marca de agua en el archivo ZIP
                    $zip->addFromString("recurso_digital_marca_de_agua_$index.png", $imagenConMarcaAgua->encode());
                } else {
                    // Log o manejo de error indicando que el archivo no existe
                    Log::error("El archivo $archivo no existe en la ruta $archivoPath");
                }
            }

            // Cierra el archivo ZIP
            $zip->close();

            // Devuelve el archivo ZIP al cliente
            return response()->download($archivosPath . $zipFileName);
        }

        return response()->json(['error' => 'Error al crear el archivo ZIP'], 500);
    }
}
