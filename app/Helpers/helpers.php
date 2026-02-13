<?php

use Illuminate\Support\Facades\Storage;
use App\Models\v7\ConfigSetting;

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

if (! function_exists('image_storage_domain')) {
    function image_storage_domain(): string
    {
		return is_primary_domain() ? Storage::disk('s3')->url(upload_path()) : Storage::disk('public')->url('app');
    }
}

if (! function_exists('is_primary_serv_active')) {
    function is_primary_serv_active(): string
    {
        return ConfigSetting::where(['name' => 'PRIMARY_ACTIVE', 'status' => 1])->value('value') === '1';
    }
}