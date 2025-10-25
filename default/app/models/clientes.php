<?php
// models/clientes.php
// se llama igual que la tabla

Class Clientes extends ActiveRecord{
    public function initialize() {
        $this->has_many('compras', 'model: Ventas', 'fk: cliente_id');

        $this->validates_presence_of('cliente.nombre', 'message: El nombre es requerido');
        $this->validates_presence_of('cliente.telefono', 'message: El teléfono es requerido');
        $this->validates_format_of('cliente.telefono', '/^[0-9]{7,15}$/', 'message: El teléfono debe contener solo números (7 a 15 dígitos)');
        $this->validates_presence_of('cliente.email', 'message: El email es requerido');
        $this->validates_format_of('cliente.email', '/^[^@\s]+@[^@\s]+\.[^@\s]+$/', 'message: El formato del email no es válido');

    }
}