<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Summary of upload
     * @param mixed $file
     * @param string $directory
     * @return bool|string|null
     */
    public static function upload(?UploadedFile $file, string $directory = 'uploads'): ?string
    {
        if (!$file) {
            return null;
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $slug = Str::slug($originalName) . '_' . Str::random(10);

        $newFileName = $slug . '_' . time() . '.' . $extension;

        $path = $file->storeAs($directory, $newFileName, 'public');

        return $path;
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
