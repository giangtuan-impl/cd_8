<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FileService
{
    public function makeDirectory($path)
    {
        try {
            File::makeDirectory($path);

            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function deleteDirectory($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        try {
            File::deleteDirectory($path);

            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function renameDirectory($oldName, $newName)
    {
        try {
            return File::moveDirectory($oldName, $newName);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function findFileInDirectory($path, $fileName)
    {
        if (!file_exists($path)) {
            return null;
        }

        try {
            $files = File::files($path);

            foreach ($files as $file) {
                if ($fileName == $file->getFilename()) {
                    return $file;
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public function upload($path, $file)
    {
        try {
            $name = $file->getFilenameWithoutExtension();
            $extension = "." . $file->getExtension();
            $newName = $name . "_" . md5($name . time()) . $extension;
            $path = $path . $newName;
            if (!File::copy($file, $path)) {
                return '';
            }

            return $newName;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return '';
    }

    public function getFile($path)
    {
        return File::files($path);
    }

    public function saveFile($path, $value)
    {
        try {
            return File::put($path, $value);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
