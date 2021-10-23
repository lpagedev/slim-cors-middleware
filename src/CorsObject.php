<?php

namespace lpagedev\Middleware;

class CorsObject
{
    public string $AllowCredentials = 'False';
    public string $AllowHeaders = 'Origin, Content-Type';
    public string $AllowMethods = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
    public string $AllowOrigin = '*';
    public string $MaxAge = '86400';
    public string $ExposeHeaders = '';
}
