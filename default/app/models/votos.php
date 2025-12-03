<?php
class Votos extends ActiveRecord
{
    public function initialize()
    {
        $this->belongs_to('reportes');
        $this->belongs_to('usuarios');
    }
}
