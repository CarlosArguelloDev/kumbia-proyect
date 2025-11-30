<?php
Class Reportes extends ActiveRecord{
    public function initialize()
    {
        $this->belongs_to('usuarios', 'model: Usuarios', 'fk: usuario_id');
        $this->belongs_to('prioridades', 'model: Prioridades', 'fk: prioridad_id');
        $this->belongs_to('estados', 'model: Estados', 'fk: estado_id');
        $this->has_many('comentarios', 'model: Comentarios', 'fk: reporte_id');
        $this->has_many('votos', 'model: Votos', 'fk: reporte_id');

    }
}