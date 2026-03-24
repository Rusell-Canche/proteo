<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

class PdfController extends Controller
{
    public function extraerTextoPDF($rutaPDF)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($rutaPDF);
        $texto = $pdf->getText();

        return $texto;
    }

    public function procesarPDF(Request $request)
    {
        // Validar que venga un archivo PDF
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);

        // Guardar temporalmente el archivo
        $archivo = $request->file('pdf');
        $ruta = $archivo->store('pdfs');

        // Obtener la ruta absoluta
        $rutaCompleta = storage_path('app/'.$ruta);

        // Extraer texto
        $texto = $this->extraerTextoPDF($rutaCompleta);

        // Devolver JSON con el texto
        return response()->json([
            'texto_extraido' => $texto,
        ]);
    }
}
