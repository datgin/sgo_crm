<?php

namespace App\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Validation\ValidationException;

trait ValidatesMediaPaths
{
    /**
     * Kiểm tra danh sách URL ảnh có tồn tại trong Media Library không
     *
     * @param array $paths
     * @param string $fieldName
     * @return void
     * @throws ValidationException
     */
    public function validateMediaPaths(array $paths, string $fieldName)
    {
        foreach ($paths as $url) {
            $filename = basename(parse_url($url, PHP_URL_PATH));

            if (!Media::where('file_name', $filename)->exists()) {
                throw ValidationException::withMessages([
                    $fieldName => "Ảnh {$filename} không tồn tại trong thư viện media.",
                ]);
            }
        }
    }

    public function validateMultipleMediaFields(array $fields)
    {
        foreach ($fields as $field => $paths) {
            $this->validateMediaPaths($paths, $field);
        }
    }
}
