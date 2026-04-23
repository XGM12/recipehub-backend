<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Utils
{
    public static function checkRequestMethod(Request $request, string $method)
    {
        if ($request->getMethod() != $method)
            throw new BadRequestHttpException("HTTP method not valid");
    }

    public static function checkNotNull($entity, string $message = "Resource not found"): void
    {
        if (!$entity)
            throw new NotFoundHttpException($message);
    }
}