<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DiarioOficial extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'DiarioOficial_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Día', 'Mes', 'Año', 'Tipo Publicación', 'Número', 'Tomo', 'Epoca', 'Ocr', 'Recurso Digital', ];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
        'Día' => 'number', 'Mes' => 'string', 'Año' => 'number', 'Tipo Publicación' => 'string', 'Número' => 'number', 'Tomo' => 'string', 'Epoca' => 'string', 'Ocr' => 'string', 'Recurso Digital' => 'file', 
    ];

    // Define alias para los campos
    public static $fieldAliases = [
        'Día' => 'Día', 'Mes' => 'Mes', 'Año' => 'Año', 'Tipo Publicación' => 'Tipo Publicación', 'Número' => 'Número', 'Tomo' => 'Tomo', 'Epoca' => 'Epoca',  'Ocr' => 'Ocr', 'Recurso Digital' => 'Recurso Digital', 
    ];

    // Define los campos obligatorios
    public static $requiredFields = [
        
    ];

    // Define el nombre con espacios
    public static $title = 'Diario Oficial'; // Nombre original de la plantilla con espacios

}