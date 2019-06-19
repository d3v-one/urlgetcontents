/*
This is a replacement for the function file_get_contents when using an URI.

The problem with file_get_contents for URIs is that the function waits for the connection to be closed by the
remote side before returning. When the connection stays open, the function runs into a connection timeout of
typically 120 seconds. Normally, two possible remedies are suggested for that: sending a "Connection" header
with the argument "close" or setting the connection timeout to the lowest possible value:

	$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n', 'timeout' => 2)));
	$content = file_get_contents($url, false, $params);

This may or may not work. The best way is obviously to parse the "Content-Length" header sent by the remote
server and to close the connection when all data is received.

This replacement version does not use the "offset" or "maxlen" arguments of the original, which may very easily
be added.
*/

function url_get_contents($url, $context = NULL)
{
	const BUFSIZE = 8192;

	if ($context) {
		$conn = fopen($url, "rb", FALSE, $context);
	} else {
		$conn = fopen($url, "rb");
	}
	if ($conn === FALSE) return FALSE;

	$data = '';

  static $regex = '/^Content-Length: *+\K\d++$/im';
  if (isset($http_response_header) && preg_match($regex, implode("\n", $http_response_header), $matches)) {
    $size = (int)$matches[0];
 		while ($size > BUFSIZE) {
			$data .= fread($conn, BUFSIZE);
			$size -= BUFSIZE;
		}
		$data .= fread($conn, $size);
  } else {
		while (!feof($conn)) {
			$data .= fread($conn, BUFSIZE);
		}
	}
	fclose($conn);
	return $data;
}

