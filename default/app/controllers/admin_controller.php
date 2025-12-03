<?php
class AdminController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        Auth::requireAdmin();
    }

    // Dashboard de administración
    public function dashboard()
    {
        $this->title = 'Panel de Administración';
        $this->subtitle = 'Dashboard';

        $reportes = new Reportes();
        $this->total_reportes = $reportes->count();
        $this->reportes_pendientes = $reportes->count("estado_id != 3");
        $this->reportes_atendidos = $reportes->count("estado_id = 3");

        $usuarios = new Usuarios();
        $this->total_usuarios = $usuarios->count();

        $comentarios = new Comentarios();
        $this->total_comentarios = $comentarios->count();

        $this->ultimos_reportes = $reportes->find("limit: 5", "order: created_at DESC");

        $this->estados = (new Estados())->find();
        $this->prioridades = (new Prioridades())->find();

        $this->reportes_por_estado = [];
        foreach ($this->estados as $estado) {
            $this->reportes_por_estado[$estado->id] = [
                'nombre' => $estado->nombre,
                'total' => $reportes->count("estado_id = {$estado->id}")
            ];
        }

        $this->reportes_por_prioridad = [];
        foreach ($this->prioridades as $prioridad) {
            $this->reportes_por_prioridad[$prioridad->id] = [
                'nombre' => $prioridad->nombre,
                'total' => $reportes->count("prioridad_id = {$prioridad->id}")
            ];
        }
    }
}
