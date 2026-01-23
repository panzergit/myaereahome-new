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
