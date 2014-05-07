<?php
namespace NibyNool\FitBitBundle\FitBit;

use NibyNool\FitBitBundle\FitBit\Exception as FBException;

/**
 * Class UserGateway
 *
 * @package NibyNool\FitBitBundle\FitBit
 *
 * @since 0.1.0
 */
class UserGateway extends EndpointGateway {

    /**
     * Valid subscription types mapped to their collection paths.
     * @var array
     */
    protected $subscriptionTypes = array(
        'sleep'      => '/sleep',
        'body'       => '/body',
        'activities' => '/activities',
        'foods'      => '/foods',
        'all'        => '',
        ''           => '',
    );

    /**
     * API wrappers
     *
     * @access public
     * @version 0.1.1
     *
     * @return object
     */
    public function getProfile()
    {
        return $this->makeApiRequest('user/' . $this->userID . '/profile');
    }

    /**
     * Update user profile with array of parameters.
     *
     * @access public
     *
     * @param array $parameters
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function updateProfileFromArray($parameters)
    {
        return $this->makeApiRequest('user/' . $this->userID . '/profile', 'POST', $parameters);
    }

    /**
     * Update user profile
     *
     * @access public
     *
     * @param string $gender 'FEMALE', 'MALE' or 'NA'
     * @param \DateTime $birthday Date of birth
     * @param string $height Height in cm/inches (as set with setMetric)
     * @param string $nickname Nickname
     * @param string $fullName Full name
     * @param string $timezone Timezone in the format 'America/Los_Angeles'
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function updateProfile($gender = null, \DateTime $birthday = null, $height = null, $nickname = null, $fullName = null, $timezone = null)
    {
        $parameters = array();
        if ($gender)   $parameters['gender'] = $gender;
        if ($birthday) $parameters['birthday'] = $birthday->format('Y-m-d');
        if ($height)   $parameters['height'] = $height;
        if ($nickname) $parameters['nickname'] = $nickname;
        if ($fullName) $parameters['fullName'] = $fullName;
        if ($timezone) $parameters['timezone'] = $timezone;

        return $this->updateProfileFromArray($parameters);
    }

    /**
     * Get list of devices and their properties
     *
     * @access public
     *
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getDevices()
    {
        return $this->makeApiRequest('user/-/devices');
    }

    /**
     * Get user friends
     *
     * @access public
     *
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getFriends()
    {
        return $this->makeApiRequest('user/' . $this->userID . '/friends');
    }

    /**
     * Get user's friends leaderboard
     *
     * @access public
     * @version 0.1.1
     *
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getFriendsLeaderboard()
    {
        return $this->makeApiRequest('user/-/friends/leaderboard');
    }

	/**
	 * Get friend invites
	 *
	 * @access public
	 * @version 0.5.0
	 *
	 * @return mixed SimpleXMLElement or the value encoded in json as an object
	 */
	public function getInvites()
	{
		return $this->makeApiRequest('user/-/friends/invitations');
	}

    /**
     * Invite user to become friends
     *
     * @access public
     *
     * @param string $userId Invite user by id
     * @param string $email Invite user by email address (could be already FitBit member or not)
     * @return bool
     */
    public function inviteFriend($userId = null, $email = null)
    {
        $parameters = array();
        if (isset($userId)) $parameters['invitedUserId'] = $userId;
        if (isset($email)) $parameters['invitedUserEmail'] = $email;

        return $this->makeApiRequest('user/-/friends/invitations', 'POST', $parameters);
    }

    /**
     * Accept invite to become friends from user
     *
     * @access public
     *
     * @param string $userId Id of the inviting user
     * @return bool
     */
    public function acceptFriend($userId)
    {
        $parameters = array();
        $parameters['accept'] = 'true';

        return $this->makeApiRequest('user/-/friends/invitations/' . $userId, 'POST', $parameters);
    }

    /**
     * Reject invite to become friends from user
     *
     * @access public
     * @version 0.5.0
     *
     * @param string $userId Id of the inviting user
     * @return bool
     */
    public function rejectFriend($userId)
    {
        $parameters = array();
        $parameters['accept'] = 'false';

        return $this->makeApiRequest('user/-/friends/invitations/' . $userId, 'POST', $parameters);
    }

	/**
	 * Get badges
	 *
	 * @access public
	 * @version 0.5.0
	 *
	 * @return mixed SimpleXMLElement or the value encoded in json as an object
	 */
	public function getBadges()
	{
		return $this->makeApiRequest('user/-/badges');
	}

    /**
     * Add subscription
     *
     * @access public
     *
     * @param string $id Subscription ID
     * @param string $subscriptionType Collection type
     * @param string $subscriberId The ID of the subscriber
     * @return mixed
     */
    public function addSubscription($id, $subscriptionType = 'all', $subscriberId = null)
    {
        return $this->makeApiRequest(
            $this->makeSubscriptionUrl($id, $subscriptionType),
            'POST',
            array(),
            $this->makeSubscriptionHeaders($subscriberId)
        );
    }

    /**
     * Delete user subscription
     *
     * @access public
     *
     * @param string $id Subscription Id
     * @param string $subscriptionType Collection type
     * @param string $subscriberId The ID of the subscriber
     * @return bool
     */
    public function deleteSubscription($id, $subscriptionType = 'all', $subscriberId = null)
    {
        return $this->makeApiRequest(
            $this->makeSubscriptionUrl($id, $subscriptionType),
            'DELETE',
            array(),
            $this->makeSubscriptionHeaders($subscriberId)
        );
    }

    /**
     * Validate user subscription type
     *
     * @access protected
     *
     * @param string &$subscriptionType Collection type
     * @throws FBException
     * @return bool
     */
    protected function validateSubscriptionType(&$subscriptionType)
    {
        if (isset($this->subscriptionTypes[$subscriptionType])) {
            $subscriptionType = $this->subscriptionTypes[$subscriptionType];
        } else {
            throw new FBException(sprintf('Invalid subscription collection type (valid values are \'%s\')',
                implode("', '", array_keys($this->subscriptionTypes))
            ));
        }
        return true;
    }

    /**
     * Create headers for subscription requests.
     *
     * @access protected
     *
     * @param string $subscriberId The ID of the subscriber
     * @return array
     */
    protected function makeSubscriptionHeaders($subscriberId = null)
    {
        $headers = array();
        if ($subscriberId) $headers['X-FitBit-Subscriber-Id'] = $subscriberId;
        return $headers;
    }

    /**
     * Create the subscription request URL
     *
     * @access protected
     *
     * @param string $id Subscription Id
     * @param string $subscriptionType subscriptionType resource path
     * @return string
     */
    protected function makeSubscriptionUrl($id, $subscriptionType)
    {
        $this->validateSubscriptionType($subscriptionType);
        
        return sprintf('user/%s%s/apiSubscriptions%s',
            $this->userID,
            $subscriptionType,
            ($id ? '/' . $id : '')
        );
    }

    /**
     * Get list of user's subscriptions for this application
     *
     * @access public
     *
     * @return mixed
     */
    public function getSubscriptions()
    {
        return $this->makeApiRequest($this->makeSubscriptionUrl(null, null));
    }
}
