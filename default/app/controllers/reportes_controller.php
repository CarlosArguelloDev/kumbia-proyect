<?php
class ReportesController extends AppController{

    public function index(){
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
        $this->reporte  = new Reportes();
        $this->title    = 'Reportes';
        $this->subtitle = 'Registrar Reporte';

        if (Input::hasPost("reporte")) {

            $params  = Input::post("reporte");
            $reporte = new Reportes($params);

            if ($reporte->create()) {
                Flash::valid('El reporte se registró correctamente');
            } else {
                Flash::error('No se pudo registrar el reporte');
            }
        }
    }
}
