<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest(route('login', ['account' => $request->route('account')]));
    }

    public function render($request, Throwable $exception)
    {
        // Handle CSRF token mismatch (419 errors)
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            // For delivery form, try to restore from cookies and redirect
            if ($request->is('motorcycle-delivery/*')) {
                // Check if this is a fresh submission (has form data) vs expired session
                $hasFormData = $request->has('pickup_postcode') || $request->has('full_name');
                
                // Only redirect if it's NOT a fresh submission (likely expired session)
                // If it's a fresh submission with 419, let it fail normally or try to restore
                if (!$hasFormData) {
                    // Try to restore session from cookies
                    $pickupCoordsCookie = Cookie::get('pickup_coords');
                    $dropoffCoordsCookie = Cookie::get('dropoff_coords');
                    
                    if ($pickupCoordsCookie && $dropoffCoordsCookie) {
                        // Redirect to store page which will create fresh session
                        return redirect()->route('motorcycle.delivery.store')
                            ->with('error', 'Your session expired. We\'ve restored your order details. Please complete the form again.');
                    }
                } else {
                    // Fresh submission with 419 - likely token refresh issue
                    // Redirect back with input so user can retry
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'CSRF token expired. Please try submitting again.');
                }
                
                return redirect()->route('motorcycle.delivery')
                    ->with('error', 'Your session has expired. Please start your order again.');
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                    'error' => 'token_mismatch'
                ], 419);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Your session has expired. Please refresh the page and try again.');
        }
        
        if ($exception instanceof NotFoundHttpException) {
            return response()->view('errors.404', [], 404);
        }

        return parent::render($request, $exception);
    }
}
