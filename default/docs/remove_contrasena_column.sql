-- Eliminar columna contrasena de la tabla usuarios
-- Mantendremos solo la columna password

ALTER TABLE usuarios DROP COLUMN contrasena;
