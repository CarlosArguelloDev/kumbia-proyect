<?php
class ComentariosController extends AppController
{

    public function crear($reporte_id = null)
    {
        View::select(null, null);

        if (Input::hasPost('comentario')) {

            $data = Input::post('comentario');
            $data['reporte_id'] = (int)($reporte_id ?: ($data['reporte_id'] ?? 0));
            $data['usuario_id'] = 1;
            $data['publico']    = 1;
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

}
