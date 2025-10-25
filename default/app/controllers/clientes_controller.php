<?php
class ClientesController extends AppController
{
    public function before_filter()
    {


    }

    public function index()
    {
        $this->clientes = (new Clientes())->find();
        $this->title = 'Clientes';
        $this->subtitle = 'Inicio';
    }
    public function ver($id)
    {
        $this->cliente = (new Clientes())->find($id);
        $this->title = 'Clientes';
        $this->subtitle = 'Ver Cliente';
    }
    public function registrar()
    {
        $this->cliente = new Clientes();
        $this->title = 'Clientes';
        $this->subtitle = 'Registrar Cliente';

        if (Input::hasPost("cliente")) {
            $params = Input::post("cliente");
            $cliente = new Clientes($params);
            $cliente->create();
        }
    }

    public function editar($id)
    {
        $this->cliente = (new Clientes())->find($id);
        $this->title = 'Clientes';
        $this->subtitle = 'Editar Cliente';

        if (Input::hasPost("cliente")) {
            $params = Input::post("cliente");
            $this->cliente->update($params);
        }
    }


}