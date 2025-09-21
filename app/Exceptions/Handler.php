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
        // Phiki xatolarini bostirish
        if ($e instanceof \Phiki\TextMate\FailedToInitializePatternSearchException || 
            $e instanceof \Phiki\TextMate\FailedToSetSearchPositionException) {
            return response('Server Error', 500);
        }

        // Boshqa ViewException larni ham bostirish
        if ($e instanceof \Illuminate\View\ViewException) {
            $previous = $e->getPrevious();
            if ($previous instanceof \Phiki\TextMate\FailedToInitializePatternSearchException || 
                $previous instanceof \Phiki\TextMate\FailedToSetSearchPositionException) {
                return response('Server Error', 500);
            }
        }

        return parent::render($request, $e);
    }
}
