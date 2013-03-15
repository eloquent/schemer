<?php

require __DIR__.'/../../vendor/autoload.php';

$uri = new Eloquent\Schemer\Uri\DataUri('data:,A%20brief%20note');
$uri->setEncoding('base64');
$uri->setMimeType('text/plain;encoding=utf-8');

var_dump($uri, $uri->toString(), $uri->getMimeType(), $uri->getEncoding(), $uri->getRawData(), $uri->getData());
