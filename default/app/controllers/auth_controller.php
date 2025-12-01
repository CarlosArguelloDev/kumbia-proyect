<?php
/**
 * Controlador de Autenticación  
 */
class AuthController extends AppController
{
    public function initialize()
    {
        View::template('auth');
    }

    public function login()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return Redirect::to('reportes');
        }

        $this->title = 'Iniciar Sesión';

        if (Input::hasPost('email')) {
            $email = Input::post('email');
            $password = Input::post('password');

            if (Auth::login($email, $password)) {
                Flash::valid('Bienvenido ' . Auth::user()['nombre']);
                return Redirect::to('reportes');
            } else {
                Flash::error('Email o contraseña incorrectos');
            }
        }
    }

    public function registro()
    {
        // Si ya está autenticado, redirigir
        if (Auth::check()) {
            return Redirect::to('reportes');
        }

        $this->title = 'Registro';

        if (Input::hasPost('usuario')) {
            $data = Input::post('usuario');

            $existe = (new Usuarios())->find_first("email = '{$data['email']}'");
            if ($existe) {
                Flash::error('El email ya está registrado');
                return;
            }

            // Hash de la contraseña
            $data['password'] = hash('sha256', $data['password']);
            $data['rol_id'] = 3;
            $data['activo'] = 1;

            $usuario = new Usuarios($data);

            if ($usuario->create()) {
                Flash::valid('Registro exitoso. Ahora puedes iniciar sesión');
                return Redirect::to('auth/login');
            } else {
                Flash::error('No se pudo completar el registro');
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        Flash::info('Sesión cerrada correctamente');
        return Redirect::to('auth/login');
    }
}
