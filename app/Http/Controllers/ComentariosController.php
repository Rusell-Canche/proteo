<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\NuevoComentario;
use App\Models\User;

class ComentariosController extends Controller
{

    public function store(Request $request)
    {
        // Validar la solicitud
        log::info($request);
        $request->validate([
            'usuario_id' => 'required|exists:users,_id',
            'documento_id' => 'required', 
            'contenido' => 'required',
        ]);

        // Crear el comentario
        $comentario = Comentario::create([
            'usuario_id' => $request->input('usuario_id'),
            'documento_id' => $request->input('documento_id'),
            'contenido' => $request->input('contenido'),
            'estado' => 'pendiente', 
        ]);

        // Enviar correo a usuarios con roles Validador o Admin
        //$usuariosConRol = User::whereIn('roles', ['validador', 'administrador'])->get();
        //$destinatarios = $usuariosConRol->pluck('email')->toArray();


        //Mail::to($destinatarios)->send(new NuevoComentario($comentario));

        // Puedes enviar una respuesta JSON, redirigir a otra página, etc.
        return response()->json(['message' => 'Comentario creado con éxito', 'comentario' => $comentario], 201);
    }




    public function comentariosAprobados($documentoId)
    {
        // Obtener comentarios aprobados con información del usuario
        $comentariosAprobados = Comentario::with('usuario')
            ->where('documento_id', $documentoId)
            ->where('estado', 'aprobado')
            ->get();

        log::info($comentariosAprobados);
        return response()->json(['comentariosAprobados' => $comentariosAprobados]);
    }



    
    public function aprobarComentario($id)
    {
        // Aprobar un comentario
        $comentario = Comentario::find($id);

        if (!$comentario) {
            return response()->json(['message' => 'Comentario no encontrado'], 404);
        }

        $comentario->estado = 'aprobado';
        $comentario->save();

        return response()->json(['message' => 'Comentario aprobado con éxito']);
    }

    public function denegarComentario($id)
    {
        // Denegar y eliminar un comentario
        $comentario = Comentario::find($id);

        if (!$comentario) {
            return response()->json(['message' => 'Comentario no encontrado'], 404);
        }

        $comentario->delete();

        return response()->json(['message' => 'Comentario denegado y eliminado con éxito']);
    }

    public function obtenerComentariosPendientes()
    {
        // Obtener comentarios pendientes con información del usuario y del documento
        $comentariosPendientes = Comentario::with(['usuario'])
            ->where('estado', 'pendiente')
            ->get();

        // Obtener detalles del documento para cada comentario
        foreach ($comentariosPendientes as $comentario) {
            $documento = $comentario->obtenerDocumento();

            // Asignar la información del documento como un atributo adicional
            $comentario->setAttribute('documento', $documento);
        }

        log::info($comentariosPendientes);
        return response()->json(['comentariosPendientes' => $comentariosPendientes]);
    }

    public function obtenerComentariosAprobados()
    {
        // Obtener comentarios aprobados con información del usuario y del documento
        $comentariosAprobados = Comentario::with(['usuario'])
            ->where('estado', 'aprobado')
            ->get();

        foreach ($comentariosAprobados as $comentario) {
            $documento = $comentario->obtenerDocumento();

            // Asignar la información del documento como un atributo adicional
            $comentario->setAttribute('documento', $documento);
        }

        return response()->json(['comentariosAprobados' => $comentariosAprobados]);
    }}
