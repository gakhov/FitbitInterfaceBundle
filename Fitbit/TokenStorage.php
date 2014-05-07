<?php
namespace NibyNool\FitBitBundle\FitBit;

use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Storage\Memory;
use OAuth\Common\Storage\Session;

/**
 * Class TokenStorage
 *
 * @package NibyNool\FitBitBundle\FitBit
 *
 * @since 0.1.0
 */
class TokenStorage
{
	/**
	 * @var StdOAuth1Token
	 */
	protected $token;
	/**
	 * @var Memory
	 */
	protected $adapter;

	/**
	 * Constructor for the token storage
	 *
	 * @access public
	 * @version 0.1.1
	 *
	 * @param string $storage The storage to use for the token
	 * @param string $token  The token to be added to the storage if this is pre-authorised
	 * @param string $secret The secret associated with the token.
	 */
	public function __construct($storage = 'memory', $token = null, $secret = null)
	{
		$this->token = new StdOAuth1Token();
		if ($storage == 'memory') $this->adapter = new Memory();
		elseif ($storage == 'session') $this->adapter = new Session();
		if ($token !== null && $secret !== null)
		{
			$this->token->setRequestToken($token);
			$this->token->setRequestTokenSecret($secret);
			$this->token->setAccessToken($token);
			$this->token->setAccessTokenSecret($secret);
			$this->adapter->storeAccessToken('FitBit', $this->token);
		}
	}

	/**
	 * Get the storage adapter
	 *
	 * @access public
	 * @version 0.1.1
	 *
	 * @return Memory|Session
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}
}