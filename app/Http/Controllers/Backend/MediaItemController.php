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

        $mediaItems = $query->paginate(2);

        return response()->json([
            'data' => $mediaItems->map(function ($item) {
                $media = $item->getFirstMedia('images');

                if (!$media) return null;

                // Lấy thông tin kích thước từ custom_properties (nếu đã có)
                $width = $media->getCustomProperty('width');
                $height = $media->getCustomProperty('height');

                // Nếu chưa có, dùng Intervention để lấy kích thước
                if (!$width || !$height) {
                    try {
                        // Cách đúng: dùng getPath() vì đã là đường dẫn đầy đủ
                        $image = Image::make($media->getPath());

                        $width = $image->width();
                        $height = $image->height();

                        // Lưu lại vào custom_properties
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
            })->filter()->values(), // lọc null nếu media không tồn tại
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

    public function destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:media_items,id',
        ]);

        $deletedCount = 0;

        foreach ($request->ids as $id) {
            $mediaItem = MediaItem::find($id);

            if ($mediaItem) {
                // Xoá file vật lý và bản ghi trong media table
                $mediaItem->clearMediaCollection('images');

                // Xoá bản ghi media_item
                $mediaItem->delete();
                $deletedCount++;
            }
        }

        return response()->json([
            'message' => "Đã xoá {$deletedCount} ảnh thành công.",
        ]);
    }
}
