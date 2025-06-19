<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileUploadService
{
    public static function upload(UploadedFile $file, string $folder, string $nameOption = 'original'): array
    {
        $disk = Storage::disk('public');
        $directory = "uploads/{$folder}";

        if (!$disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $extension = $file->getClientOriginalExtension();

        switch ($nameOption) {
            case 'timestamp':
                $fileName = time() . '.' . $extension;
                break;
            case 'original':
            default:
                $fileName = $file->getClientOriginalName();
                break;
        }

        $path = $file->storeAs($directory, $fileName, 'public');

        return [
            'file_name' => $fileName,
            'file_path' => $path,
        ];
    }

    public static function delete(string $path)
    {
        return Storage::disk('public')->delete($path);
    }
}
