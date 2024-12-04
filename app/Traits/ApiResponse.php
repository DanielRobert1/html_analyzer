<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Send formatted json response to client
     * @param $result
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message  = null, $code = 200): JsonResponse
    {
        $data = [
            'status' => 'success',
        ];

        if($message){
            $data['message'] = $message;
        }

        return response()->json( array_merge($data, $result), $code);
    }

    /**
     * Sends a raw message back to client
     */
    public function sendSuccess($message, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], $code);
    }

    /**
     * Send formatted error to client
     *
     * @param $error
     * @param int $code
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $code = 404, $data = []): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $error,
        ];

        if($data) $response['data'] = $data;
        return response()->json($response, $code);
    }
}
