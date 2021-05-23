Castor Incubator
================

Unstable apis and libraries from Castor Labs

```
composer require castor/incubator dev-main
```

> NOTE: This library is completely unstable, lacks proper testing and
> breaks apis constantly. Use it just for experimental purposes.

## Documentation

### `Io` Package

The `Castor\Io` namespace contains mostly interfaces to deal with input
output operations. These interfaces are directly ported from Golang's 
standard library.

The most notable interfaces are `Castor\Io\Reader` and `Castor\Io\Writer`.
Many of the classes of this composer package implement them, like the
`Castor\Os\File`.

### `Os` Package

The `Castor\Os` namespace contains classes to perform operations in the
underlying operating system, like reading files, directories, working
with paths and environment variables.

For example, to work with files:

```php
<?php

// Opens a file. Fails if the file does not exist
$file = Castor\Os\File::open('/some/file/in/the/local/filesystem');

// Opens a file and overwrites it if exists. Otherwise it creates it.
// It fails if the file cannot be created.
$file = Castor\Os\File::put('/some/file/in/the/local/filesystem');

// Creates a file. Fails if the file already exists.
$file = Castor\Os\File::make('/some/file/in/the/local/filesystem');
```

By default, `Castor\Os\File` implements both `Castor\Io\Reader` and
`Castor\Io\Writer`, which means you can write and/or read from it, and 
use it as an argument whenever either type is required.

The `Castor\Os\File` provides a useful api to work with it.

```php
<?php

// Opens a file. Fails if the file does not exist
$file = Castor\Os\File::open('/some/file.pdf');

echo $file->size();                     // Prints "34232" 
echo $file->getMimeType();              // Prints "application/pdf"
echo Castor\Io\readAll($file);          // Reads the contents of the file
```

Under the hood, a `Castor\Os\File` instance is nothing more than a wrapper
over a PHP resource. This means that has an internal 8k buffer that adds
some overhead. In the future, this library will rely on `ext_dio` to 
directly read files from the filesystem. Buffering will be provided on 
userland.

### `Castor\Net\Http` Package

In the `Castor\Net\Http` namespace you'll find an incomplete implementation of
the HTTP protocol. This implementation is mostly mutable and much 
based on in the Golang implementation.

Inside it, there is a CGI implementation that you can use to quickly
create PHP web applications that sit under a web server like Apache, Nginx,
Caddy or others.

It is very easy to create a simple application:

```php

use Castor\Net\Http;

// First, we define a simple function that writes a response
function hello(Http\ResponseWriter $writer, Http\Request $request): void {
    $writer->getHeaders()->add('Content-Type', 'text/html');
    $writer->write(sprintf(
        '<h1>Hello world!</h1><p>The requested path is %s</p>',
        $request->getUri()->getPath()
    ));
}

// Then, we convert that function into a Castor\Net\Http\Handler
$handler = Http\handlerFunc('hello');

// Then, we use the serve method to handle the CGI request.
Http\Cgi\serve($handler);
```
