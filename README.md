Castor Incubator
================

Unstable apis and libraries from Castor Labs

```
composer require castor/incubator dev-main
```

> NOTE: This library is completely unstable, lacks proper testing and
> breaks apis constantly. Use it just for experimental purposes.

## Documentation

### Http

In the `Net\Http` namespace you'll find an incomplete implementation of
the HTTP protocol. This implementation is mutable, and heavily object
oriented. It is much based on in the Golang implementation.

Inside it, there is a CGI implementation that you can use to quickly
create PHP web applications that sit under a web server like Apache, Nginx,
Caddy or others.

It is very easy to create a simple application:

```php

use function Castor\Net\Http\Cgi\serve;
use function Castor\Net\Http\handlerFunc;
use Castor\Net\Http\Request;
use Castor\Net\Http\ResponseWriter;

// First, we define a simple function that writes a response
function hello(ResponseWriter $writer, Request $request): void {
    $writer->getHeaders()->add('Content-Type', 'text/html');
    $writer->write(sprintf(
        '<h1>Hello world!</h1><p>The requested path is %s</p>',
        $request->getUri()->getPath()
    ));
}

// Then, we convert that function into a Castor\Net\Http\Handler
$handler = handlerFunc('hello');

// Then, we use the serve method to handle the CGI request.
serve($handler);
```