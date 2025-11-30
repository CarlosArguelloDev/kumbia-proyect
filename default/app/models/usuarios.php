<?php
class Usuarios extends ActiveRecord
{
    public function initialize()
    {
        $this->belongs_to('roles');
    }
}