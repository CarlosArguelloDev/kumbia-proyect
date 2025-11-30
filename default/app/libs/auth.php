<?php
/**
 * Clase para manejo de autenticación
 */
class Auth
{
    /**
     * Clave de sesión para el usuario
     */
    const SESSION_KEY = 'auth_user';

    /**
     * Intenta autenticar un usuario
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public static function login($email, $password)
    {
        $user = (new Usuarios())->find_first("email = '$email' AND activo = 1");

        // Verificar password hasheado con SHA256
        $passwordHash = hash('sha256', $password);

        if ($user && $user->password === $passwordHash) {
            // Obtener rol directamente de la tabla
            $rol = (new Roles())->find_first($user->rol_id);

            Session::set(self::SESSION_KEY, [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol_id' => $user->rol_id,
                'rol' => $rol ? $rol->nombre : 'usuario'
            ]);
            return true;
        }

        return false;
    }

    /**
     * Cierra la sesión del usuario
     */
    public static function logout()
    {
        Session::delete(self::SESSION_KEY);
    }

    /**
     * Verifica si hay un usuario autenticado
     *
     * @return boolean
     */
    public static function check()
    {
        return Session::has(self::SESSION_KEY);
    }

    /**
     * Obtiene los datos del usuario autenticado
     *
     * @return array|null
     */
    public static function user()
    {
        return Session::get(self::SESSION_KEY);
    }

    /**
     * Obtiene el ID del usuario autenticado
     *
     * @return int|null
     */
    public static function id()
    {
        $user = self::user();
        return $user ? $user['id'] : null;
    }

    /**
     * Verifica si el usuario tiene un rol específico
     *
     * @param string $rol Nombre del rol (administrador, gestor, usuario)
     * @return boolean
     */
    public static function hasRole($rol)
    {
        $user = self::user();
        return $user && $user['rol'] === $rol;
    }

    /**
     * Verifica si el usuario es administrador
     *
     * @return boolean
     */
    public static function isAdmin()
    {
        return self::hasRole('administrador');
    }

    /**
     * Verifica si el usuario es gestor
     *
     * @return boolean
     */
    public static function isGestor()
    {
        return self::hasRole('gestor');
    }

    /**
     * Requiere autenticación, redirige si no está logueado
     *
     * @param string $redirect
     */
    public static function require($redirect = 'auth/login')
    {
        if (!self::check()) {
            return Redirect::to($redirect);
        }
    }

    /**
     * Requiere un rol específico
     *
     * @param string $rol
     * @param string $redirect
     */
    public static function requireRole($rol, $redirect = 'auth/login')
    {
        if (!self::hasRole($rol)) {
            Flash::error('No tienes permisos para acceder a esta sección');
            return Redirect::to($redirect);
        }
    }

    /**
     * Requiere rol de administrador
     *
     * @param string $redirect
     */
    public static function requireAdmin($redirect = 'reportes')
    {
        if (!self::isAdmin()) {
            Flash::error('Acceso denegado: Se requieren permisos de administrador');
            return Redirect::to($redirect);
        }
    }
}
