<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaItemController extends Controller
{
    public function list(Request $request)
    {
        $query = MediaItem::with('media')->latest();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $mediaItems = $query->paginate(20);

        return response()->json([
            'data' => $mediaItems->map(function ($item) {
                $media = $item->getFirstMedia('images');

                if (!$media) return null;

                // Lấy kích thước ảnh (nếu chưa có sẵn trong custom_properties)
                $width = $media->getCustomProperty('width');
                $height = $media->getCustomProperty('height');

                // Nếu chưa có, dùng Intervention để lấy
                if (!$width || !$height) {
                    try {
                        $image = Image::make(Storage::path($media->getPath()));
                        $width = $image->width();
                        $height = $image->height();

                        // Lưu lại để lần sau khỏi load nữa
                        $media->setCustomProperty('width', $width);
                        $media->setCustomProperty('height', $height);
                        $media->save();
                    } catch (\Exception $e) {
                        $width = $height = null;
                    }
                }

                return [
                    'id' => $item->id,
                    'path' => $media->getUrl(),
                    'name' => $media->file_name,
                    'size' => round($media->size / 1024, 1), // KB
                    'uploaded_at' => $media->created_at->format('d/m/Y H:i'),
                    'width' => $width,
                    'height' => $height,
                ];
            })->filter()->values(), // lọc bỏ null
            'pagination' => [
                'total' => $mediaItems->total(),
                'current_page' => $mediaItems->currentPage(),
                'last_page' => $mediaItems->lastPage(),
            ],
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|array',
            'file.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
        ]);

        $uploaded = [];

        foreach ($request->file('file') as $file) {
            $mediaItem = MediaItem::create([
                'name' => $file->getClientOriginalName(),
            ]);

            $mediaItem->addMedia($file)->toMediaCollection('images');

            $uploaded[] = [
                'id' => $mediaItem->id,
                'path' => $mediaItem->getFirstMediaUrl('images'),
            ];
        }

        return response()->json([
            'message' => 'Tải ảnh thành công!',
            'data' => $uploaded
        ]);
    }
}
