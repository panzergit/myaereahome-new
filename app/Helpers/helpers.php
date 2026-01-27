<?php

if (! function_exists('upload_path')) {
    function upload_path(string $folder = ''): string
    {
        $base = config('filesystems.upload_folder');
        return $base
            ? trim($base.'/'.$folder, '/')
            : trim($folder, '/');
    }
}

if (! function_exists('is_primary_domain')) {
    function is_primary_domain(): int
    {
        $host = request()->getHost(); // abc.com, def.com, ghi.com
        return $host === 'new.myaereahome.com' ? 1 : 0;
    }
}

if (! function_exists('remove_upload_path')) {
    function remove_upload_path(string $path): string
    {
        return str_replace(upload_path(). '/', '', $path);
    }
} 