<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Summary of upload
     * @param mixed $file
     * @param string $directory
     * @return bool|string|null
     */
    public static function upload(?UploadedFile $file, string $directory = 'avatars'): ?string
    {
        return $file ? $file->store($directory, 'public') : null;
    }

    /**
     * Delete an existing file from the public disk.
     * @param string $path
     * @return void
     */
    public static function delete(string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Replace old file with a new one.
     * @param mixed $oldPath
     * @param mixed $newFile
     * @param string $directory
     * @return bool|string|null
     */
    public static function replace(?string $oldPath, ?UploadedFile $newFile, string $directory): ?string
    {
        if ($oldPath) {
            self::delete($oldPath);
        }

        return self::upload($newFile, $directory);
    }
}
