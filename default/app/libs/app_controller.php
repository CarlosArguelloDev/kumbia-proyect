<?php
/**
 * @see Controller nuevo controller
 */
require_once CORE_PATH . 'kumbia/controller.php';

// Cargar clase Auth
require_once APP_PATH . 'libs/auth.php';

/**
 * Controlador principal que heredan los controladores
 *
 * Todos las controladores heredan de esta clase en un nivel superior
 * por lo tanto los métodos aquií definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 */
abstract class AppController extends Controller
{

    protected function initialize()
    {
        $this->title = "Web2025";
        $this->subtitle = "Sección";
        View::template("mantis");
    }

    final protected function finalize()
    {

    }

}
