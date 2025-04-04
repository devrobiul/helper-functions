<?php

if (!function_exists('uploadFile')) {
    function uploadFile($file, $path = 'frontend/upload/files')
    {
        if ($file) {
            $fileName = hexdec(uniqid()) . '.' . $file->extension();
            $file->move(public_path($path), $fileName);
            $filePath = $path . '/' . $fileName;
            return str_replace('\\', '/', $filePath);
        }
        return null;
    }
}
if (! function_exists('setting')) :
    function setting($name, $default = null, $callback = null)
    {
        static $settings;
        if (! $settings) {
            $settings = \App\Models\Setting::get()->pluck('value', 'name')->toArray();
        }

        $return = $settings[$name] ?? $default;

        if (is_callable($callback)) {
            return call_user_func($callback, $return, $settings);
        }

        return $return;
    }
endif;