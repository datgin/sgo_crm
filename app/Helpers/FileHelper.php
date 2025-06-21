<?php

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;


if (!function_exists('showImage')) {
    function showImage(?string $absoluteUrl): string
    {
        if (!$absoluteUrl) {
            return asset('assets/backend/images/image-default.png');
        }

        $parsedPath = parse_url($absoluteUrl, PHP_URL_PATH);
        $relativePath = ltrim(str_replace('/storage/', '', $parsedPath), '/');

        /** @var \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        if ($storage->exists($relativePath)) {
            return $absoluteUrl;
            // return $storage->url($relativePath);
        }

        return asset('assets/backend/images/image-default.png');
    }
}

if (!function_exists('fileExists')) {
    function fileExists(?string $relativePath): ?string
    {
        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        if ($relativePath && $storage->exists($relativePath)) {
            return $storage->url($relativePath);
        }

        return asset('assets/backend/images/image-default.png');
    }
}

if (!function_exists('deleteImage')) {
    function deleteImage($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

if (!function_exists('uploadImages')) {
    function uploadImages($flieName, string $directory = 'images', $isArray = false,  $resize = false, $width = 150, $height = 150, $quality = 80)
    {
        $paths = [];

        $images = request()->file($flieName);
        if (!is_array($images)) {
            $images = [$images];
        }

        $manager = new ImageManager(['driver' => 'gd']);
        $storagePath = storage_path('app/public/' . trim($directory, '/'));

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        foreach ($images as $key => $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $img = $manager->make($image->getRealPath());

                // Resize nếu $resize = true, giữ tỷ lệ
                if ($resize) {
                    $img->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize(); // Không phóng to ảnh nhỏ
                    });
                }

                $filename = time() . uniqid() . '.webp';

                // Encode với chất lượng 80 (bạn có thể chỉnh từ 60 đến 90)
                Storage::disk('public')->put($directory . '/' . $filename, $img->encode('webp', $quality));

                $paths[$key] = $directory . '/' . $filename;
            }
        }

        return $isArray ? $paths : $paths[0] ?? null;
    }
}


if (!function_exists('uploadPdf')) {
    function uploadPdf(string $fieldName, string $directory = 'documents'): ?string
    {
        $file = request()->file($fieldName);

        if ($file && $file->isValid() && $file->getClientOriginalExtension() === 'pdf') {
            $filename = time() . uniqid() . '.pdf';
            $path = $file->storeAs($directory, $filename, 'public');
            return $path; // Đường dẫn tính từ public disk
        }

        return null; // Không có file hoặc không hợp lệ
    }
}
