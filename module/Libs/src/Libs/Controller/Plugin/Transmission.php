<?php

namespace Libs\Controller\Plugin;

use Transmission\Client;
use Transmission\Transmission as Trans;

class Transmission extends AbstractPlugin
{
	protected $host = 'localhost';

	protected $port = 9091;

	protected $username = '';

	protected $password = '';

	public function __construct($tmdOptions)
	{
		if ($tmdOptions['host']) $this->host = $tmdOptions['host'];
		if ($tmdOptions['port']) $this->port = $tmdOptions['port'];
		if ($tmdOptions['username']) $this->username = $tmdOptions['username'];
		if ($tmdOptions['password']) $this->password = $tmdOptions['password'];
	}
	
	public function __invoke($host = '', $port = '', $username = '', $password = '')
	{
		if ($host) $this->host = $host;
		if ($port) $this->port = $port;
		if ($username) $this->username = $username;
		if ($password) $this->password = $password;

		$trans = new Trans($this->host, $this->port);
		if ($this->username || $this->password)
		{
			$client = new Client();
			$client->authenticate($this->username, $this->password);
			$trans->setClient($client);
		}

		return $trans;
	}
}

