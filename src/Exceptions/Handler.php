<?php

namespace Voyager\Admin\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Prepare exception for rendering.
     *
     * @param  \Throwable  $e
     * @return \Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        $exception = [
            'status'    => $response->status(),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'exception' => get_class($e),
        ];

        if ($response->status() === 419) {
            return back()->with([
                'exception' => $exception,
            ]);
        }

        return Inertia::render('Error', [
            'title'     => __('voyager::generic.error', [ 'code' => $response->status() ]),
            'exception' => $exception
        ])
        ->toResponse($request)
        ->setStatusCode($response->status());
    }
}
