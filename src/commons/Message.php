<?php

namespace Zank\Common;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * 消息公用接口
 *
 * @author Seven Du <lovevipdsw@outlook.com> 
 **/
class Message
{
    protected $response;

    protected $status;

    protected $message;

    protected $data;

    public function __construct(Response $response, bool $status, string $message, $data = null)
    {
        $this->response = $response;
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    public function withArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public function withJson(): Response
    {
        return $this->withTo('withJson');
    }

    public function withTo(string $functionName): Response
    {
        return $this
            ->response
            ->$functionName($this->withArray())
        ;
    }
} // END class Message
