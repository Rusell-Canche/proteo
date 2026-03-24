<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AcervoCartográfico extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'AcervoCartográfico_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Nomenclatura', 'Año', 'Título', 'Autor', 'Fuente', 'Escala', 'Medidas', 'Recurso Digital', ];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
        'Nomenclatura' => 'string', 'Año' => 'number', 'Título' => 'string', 'Autor' => 'string', 'Fuente' => 'string', 'Escala' => 'number', 'Medidas' => 'number', 'Recurso Digital' => 'file', 
    ];

    // Define alias para los campos
    public static $fieldAliases = [
        'Nomenclatura' => 'Nomenclatura', 'Año' => 'Año', 'Título' => 'Título', 'Autor' => 'Autor', 'Fuente' => 'Fuente', 'Escala' => 'Escala', 'Medidas' => 'Medidas', 'Recurso Digital' => 'Recurso Digital', 
    ];

    // Define los campos obligatorios
    public static $requiredFields = [
        'Nomenclatura', 'Título', 'Medidas', 'Recurso Digital', 
    ];

    // Define el nombre con espacios
    public static $title = 'Acervo Cartográfico'; // Nombre original de la plantilla con espacios

}