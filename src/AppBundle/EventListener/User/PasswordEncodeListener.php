<?php

namespace AppBundle\EventListener\User;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\User;

class PasswordEncodeListener
{
	private $_encoder;

	public function __construct($encoder)
	{
		$this->_encoder = $encoder;
	}

	public function prePersist(LifecycleEventArgs $args)
	{
		$user = $args->getEntity();
		if ($user instanceof User) {
			$this->_encodePassword($user);
		}
	}

	private function _encodePassword(User $user)
	{
		$encodedPassword = $this->_encoder->encodePassword($user, $user->getPassword());
		$user->setPassword($encodedPassword);
	}
}