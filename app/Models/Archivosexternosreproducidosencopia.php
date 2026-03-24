<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Archivosexternosreproducidosencopia extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'Archivosexternosreproducidosencopia_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Archivo de procedencia', 'Sección', 'Número de expediente', 'Número de caja', 'Fechas extremas', 'Fojas', 'Descripción', 'Estante', 'Anquel', 'Charola', 'Recurso Digital'];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
            'Archivo de procedencia' => 'string', 'Sección' => 'string', 'Número de expediente' => 'number', 'Número de caja' => 'number', 'Fechas extremas' => 'string', 'Fojas' => 'number', 'Descripción' => 'string', 'Estante' => 'string', 'Anquel' => 'string', 'Charola' => 'string', 'Recurso Digital' => 'file',
        ];

    // Define alias para los campos
    public static $fieldAliases = [
            'Archivo de procedencia' => 'Archivo De Procedencia', 'Sección' => 'Sección', 'Número de expediente' => 'Número De Expediente', 'Número de caja' => 'Número De Caja', 'Fechas extremas' => 'Fechas Extremas', 'Fojas' => 'Fojas', 'Descripción' => 'Descripción', 'Estante' => 'Estante', 'Anquel' => 'Anquel', 'Charola' => 'Charola', 'Recurso Digital' => 'Recurso Digital',
        ];

    // Define los campos obligatorios
    public static $requiredFields = ['Archivo de procedencia', 'Número de expediente', 'Fojas', 'Descripción'];

    // Define el nombre con espacios
    public static $title = 'Archivos externos reproducidos en copia'; // Nombre original de la plantilla con espacios

}