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
        $this->usuarios = (new Usuarios())->find("order: id ASC");
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
