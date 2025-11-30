<?php
class ReportesController extends AppController
{
    public function index()
    {
        $this->reportes = (new Reportes())->find();
        $this->title = 'Reportes';
        $this->subtitle = 'Listado de baches';
    }

    public function atendidos()
    {
        // Estado resuelto = id 3
        $this->reportes = (new Reportes())->find("conditions: estado_id = 3", "order: id desc");
        $this->title = 'Reportes Atendidos';
        $this->subtitle = 'Listado de baches';
    }

    public function registrar()
    {
        // Requiere estar autenticado
        Auth::require();

        $this->reporte = new Reportes();
        $this->title = 'Reportes';
        $this->subtitle = 'Registrar Reporte';

        if (Input::hasPost("reporte")) {
            $params = Input::post("reporte");
            $reporte = new Reportes($params);

            $reporte->usuario_id = Auth::id(); // Usuario autenticado
            $reporte->estado_id = 1;
            $reporte->prioridad_id = 1;

            if ($reporte->create()) {
                if (!empty($_FILES["foto"]["name"])) {
                    $tmp = $_FILES["foto"]["tmp_name"];
                    $dest = dirname(APP_PATH) . "/public/storage/reportes/{$reporte->id}.jpg";

                    $dir = dirname($dest);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    move_uploaded_file($tmp, $dest);
                }

                Flash::valid('El reporte se registró correctamente');
                return Redirect::to("reportes/index");
            } else {
                Flash::error('No se pudo registrar el reporte');
            }
        }
    }

    public function ver($id)
    {
        $this->reporte = (new Reportes())->find_first((int) $id);
        $this->comentarios = (new Comentarios())->find("conditions: reporte_id = $id", "order: created_at DESC");
        $this->title = 'Ver Reporte';
        $this->subtitle = $this->reporte ? $this->reporte->titulo : 'Detalle del reporte';
    }

    public function editar($id)
    {
        // Requiere autenticación
        Auth::require();

        $this->reporte = (new Reportes())->find_first((int) $id);

        if (!$this->reporte) {
            Flash::error('No se encontró el reporte');
            return Redirect::to("reportes/index");
        }

        // Solo el dueño o un admin pueden editar
        if ($this->reporte->usuario_id != Auth::id() && !Auth::isAdmin()) {
            Flash::error('No tienes permisos para editar este reporte');
            return Redirect::to("reportes/index");
        }

        $this->title = 'Reportes';
        $this->subtitle = 'Editar Reporte';

        if (Input::hasPost("reporte")) {
            $params = Input::post("reporte");

            // Manejar la foto ANTES de actualizar
            if (!empty($_FILES["foto"]["name"])) {
                $tmp = $_FILES["foto"]["tmp_name"];
                $dest = dirname(APP_PATH) . "/public/storage/reportes/{$this->reporte->id}.jpg";

                $dir = dirname($dest);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                // Eliminar foto anterior si existe
                if (file_exists($dest)) {
                    unlink($dest);
                }

                move_uploaded_file($tmp, $dest);
            }

            if ($this->reporte->update($params)) {
                Flash::valid('El reporte se actualizó correctamente');
                return Redirect::to("reportes/index");
            } else {
                Flash::error('No se pudo actualizar el reporte');
            }
        }
    }

    public function mis_reportes()
    {
        // Requiere autenticación
        Auth::require();

        // Obtener reportes del usuario autenticado
        $usuario_id = Auth::id();

        $this->reportes = (new Reportes())->find("conditions: usuario_id = $usuario_id", "order: created_at DESC");
        $this->title = 'Mis Reportes';
        $this->subtitle = 'Gestiona tus reportes';
    }

    public function eliminar($id)
    {
        $reporte = (new Reportes())->find_first((int) $id);

        if (!$reporte) {
            Flash::error('No se encontró el reporte');
            return Redirect::to("reportes/mis_reportes");
        }

        if ($reporte->delete()) {
            // Eliminar foto asociada si existe
            $foto_path = dirname(APP_PATH) . "/public/storage/reportes/{$reporte->id}.jpg";
            if (file_exists($foto_path)) {
                unlink($foto_path);
            }

            Flash::valid('El reporte se eliminó correctamente');
        } else {
            Flash::error('No se pudo eliminar el reporte');
        }

        return Redirect::to("reportes/mis_reportes");
    }

    /**
     * Vista de administración de reportes
     */
    public function admin()
    {
        // Solo administradores pueden gestionar reportes
        Auth::requireAdmin();

        $this->reportes = (new Reportes())->find("order: created_at DESC");
        $this->estados = (new Estados())->find();
        $this->prioridades = (new Prioridades())->find();
        $this->title = 'Gestión de Reportes';
        $this->subtitle = 'Panel de Administración';
    }

    /**
     * Cambiar estado de un reporte
     */
    public function cambiar_estado($id)
    {
        $reporte = (new Reportes())->find_first((int) $id);

        if (!$reporte) {
            Flash::error('No se encontró el reporte');
            return Redirect::to("reportes/admin");
        }

        if (Input::hasPost('estado_id')) {
            $reporte->estado_id = Input::post('estado_id');

            if ($reporte->update()) {
                Flash::valid('Estado actualizado correctamente');
            } else {
                Flash::error('No se pudo actualizar el estado');
            }
        }

        return Redirect::to("reportes/admin");
    }

    /**
     * Cambiar prioridad de un reporte
     */
    public function cambiar_prioridad($id)
    {
        $reporte = (new Reportes())->find_first((int) $id);

        if (!$reporte) {
            Flash::error('No se encontró el reporte');
            return Redirect::to("reportes/admin");
        }

        if (Input::hasPost('prioridad_id')) {
            $reporte->prioridad_id = Input::post('prioridad_id');

            if ($reporte->update()) {
                Flash::valid('Prioridad actualizada correctamente');
            } else {
                Flash::error('No se pudo actualizar la prioridad');
            }
        }

        return Redirect::to("reportes/admin");
    }
}
