<?php
/**
 *
 * Error Codes: 201 - 206
 */
namespace Nibynool\FitbitInterfaceBundle\Fitbit;

use OAuth\OAuth2\Token\TokenInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Nibynool\FitbitInterfaceBundle\Fitbit\Exception as FBException;

/**
 * Class AuthenticationGateway
 *
 * @package Nibynool\FitbitInterfaceBundle\Fitbit
 *
 * @since 0.1.0
 */
class AuthenticationGateway extends EndpointGateway
{
	/**
	 * Determine if this user is authorised with Fitbit
	 *
	 * @access public
	 * @version 0.5.0
	 *
	 * @throws FBException
	 * @return bool
	 */
	public function isAuthorized()
    {
        try
        {
	        return $this->service->getStorage()->hasAccessToken('FitBit');
        }
        catch (\Exception $e)
        {
	        throw new FBException('Could not find the access token.', 206, $e);
        }
    }

    /**
     * Determine if access token is expired and refresh it
     *
     * @access public
     * @version 0.0.1
     *
     * @throws FBException
     * @return bool
     */
    public function refreshTokenIfRequired()
    {
        try
        {
            $accessToken = $this->service->getStorage()->retrieveAccessToken('FitBit');
            if ($accessToken->isExpired() !== TRUE) {
                return FALSE;
            }
            $this->service->refreshAccessToken($accessToken);
            return TRUE;
        }
        catch (\Exception $e)
        {
            throw new FitBitException('Could not refresh the access token.', 202, $e);
        }
    }

    /**
     * Initiate the login process
     *
     * @access public
     * @version 0.5.0
     *
     * @throws FBException
     * @return void
     */
    public function initiateLogin()
    {
        $url = $this->service->getAuthorizationUri();
        if (!filter_var($url, FILTER_VALIDATE_URL)) throw new FitBitException('FitBit returned an invalid login URL ('.$url.').', 201);
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Authenticate user, request access token.
     *
     * @access public
     * @version 0.5.2
     *
     * @param string $token
     * @param string $verifier
     * @throws FBException
     * @return TokenInterface
     */

    public function authenticateUser($code)
    {
        /** @var Stopwatch $timer */
        $timer = new Stopwatch();
        $timer->start('Authenticating User', 'FitBit API');

        try
        {
            /** @var TokenInterface $tokenResponse */
            $tokenResponse = $this->service->requestAccessToken(
                $code
            );
            $timer->stop('Authenticating User');
            return $tokenResponse;
        }
        catch (\Exception $e)
        {
            $timer->stop('Authenticating User');
            throw new FBException('Unable to request the access token.', 203, $e);
        }
    }

    /**
     * Reset session
     *
     * @access public
     * @version 0.5.2
     *
     * @todo Need to add clear to the interface for phpoauthlib (this item was here when this project was branched)
     *
     * @throws FBException
     * @return void
     */
    public function resetSession()
    {
	    /** @var Stopwatch $timer */
	    $timer = new Stopwatch();
	    $timer->start('Resetting Session', 'Fitbit API');

	    try
	    {
		    $this->service->getStorage()->clearToken('FitBit');
	    }
	    catch (\Exception $e)
	    {
		    $timer->stop('Resetting Session');
		    throw new FBException('Could not clear the token.', 204);
	    }
	    $timer->stop('Resetting Session');
    }

	/**
	 * Verify the token
	 *
	 * @access protected
	 * @version 0.5.0
	 *
	 * @throws Exception
	 * @return bool
	 */
	protected function verifyToken()
    {
        if (!$this->isAuthorized()) throw new FBException('Token could not be verified.', 205);
	    return true;
    }
}
