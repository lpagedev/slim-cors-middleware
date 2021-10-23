<?php
/** @noinspection PhpUnused */

namespace lpagedev\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class SlimCorsMiddleware
{
    private CorsObject $_cors;

    /**
     * @param CorsObject $pCorsObject
     */
    public function __construct(CorsObject $pCorsObject)
    {
        $this->_cors = $pCorsObject;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Handle options request
        if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === "options") {
            return self::BlankCorsResponse(201);
        }

        // If origin is not allowed, don't continue, and definitely don't send any data!
        if (isset($_SERVER['HTTP_ORIGIN']) && (strtolower($_SERVER['HTTP_ORIGIN']) !== strtolower($this->_cors->AllowOrigin))) {
            return self::BlankCorsResponse(403, '403 - Origin Not Allowed');
        }

        // If method is not allowed, don't continue
        if (isset($_SERVER['REQUEST_METHOD']) && !str_contains(strtolower($this->_cors->AllowMethods), strtolower($_SERVER['REQUEST_METHOD']))) {
            return self::BlankCorsResponse(403, '403 - Method "' . $_SERVER['REQUEST_METHOD'] . '" Not Allowed');
        }

        return $this->AddCorsToResponse($handler->handle($request));
    }

    /** @noinspection PhpSameParameterValueInspection */

    /**
     * @param int $pHttpStatusCode
     * @param string $pData
     * @param string $pContentType
     * @return Response
     */
    private function BlankCorsResponse(int $pHttpStatusCode, string $pData = '', string $pContentType = 'text/plain'): Response
    {
        $response = new Response();
        $response = $this->AddCorsToResponse($response);
        $response = $response->withHeader('Content-Type', $pContentType);
        $response->getBody()->write($pData);
        return $response->withStatus($pHttpStatusCode);
    }

    /**
     * @param Response $pResponse
     * @return Response
     */
    private function AddCorsToResponse(ResponseInterface $pResponse): Response
    {
        $response = $pResponse;
        $response = $response->withHeader('Access-Control-Allow-Credentials', $this->_cors->AllowCredentials);
        $response = $response->withHeader('Access-Control-Allow-Headers', $this->_cors->AllowHeaders);
        $response = $response->withHeader('Access-Control-Allow-Methods', $this->_cors->AllowMethods);
        $response = $response->withHeader('Access-Control-Allow-Origin', $this->_cors->AllowOrigin);
        $response = $response->withHeader('Access-Control-Max-Age', $this->_cors->MaxAge);
        $response = $response->withHeader('Access-Control-Expose-Headers', $this->_cors->ExposeHeaders);
        return $response;
    }
}
