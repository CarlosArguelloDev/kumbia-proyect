<?php

class Bform
{
    protected static function attrsdefaut($attrs, $defaults)
    {
        foreach ($defaults as $k => $v) {
            if (isset($attrs[$k])) {
                if (strpos($attrs[$k], $v) === false) {
                    $attrs[$k] .= ' ' . $v;
                }
            } else {
                $attrs[$k] = $v;
            }
        }
        return $attrs;
    }

    // Formbs::btn_aceptar("Aceptar")
    public static function btn_aceptar($text = "Aceptar", $attrs = [])
    {
        $text = '<i class="ti ti-plus"></i> ' . $text;
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-primary"]);
        return Form::submit($text, $attrs);
    }

    // Formbs::btn_eliminar("Eliminar")
    public static function btn_eliminar($text = "Eliminar", $attrs = [])
    {
        $text = "🗑 " . $text;
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-danger"]);
        return Form::submit($text, $attrs);
    }

    // Formbs::btn_editar("Editar")
    public static function btn_editar($action, $text, $attrs = [])
    {
        $text = "📝 " . $text;
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-warning"]);
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . "$action\" $attrs>$text</a>";
    }

    public static function btn_agregar($action, $text, $attrs = [])
    {
        $text = '<i class="ti ti-plus"></i> ' . $text;
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-primary"]);
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . "$action\" $attrs>$text</a>";
    }

    public static function text_label($label, $field, $attrs = [], $value = null)
    {
        /*...*/
        $attrs = Bform::attrsdefaut($attrs, ["class" => "form-control"]);
        $html = "<label>$label</label>";
        return $html . Form::input('text', $field, $attrs, $value);
    }

    public static function badge_estado($estado)
    {
        $estilos = [
            1 => ["bg" => "bg-info", "icon" => "ti ti-clock"],       // Pendiente
            2 => ["bg" => "bg-primary", "icon" => "ti ti-loader-3"],    // En proceso
            3 => ["bg" => "bg-success", "icon" => "ti ti-check"],       // Resuelto
            4 => ["bg" => "bg-secondary", "icon" => "ti ti-lock"],        // Cerrado
        ];

        $cfg = $estilos[$estado->id] ?? ["bg" => "bg-dark", "icon" => "ti ti-help"];

        return "
        <span class=\"badge {$cfg['bg']}\">
            <i class=\"{$cfg['icon']}\"></i>
            Estado: {$estado->nombre}
        </span>
    ";
    }

    public static function badge_prioridad($prioridad)
    {
        $estilos = [
            1 => ["bg" => "bg-secondary", "icon" => "ti ti-arrow-down"],         // Baja
            2 => ["bg" => "bg-warning", "icon" => "ti ti-arrow-right"],        // Media
            3 => ["bg" => "bg-danger", "icon" => "ti ti-arrow-up"],           // Alta
            4 => ["bg" => "bg-danger", "icon" => "ti ti-alert-triangle"],     // Crítica
        ];

        $cfg = $estilos[$prioridad->id] ?? ["bg" => "bg-dark", "icon" => "ti ti-help"];

        return "
        <span class=\"badge {$cfg['bg']}\">
            <i class=\"{$cfg['icon']}\"></i>
            Prioridad: {$prioridad->nombre}
        </span>
    ";
    }

    public static function model_errors($model)
    {
        if (!$model)
            return "";

        $errors = $model->getErrors();
        if (empty($errors))
            return "";

        $html = "<div class=\"alert alert-danger alert-dismissible fade show\" role=\"alert\">";
        $html .= "<strong>Corrige lo siguiente:</strong><ul class=\"mb-0\">";

        foreach ($errors as $e) {
            // $e puede venir como string o array dependiendo versión
            if (is_array($e) && isset($e['message'])) {
                $msg = $e['message'];
            } else {
                $msg = $e;
            }
            $html .= "<li>$msg</li>";
        }

        $html .= "</ul>";
        $html .= "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>";
        $html .= "</div>";

        return $html;
    }

    /**
     * Botón Ver - Icono de ojo
     */
    public static function btn_ver($url, $attrs = [])
    {
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-sm btn-light-info", "title" => "Ver"]);
        $attrsStr = Tag::getAttrs($attrs);

        return '<a href="' . PUBLIC_PATH . $url . '" ' . $attrsStr . '>
            <i class="ti ti-eye"></i>
        </a>';
    }

    /**
     * Botón Editar - Icono de lápiz
     */
    public static function btn_editar_link($url, $attrs = [])
    {
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-sm btn-light-warning", "title" => "Editar"]);
        $attrsStr = Tag::getAttrs($attrs);

        return '<a href="' . PUBLIC_PATH . $url . '" ' . $attrsStr . '>
            <i class="ti ti-edit"></i>
        </a>';
    }

    /**
     * Botón Eliminar - Icono de basura con confirmación
     */
    public static function btn_eliminar_link($url, $mensaje = '¿Estás seguro de eliminar este elemento?', $attrs = [])
    {
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-sm btn-light-danger", "title" => "Eliminar"]);
        $attrs['onclick'] = "return confirm('$mensaje')";
        $attrsStr = Tag::getAttrs($attrs);

        return '<a href="' . PUBLIC_PATH . $url . '" ' . $attrsStr . '>
            <i class="ti ti-trash"></i>
        </a>';
    }

    /**
     * Grupo de botones de acción (Ver, Editar, Eliminar)
     */
    public static function grupo_acciones($id, $controlador, $opciones = [])
    {
        $mostrar_ver = $opciones['ver'] ?? true;
        $mostrar_editar = $opciones['editar'] ?? true;
        $mostrar_eliminar = $opciones['eliminar'] ?? true;
        $mensaje_eliminar = $opciones['mensaje_eliminar'] ?? '¿Estás seguro de eliminar este elemento?';

        $html = '';

        if ($mostrar_ver) {
            $html .= self::btn_ver("$controlador/ver/$id") . ' ';
        }

        if ($mostrar_editar) {
            $html .= self::btn_editar_link("$controlador/editar/$id") . ' ';
        }

        if ($mostrar_eliminar) {
            $html .= self::btn_eliminar_link("$controlador/eliminar/$id", $mensaje_eliminar);
        }

        return $html;
    }

    /**
     * Botón Volver/Regresar
     * Soporta tanto URLs normales como javascript:history.back()
     */
    public static function btn_volver($url, $texto = 'Volver', $attrs = [])
    {
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-secondary"]);

        // Detectar si es JavaScript
        if (stripos($url, 'javascript:') === 0) {
            // Extraer el código JavaScript
            $jsCode = substr($url, 11); // Quitar "javascript:"
            $attrs['onclick'] = $jsCode;
            $attrsStr = Tag::getAttrs($attrs);

            return '<button type="button" ' . $attrsStr . '>
                <i class="ti ti-arrow-left"></i> ' . $texto . '
            </button>';
        } else {
            // Enlace normal
            $attrsStr = Tag::getAttrs($attrs);
            return '<a href="' . PUBLIC_PATH . $url . '" ' . $attrsStr . '>
                <i class="ti ti-arrow-left"></i> ' . $texto . '
            </a>';
        }
    }

    /**
     * Botón Gestionar - Icono de tuerca/settings
     */
    public static function btn_gestionar($url, $texto = 'Gestionar', $attrs = [])
    {
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-sm btn-light"]);
        $attrsStr = Tag::getAttrs($attrs);

        return '<a href="' . PUBLIC_PATH . $url . '" ' . $attrsStr . '>
            <i class="ti ti-settings me-1"></i> ' . $texto . '
        </a>';
    }
}