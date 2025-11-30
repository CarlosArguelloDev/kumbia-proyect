<?php
/**
 * Controlador de Comentarios
 */
class ComentariosController extends AppController
{
    public function index()
    {
        // Obtener comentarios con orden descendente
        $comentarios_raw = (new Comentarios())->find("order: created_at DESC");

        // Cargar las relaciones manualmente si es necesario
        $this->comentarios = [];
        foreach ($comentarios_raw as $comentario) {
            $this->comentarios[] = $comentario;
        }

        $this->title = 'Gestión de Comentarios';
        $this->subtitle = 'Todos los comentarios';
    }

    public function crear($reporte_id = null)
    {
        View::select(null, null);

        if (Input::hasPost('comentario')) {

            $data = Input::post('comentario');
            $data['reporte_id'] = (int) ($reporte_id ?: ($data['reporte_id'] ?? 0));
            $data['usuario_id'] = 1;
            $data['publico'] = 1;
            $comentario = new Comentarios($data);
            if ($comentario->create()) {
                Flash::valid('Comentario agregado');
            } else {
                Flash::error('No se pudo guardar el comentario');
            }
            return Redirect::to("reportes/index");
        }

        return Redirect::to("reportes/index");
    }

    public function eliminar($id)
    {
        $comentario = (new Comentarios())->find_first((int) $id);

        if (!$comentario) {
            Flash::error('Comentario no encontrado');
            return Redirect::to('comentarios');
        }

        if ($comentario->delete()) {
            Flash::valid('Comentario eliminado correctamente');
        } else {
            Flash::error('No se pudo eliminar el comentario');
        }

        return Redirect::to('comentarios');
    }
}
