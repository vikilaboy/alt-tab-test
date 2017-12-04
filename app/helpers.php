<?php

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('get_short_class')) {
    /**
     * Get the short class name, without the namespace.
     *
     * @param $name
     *
     * @return string
     */
    function get_short_class($name)
    {
        if (is_object($name)) {
            $name = get_class($name);
        }

        if (($pos = strrpos($name, '\\')) !== false) {
            $name = substr($name, $pos + 1);
        }
        return $name;
    }
}
