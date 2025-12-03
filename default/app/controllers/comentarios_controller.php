<?php
/**
 * Controlador de Comentarios
 */
class ComentariosController extends AppController
{
    public function initialize()
    {
        // Llamar al initialize del padre
        parent::initialize();

        // Solo admins pueden gestionar comentarios
        Auth::requireAdmin();
    }

    public function index()
    {
        // Obtener parámetros de filtro
        $busqueda = Input::get('busqueda');
        $reporte_id = Input::get('reporte_id');
        $fecha_desde = Input::get('fecha_desde');
        $fecha_hasta = Input::get('fecha_hasta');

        // Construir condiciones de filtro
        $conditions = [];

        if ($busqueda) {
            $busqueda_safe = addslashes($busqueda);
            $conditions[] = "texto LIKE '%{$busqueda_safe}%'";
        }

        if ($reporte_id) {
            $conditions[] = "reporte_id = " . (int) $reporte_id;
        }

        if ($fecha_desde) {
            $conditions[] = "DATE(created_at) >= '" . date('Y-m-d', strtotime($fecha_desde)) . "'";
        }

        if ($fecha_hasta) {
            $conditions[] = "DATE(created_at) <= '" . date('Y-m-d', strtotime($fecha_hasta)) . "'";
        }

        // Construir query
        $where = count($conditions) > 0 ? "conditions: " . implode(" AND ", $conditions) : "";
        $order = "order: created_at DESC";

        // Obtener comentarios filtrados
        if ($where) {
            $comentarios_raw = (new Comentarios())->find($where, $order);
        } else {
            $comentarios_raw = (new Comentarios())->find($order);
        }

        // Cargar las relaciones manualmente si es necesario
        $this->comentarios = [];
        foreach ($comentarios_raw as $comentario) {
            $this->comentarios[] = $comentario;
        }

        // Pasar valores de filtros actuales a la vista
        $this->filtros = [
            'busqueda' => $busqueda,
            'reporte_id' => $reporte_id,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta
        ];

        $this->title = 'Gestión de Comentarios';
        $this->subtitle = 'Todos los comentarios';
    }

    public function crear($reporte_id = null)
    {
        // Requiere autenticación para crear comentarios
        Auth::require();

        View::select(null, null);

        if (Input::hasPost('comentario')) {

            $data = Input::post('comentario');
            $data['reporte_id'] = (int) ($reporte_id ?: ($data['reporte_id'] ?? 0));
            $data['usuario_id'] = Auth::id(); // Usuario autenticado
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
