## About

This is a replacement for the function `file_get_contents` when using an URI.

The problem with `file_get_contents` for URIs is that the function waits for the connection to be closed by the
remote side before returning. When the connection stays open, the function runs into a connection timeout of
typically 120 seconds. Normally, two possible remedies are suggested for that: sending a "Connection" header
with the argument "close" or setting the connection timeout to the lowest possible value:

```php
$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n', 'timeout' => 2)));
$content = file_get_contents($url, false, $params);
```

This may or may not work. The best way is obviously to parse the "Content-Length" header sent by the remote
server and to close the connection when all data is received.

This replacement version does not use the "offset" or "maxlen" arguments of the original, which may very easily
be added.

## Author

**Michael Koch** [d3v.one](https://d3v.one)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

