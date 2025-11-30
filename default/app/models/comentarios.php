<?php
class Comentarios extends ActiveRecord {

    public function initialize() {
        $this->belongs_to('reportes', 'model: Reportes', 'fk: reporte_id');
        $this->belongs_to('usuarios', 'model: Usuarios', 'fk: usuario_id');
    }

}