<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

if (!function_exists('transaction')) {
    function transaction($callback, $onError = null)
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($onError && is_callable($onError)) {
                $onError($e);
            }

            Log::error('Exception Details:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return errorResponse('Có lỗi xảy ra, vui lòng thử lại sau!');
        }
    }
}

if (!function_exists('successResponse')) {
    function successResponse($message, $data = null, $code = 200, bool $isResponse = true, bool $isToastr = true)
    {
        $response = ['success' => true, 'message' => $message, 'data' => $data, 'code' => $code];

        if ($isToastr) sessionFlash('success', $message);

        return $isResponse ? response()->json($response, $code) : $response;
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse(string $message, $code = 500, bool $isResponse = true)
    {
        $response = [
            'success' => false,
            'message' => $message,
            'code' => $code
        ];
        return $isResponse ? response()->json($response, $code) : $response;
    }
}
