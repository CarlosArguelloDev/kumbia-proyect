<?php

class Bform{
    protected static function attrsdefaut($attrs, $defaults)
    {
        foreach ($defaults as $k => $v) {
            if (isset($attrs[$k])) {
                if (strpos($attrs[$k], $v) === false) {
                    $attrs[$k] .= ' '.$v;
                }
            } else {
                $attrs[$k] = $v;
            }
        }
        return $attrs;
    }

    // Formbs::btn_aceptar("Aceptar")
    public static function btn_aceptar($text = "Aceptar", $attrs = []){
        $text = "💾 ".$text;
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-primary"]);
        return Form::submit($text, $attrs);
    }

    // Formbs::btn_eliminar("Eliminar")
    public static function btn_eliminar($text = "Eliminar", $attrs = []){
        $text = "🗑 ".$text;
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-danger"]);
        return Form::submit($text, $attrs);
    }

    // Formbs::btn_editar("Editar")
    public static function btn_editar($action, $text, $attrs = []){
        $text = "📝 ".$text;
        $attrs = Bform::attrsdefaut($attrs, ["class" => "btn btn-warning"]);
        $attrs = Tag::getAttrs($attrs);
        return '<a href="' . PUBLIC_PATH . "$action\" $attrs>$text</a>";
    }

    public static function btn_agregar($action, $text, $attrs = []){
        $text = "📝 ".$text;
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



}