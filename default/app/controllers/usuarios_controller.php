<?php
/**
 * Controlador de Usuarios
 */
class UsuariosController extends AppController
{
    public function initialize()
    {
        // Llamar al initialize del padre
        parent::initialize();

        // Solo admins pueden gestionar usuarios
        $action = Router::get('action');
        if ($action !== 'perfil') {
            Auth::requireAdmin();
        }
    }

    public function index()
    {
        // Obtener parámetros de filtro
        $busqueda = Input::get('busqueda');
        $activo = Input::get('activo');
        $fecha_desde = Input::get('fecha_desde');
        $fecha_hasta = Input::get('fecha_hasta');

        // Construir condiciones de filtro
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

        // Construir query
        $where = count($conditions) > 0 ? "conditions: " . implode(" AND ", $conditions) : "";
        $order = "order: id ASC";

        // Obtener usuarios filtrados
        if ($where) {
            $this->usuarios = (new Usuarios())->find($where, $order);
        } else {
            $this->usuarios = (new Usuarios())->find($order);
        }

        // Pasar valores de filtros actuales a la vista
        $this->filtros = [
            'busqueda' => $busqueda,
            'activo' => $activo,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta
        ];

        $this->title = 'Gestión de Usuarios';
        $this->subtitle = 'Listado completo';
    }

    public function crear()
    {
        $this->title = 'Crear Usuario';
        $this->subtitle = 'Nuevo usuario';

        if (Input::hasPost('usuario')) {
            $data = Input::post('usuario');

            // Hash de la contraseña directamente en password
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

            // Si se proporciona una nueva contraseña, hashearla
            if (!empty($data['password'])) {
                $data['password'] = hash('sha256', $data['password']);
            } else {
                // Si no hay contraseña, no actualizar ese campo
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

    public function perfil()
    {
        Auth::require();

        // Obtener usuario autenticado
        $this->usuario = (new Usuarios())->find_first(Auth::id());

        if (!$this->usuario) {
            Flash::error('Usuario no encontrado');
            return Redirect::to('auth/logout');
        }

        $this->title = 'Mi Perfil';
        $this->subtitle = 'Información personal';

        if (Input::hasPost('usuario')) {
            $data = Input::post('usuario');

            // Si se proporciona una nueva contraseña, hashearla
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
