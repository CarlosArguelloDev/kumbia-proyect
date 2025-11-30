<?php
/**
 * Controlador de Zonas
 */
class ZonasController extends AppController
{
    public function index()
    {
        // Obtener todos los reportes con sus coordenadas y prioridades
        $this->reportes = (new Reportes())->find("order: created_at DESC");

        $this->title = 'Zonas de San Juan del Río';
        $this->subtitle = 'Visualización de reportes por zona';
    }
}
