<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ExpedientesHistóricos extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'ExpedientesHistóricos_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Fondo', 'Sección', 'Serie', 'Número de expediente', 'Título de expediente', 'Descripción', 'Fechas extremas', 'Número de caja', 'Fojas', 'Estante', 'Anaquel', 'Charola', 'Recurso Digital', ];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
        'Fondo' => 'string', 'Sección' => 'string', 'Serie' => 'string', 'Número de expediente' => 'number', 'Título de expediente' => 'string', 'Descripción' => 'string', 'Fechas extremas' => 'string', 'Número de caja' => 'number', 'Fojas' => 'number', 'Estante' => 'string', 'Anaquel' => 'string', 'Charola' => 'string', 'Recurso Digital' => 'file', 
    ];

    // Define alias para los campos
    public static $fieldAliases = [
        'Fondo' => 'Fondo', 'Sección' => 'Sección', 'Serie' => 'Serie', 'Número de expediente' => 'Número De Expediente', 'Título de expediente' => 'Título De Expediente', 'Descripción' => 'Descripción', 'Fechas extremas' => 'Fechas Extremas', 'Número de caja' => 'Número De Caja', 'Fojas' => 'Fojas', 'Estante' => 'Estante', 'Anaquel' => 'Anaquel', 'Charola' => 'Charola', 'Recurso Digital' => 'Recurso Digital', 
    ];

    // Define los campos obligatorios
    public static $requiredFields = [
        'Fondo', 
    ];

    // Define el nombre con espacios
    public static $title = 'Expedientes Históricos'; // Nombre original de la plantilla con espacios

}