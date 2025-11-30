<?php
/**
 * Modelo Roles
 */
class Roles extends ActiveRecord
{
    // Relación: Un rol tiene muchos usuarios
    public function initialize()
    {
        $this->has_many('usuarios');
    }
}
