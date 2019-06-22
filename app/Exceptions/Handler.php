<?php

namespace App\Exceptions;

use App\Http\Resources\Review\ReviewResource;
use App\Utils\ErrorFormatter;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return array|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->isJson()) {
            return $this->handleApiException($exception, $request);
        } else {
            $res = parent::render($request, $exception);
        }

        return $res;
    }


    /**
     * @param Exception $exception
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException(Exception $exception, $request)
    {
        $data['exception']['code']=$exception->getCode();
        $class=get_class($exception);

        $statusCode=null;
        switch ($class){
            case NotBelongsToUser::class:
                $data['label']="not_belongs_to_this_user";
                $data['exception']['messages']=$exception->getMessage();
                break;
            case ModelNotFoundException::class:
                $data['label']="not_found";
                $data['exception']['messages']=$exception->getMessage();
                break;
            case ValidationException::class:
                $data['label']="validation_failed";
                $data['exception']['messages']=$exception->validator->getMessageBag();
                $statusCode=Response::HTTP_BAD_REQUEST;
                break;
            default:
                $data['label']="bad_request";
                $data['exception']['messages']=$exception->getMessage();
                $statusCode=Response::HTTP_BAD_REQUEST;
        }
        return ErrorFormatter::getErrorFormat($data,$statusCode);
    }

}
