<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $msg = $this->handleValidationException($exception);
            return response(['code' => 0, 'msg' => $msg], 200);
        }

        if ($exception instanceof AuthorizationException) {
            return response(['code' => 403, 'msg' => $exception->getMessage()], 200);
        }

        $code = $exception->getCode();
        if (!in_array($code, [401,403])) {
            $code = 0;
        }

        $message = $exception->getMessage();
        if (empty($message)) {
            $message = '网络异常,请稍后重试';
        }
        //echo $message;die;
        return response(['code' => $code, 'msg' => $message], 200);
    }

    protected function handleValidationException($e)
    {
        $messages = $e->validator->messages();
        if ($messages->isNotEmpty()) {
            return $messages->first();
        } else {
            return "验证失败";
        }
    }
}
