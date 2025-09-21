<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
{
    $targetErrors = [
        \Phiki\TextMate\FailedToInitializePatternSearchException::class,
        \Phiki\TextMate\FailedToSetSearchPositionException::class,
    ];

    // Agar bu ViewException bo‘lsa, asl sababini tekshiramiz
    $exception = $e instanceof \Illuminate\View\ViewException
        ? $e->getPrevious() ?? $e
        : $e;

    if (in_array(get_class($exception), $targetErrors)) {
        // Asl xatoni foydalanuvchiga ko‘rsatamiz
        return response()->json([
            'status'  => 'error',
            'message' => $exception->getMessage(),
        ], 500);
    }

    // Agar ma’lumotlar bazasi xatosi bo‘lsa
    if ($exception instanceof \Illuminate\Database\QueryException) {
        return response()->json([
            'status'  => 'error',
            'message' => $exception->getMessage(), // yoki: 'Xatolik: ' . $exception->errorInfo[2]
        ], 500);
    }

    return parent::render($request, $e);
}

}
