<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AcervoFotográfico extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'AcervoFotográfico_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Fondo', 'Sección', 'Serie', 'Número de expediente', 'Título del expediente', 'Descripción', 'Año', 'Fechas extremas', 'Número de caja', 'Fotos por expediente', 'Estante', 'Anaquel', 'Charola', 'Recurso Digital', ];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
        'Fondo' => 'string', 'Sección' => 'string', 'Serie' => 'string', 'Número de expediente' => 'number', 'Título del expediente' => 'string', 'Descripción' => 'string', 'Año' => 'number', 'Fechas extremas' => 'string', 'Número de caja' => 'number', 'Fotos por expediente' => 'number', 'Estante' => 'string', 'Anaquel' => 'string', 'Charola' => 'string', 'Recurso Digital' => 'file', 
    ];

    // Define alias para los campos
    public static $fieldAliases = [
        'Fondo' => 'Fondo', 'Sección' => 'Sección', 'Serie' => 'Serie', 'Número de expediente' => 'Número De Expediente', 'Título del expediente' => 'Título Del Expediente', 'Descripción' => 'Descripción', 'Año' => 'Año', 'Fechas extremas' => 'Fechas Extremas', 'Número de caja' => 'Número De Caja', 'Fotos por expediente' => 'Fotos Por Expediente', 'Estante' => 'Estante', 'Anaquel' => 'Anaquel', 'Charola' => 'Charola', 'Recurso Digital' => 'Recurso Digital', 
    ];

    // Define los campos obligatorios
    public static $requiredFields = [
        'Fondo', 'Sección', 'Serie', 'Número de expediente', 'Título del expediente', 'Descripción', 'Año', 'Número de caja', 'Recurso Digital', 
    ];

    // Define el nombre con espacios
    public static $title = 'Acervo Fotográfico'; // Nombre original de la plantilla con espacios

}