<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    public const MESSAGE_VAR = 'data';

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*') || $request->wantsJson()) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
            $response = [
                'success' => false,
                self::MESSAGE_VAR => $exception->getMessage(),
            ];

            if ($exception instanceof ValidationException) {
                $response[self::MESSAGE_VAR] = collect($exception->errors())->flatten()->first();
                $statusCode = 422;
            }

            if ($exception instanceof UnauthorizedHttpException) {
                $response[self::MESSAGE_VAR] = __('system.401');
                $statusCode = 401;
            }

            if ($exception instanceof AuthorizationException) {
                $response[self::MESSAGE_VAR] = __('system.403');
                $statusCode = 403;
            }

            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                $response[self::MESSAGE_VAR] = __('system.404');
                $statusCode = 404;
            }

            // This will replace our 405 response with a JSON response.
            if ($exception instanceof MethodNotAllowedHttpException) {
                $response[self::MESSAGE_VAR] = __('system.405');
                $statusCode = 405;
            }


            // more info at localhost
            if (\App::isLocal()) {
                $response['file'] = $exception->getFile();
                $response['line'] = $exception->getLine();
//                $response['trace'] = $exception->getTrace();
            }

            return response()->json($response, $statusCode);
        }

        return parent::render($request, $exception);
    }
}
