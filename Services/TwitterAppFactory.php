<?php

namespace Inori\TwitterAppBundle\Services;

use Arulu\General\SocialMediaBundle\Service\Publisher\TwitterPublisher;
use \TwitterOAuth;

class TwitterAppFactory
{
	private $config, $apps, $apis, $publishers, $appClass;

	public function __construct(array $config, $appClass)
	{
		$this->config = $config;
		$this->appClass = $appClass;
		$this->apps = [];
		$this->apis = [];
		$this->publishers = [];
	}

	private function throwUndefined($key)
	{
		throw new \InvalidArgumentException("Undefined configuration key $key");
	}

	public function getApi($key)
	{
		if(!isset($this->config[$key]))
			$this->throwUndefined($key);

		if(!isset($this->apis[$key]))
		{
			include_once $this->config[$key]['file'];
			$this->apis[$key] = new TwitterOAuth(
				$this->config[$key]['consumer_key'],
				$this->config[$key]['consumer_secret'],
				$this->config[$key]['oauth_token'],
				$this->config[$key]['oauth_token_secret']
			);
		}

		return $this->apis[$key];
	}

	public function get($key)
	{
		return $this->getApp($key);
	}

	public function getApp($key)
	{
		if(!isset($this->config[$key]))
			$this->throwUndefined($key);

		if(!isset($this->apps[$key]))
		{
			$this->apps[$key] = new $this->appClass($this->getApi($key));
		}

		return $this->apps[$key];
	}

	public function getPublisher($key)
	{
		if(!isset($this->config[$key]))
			$this->throwUndefined($key);

		if(!isset($this->publishers[$key]))
		{
			$this->publishers[$key] = new TwitterPublisher($this->getApp($key));
		}

		return $this->publishers[$key];
	}

	public function has($key)
	{
		return isset($this->config[$key]);
	}
}