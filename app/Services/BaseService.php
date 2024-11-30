<?php

namespace App\Services;

class BaseService
{
    protected $result;

    public $data;

    public function __construct()
    {
        $this->result = [];
        $this->data = null;
    }

    /**
     * Handle Success Cases and responses
     *
     * @param int $statusCode
     * @param $data
     * @param int $code
     * @param string $hint
     * @return array
     */
    public function success(int $statusCode, $data, int $code = 1, string $hint = ''): array
    {
        switch ($statusCode) {
            case 200:
                $code = 1020;
                $hint = 'Processed Successfully';
                break;
            case 201:
                $code = 1021;
                $hint = 'Resource created successfully';
                break;
            default:
                break;
        }

        $this->result['status_code'] = $statusCode;
        $this->result['code'] = $code;
        $this->result['hint'] = $hint;
        $this->result['success'] = true;
        $this->result['data'] = $data;

        return $this->result;
    }

    /**
     * Handle Failed Cases and Responses
     *
     * @param int $statusCode
     * @param $errors
     * @param int $code
     * @param string $hint
     * @return array
     */
    public function failed(int $statusCode, $errors, int $code = 0, string $hint = ''): array
    {
        switch ($statusCode) {
            case 401:
                $code = 1041;
                $hint = 'Unauthenticated';
                break;
            case 403:
                $code = 1043;
                $hint = 'Forbidden';
                break;
            case 404:
                $code = 1044;
                $hint = 'Resource not found';
                break;
            case 409:
                $code = 1049;
                $hint = 'Method Not Allowed';
                break;
            case 422:
                $code = 1422;
                $hint = 'Unprocessable Entity';
                break;
            case 500:
                $code = 1050;
                $hint = 'Server error';
                break;
            default:
                break;
        }

        $this->result['status_code'] = $statusCode;
        $this->result['code'] = $code;
        $this->result['hint'] = $hint;
        $this->result['success'] = false;
        $this->result['errors'] = $errors;

        return $this->result;
    }

    /**
     * Handles Exceptions Fails
     *
     * @param Exception $e
     * @return array
     */
    public function failedWithException(Exception $e): array
    {
        return $this->failed(500, [
            'error' => __('common.something_went_wrong'),
            'description' => [
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
            ],
        ]);
    }
}
