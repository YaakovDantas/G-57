<?php

namespace Bereshit\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ResponseResource extends JsonResource
{
    private $statusOk = [
        Response::HTTP_OK,
        Response::HTTP_CREATED,
        Response::HTTP_NO_CONTENT,
        Response::HTTP_ACCEPTED,
    ];

    private $statusCode;
    private $error;
    private $alert;
    private $line;
    private $file;
    private $message;
    private $data;
    private $errorOrMessage;

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setAlert($alert)
    {
        $this->alert = $alert;
        return $this;
    }

    public function setLine($line)
    {
        $this->line = $line;
        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function __construct($errorOrMessage = null, $statusCode = null)
    {
        $this->errorOrMessage = $errorOrMessage;
        $this->statusCode = $statusCode ?? Response::HTTP_OK;
        $this->error = null;
        $this->data = null;
    }

    public function toJson($options = 0)
    {
        if (is_null($this->error) && is_null($this->data)) {
            in_array($this->statusCode, $this->statusOk)
                ? $this->setData($this->errorOrMessage)
                : $this->setError($this->errorOrMessage);
        }

        if ($this->error && !$this->statusCode) {
            $this->setCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = [
            "erro" => $this->error,
            "dados" => $this->data,
        ];

        if (isset($this->alert)) {
            $response["alert"] = $this->alert;
        }

        if (isset($this->line)) {
            $response["line"] = $this->line;
        }

        if (isset($this->file)) {
            $response["file"] = $this->file;
        }

        if (isset($this->message)) {
            $response["message"] = $this->message;
        }

        return response()->json($response, $this->statusCode);
    }
}