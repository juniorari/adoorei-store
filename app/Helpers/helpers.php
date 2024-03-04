<?php


use Illuminate\Http\JsonResponse;

function responseJson(int $code, string $message, ?array $resource = []): JsonResponse
{
    $result = [
        'code' => $code,
        'message' => $message,
        'data' => [],
    ];

    if (count($resource)) {
        $result = array_merge($result, ['data' => $resource['data']]);

        if (count($resource) > 1)
            $result = array_merge($result, ['pages' => ['links' => $resource['links'], 'meta' => $resource['meta']]]);
    }

    return response()->json($result, $code);
}

