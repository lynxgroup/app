<?php namespace LynxGroup\Component\App;

use LynxGroup\Contracts\App\WebApp as WebAppInterface;

use LynxGroup\Contracts\Container\Container; 

use Psr\Http\Message\RequestInterface;

use Psr\Http\Message\ResponseInterface;

use Exception;

class WebApp implements WebAppInterface
{
	protected $container;

	protected $middleware;

	public function __construct(Container $container)
	{
		$this->container = $container;

		$this->middleware = $container->middleware;
	}

	public function add(callable $callback)
	{
		$this->middleware->add($callback);
	}

	public function run()
	{
		$request = $this->container->request;

		$response = $this->container->response;

		$this->middleware->setResolver(function($entry)
		{
			return function(RequestInterface $request, ResponseInterface $response, $next) use($entry)
			{
				return call_user_func($entry, $request, $response, $next);
			};
		});

		try
		{
			$response = $this->middleware->__invoke($request, $response);
		}
		catch(NotFoundException $e)
		{
			$response = $e->getResponse()->withStatus(404);

			$response->getBody()->write('NotFoundException');
		}
		catch(Exception $e)
		{
			$response = $e->getResponse()->withStatus(404);

			$response->getBody()->write('Exception');
		}

		$this->render($response);
	}

	public function render($response)
	{
		header(sprintf(
			'HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		));

		foreach( $response->getHeaders() as $name => $values )
		{
			foreach( $values as $value )
			{
				header(sprintf('%s: %s', $name, $value), false);
			}
		}

		echo (string)$response->getBody();
	}
}
