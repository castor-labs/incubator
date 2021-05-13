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

The `Castor\Os\File` provides a useful api to work with files.

```php
<?php

// Opens a file. Fails if the file does not exist
$file = Castor\Os\File::open('/some/file.pdf');

echo $file->getSize();                  // Prints "34232" 
echo $file->getContentType();           // Prints "application/pdf"
echo $file->getPath()->getFilename();   // Prints "file.pdf"
echo $file->getPath()->getBasename();   // Prints "file"
echo $file->getPath()->getDirname();    // Prints "/some"
echo $file->getPath()->getExtension();  // Prints "pdf"
echo $file->getPath()->isAbsolute();    // Prints "true"
```

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