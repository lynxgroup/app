<?php namespace LynxGroup\Component\App;

use Exception; 

use Psr\Http\Message\RequestInterface;

use Psr\Http\Message\ResponseInterface;

class AppException extends Exception
{
	protected $request;

	protected $middleware;

	public function __construct(
		RequestInterface $request,
		ResponseInterface $response,
		$message = "",
		$code = 0,
		Exception $previous = NULL
	)
	{
		$this->request = $request;

		$this->response = $response;

		parent::__construct($message, $code, $previous);
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getResponse()
	{
		return $this->response;
	}
}
