<?php namespace Orchestra\Auth;

class Guard extends \Illuminate\Auth\Guard {
	
	/**
	 * Cached user to roles relationship.
	 * 
	 * @var array
	 */
	protected $userRoles = null;

	/**
	 * Get the current user's roles of the application.
	 *
	 * If the user is a guest, empty array should be returned.
	 *
	 * @return array
	 */
	public function roles()
	{
		$user   = $this->user();
		$roles  = array();
		$userId = 0;

		// This is a simple check to detect if the user is actually logged-in,
		// otherwise it's just as the same as setting userId as 0.
		is_null($user) or $userId = $user->id;

		// This operation might be called more than once in a request, by 
		// cached the event result we can avoid duplicate events being fired.
		if ( ! isset($this->userRoles[$userId]) or is_null($this->userRoles[$userId]))
		{
			$this->userRoles[$userId] = $this->events->until('orchestra.auth: roles', array(
				$user, 
				$roles,
			));
		}

		// It possible that after event are all propagated we don't have a 
		// roles for the user, in this case we should properly append "Guest" 
		// user role to the current user.
		if (is_null($this->userRoles[$userId]))
		{
			$this->userRoles[$userId] = array('Guest');
		}

		return $this->userRoles[$userId];
	}

	/**
	 * Determine if current user has the given role.
	 *
	 * @param  string   $roles
	 * @return boolean
	 */
	public function is($roles)
	{
		$userRoles = $this->roles();

		// In events where user roles return anything other than an array, 
		// just as a pre-caution.
		if ( ! is_array($userRoles)) return false;

		foreach ((array) $roles as $role)
		{
			if ( ! in_array($role, $userRoles)) return false;
		}

		return true;
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return void
	 */
	public function logout()
	{
		parent::logout();
		
		$this->userRoles = null;
	}
}
