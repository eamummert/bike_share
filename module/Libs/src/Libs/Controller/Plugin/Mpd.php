<?php

namespace Libs\Controller\Plugin;

use Libs\Mpd\Mpd as LibsMpd;

class Mpd extends AbstractPlugin
{
	protected $host = 'localhost';

	protected $port = 6600;

	protected $password = null;

	public function __construct($mpdOptions)
	{
		if ($mpdOptions['host']) $this->host = $mpdOptions['host'];
		if ($mpdOptions['port']) $this->port = $mpdOptions['port'];
		if ($mpdOptions['password']) $this->password = $mpdOptions['password'];
	}
	
	public function __invoke($host = '', $port = '', $password = '')
	{
		if ($host) $this->host = $host;
		if ($port) $this->port = $port;
		if ($password) $this->password = $password;

		$mpd = new LibsMpd($this->host, $this->port, $this->password);

		return $mpd;
	}
}

