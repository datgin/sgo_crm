<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $error = $exception->errors();
            $firstError = reset($error);

            if ($request->ajax()) {
                return response()->json([
                    'message' => $firstError[0]
                ], 422);
            }

            return parent::render($request, $exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Dữ liệu không tồn tại.',
                ], Response::HTTP_NOT_FOUND); // 404
            }
        }

        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->view('errors.403', [], 403);
            }

            return response()->view('errors.403', [], 403);
        }

        // ✅ Bổ sung dòng này
        return parent::render($request, $exception);
    }


    public function report(Throwable $exception)
    {
        Log::error('>>Exception occurred<<', [
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
        ]);

        parent::report($exception);
    }
}
