<?php
class UsuariosController extends AppController
{
    public function initialize()
    {
        parent::initialize();

        $action = Router::get('action');
        if ($action !== 'perfil') {
            Auth::requireAdmin();
        }
    }

    // Listado de usuarios con filtros
    public function index()
    {
        $busqueda = Input::get('busqueda');
        $activo = Input::get('activo');
        $fecha_desde = Input::get('fecha_desde');
        $fecha_hasta = Input::get('fecha_hasta');

        $conditions = [];

        if ($busqueda) {
            $busqueda_safe = addslashes($busqueda);
            $conditions[] = "(nombre LIKE '%{$busqueda_safe}%' OR email LIKE '%{$busqueda_safe}%')";
        }

        if ($activo !== '' && $activo !== null) {
            $conditions[] = "activo = " . (int) $activo;
        }

        if ($fecha_desde) {
            $conditions[] = "DATE(created_at) >= '" . date('Y-m-d', strtotime($fecha_desde)) . "'";
        }

        if ($fecha_hasta) {
            $conditions[] = "DATE(created_at) <= '" . date('Y-m-d', strtotime($fecha_hasta)) . "'";
        }

        $where = count($conditions) > 0 ? "conditions: " . implode(" AND ", $conditions) : "";
        $order = "order: id ASC";

        if ($where) {
            $this->usuarios = (new Usuarios())->find($where, $order);
        } else {
            $this->usuarios = (new Usuarios())->find($order);
        }

        $this->filtros = [
            'busqueda' => $busqueda,
            'activo' => $activo,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta
        ];

        $this->title = 'Gestión de Usuarios';
        $this->subtitle = 'Listado completo';
    }

    // Creación de usuarios
    public function crear()
    {
        $this->title = 'Crear Usuario';
        $this->subtitle = 'Nuevo usuario';

        if (Input::hasPost('usuario')) {
            $data = Input::post('usuario');

            if (!empty($data['password'])) {
                $data['password'] = hash('sha256', $data['password']);
            }

            $usuario = new Usuarios($data);

            if ($usuario->create()) {
                Flash::valid('Usuario creado correctamente');
                return Redirect::to('usuarios');
            } else {
                Flash::error('No se pudo crear el usuario');
            }
        }
    }

    // Edición de usuarios
    public function editar($id)
    {
        $this->usuario = (new Usuarios())->find_first((int) $id);

        if (!$this->usuario) {
            Flash::error('Usuario no encontrado');
            return Redirect::to('usuarios');
        }

        $this->title = 'Editar Usuario';
        $this->subtitle = $this->usuario->nombre;

        if (Input::hasPost('usuario')) {
            $data = Input::post('usuario');

            if (!empty($data['password'])) {
                $data['password'] = hash('sha256', $data['password']);
            } else {
                unset($data['password']);
            }

            if ($this->usuario->update($data)) {
                Flash::valid('Usuario actualizado correctamente');
                return Redirect::to('usuarios');
            } else {
                Flash::error('No se pudo actualizar el usuario');
            }
        }
    }

    // Eliminación de usuarios
    public function eliminar($id)
    {
        $usuario = (new Usuarios())->find_first((int) $id);

        if (!$usuario) {
            Flash::error('Usuario no encontrado');
            return Redirect::to('usuarios');
        }

        if ($usuario->delete()) {
            Flash::valid('Usuario eliminado correctamente');
        } else {
            Flash::error('No se pudo eliminar el usuario');
        }

        return Redirect::to('usuarios');
    }

    // Perfil del usuario
    public function perfil()
    {
        Auth::require();

        $this->usuario = (new Usuarios())->find_first(Auth::id());

        if (!$this->usuario) {
            Flash::error('Usuario no encontrado');
            return Redirect::to('auth/logout');
        }

        $this->title = 'Mi Perfil';
        $this->subtitle = 'Información personal';

        if (Input::hasPost('usuario')) {
            $data = Input::post('usuario');

            if (!empty($data['password'])) {
                $data['password'] = hash('sha256', $data['password']);
            } else {
                unset($data['password']);
            }

            if ($this->usuario->update($data)) {
                Flash::valid('Perfil actualizado correctamente');
            } else {
                Flash::error('No se pudo actualizar el perfil');
            }
        }
    }
}
