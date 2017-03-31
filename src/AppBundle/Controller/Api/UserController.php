<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

class UserController extends FOSRestController implements ClassResourceInterface
{
	/**
	 * Get user
	 *
	 * @Get("/users/current")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 */
	public function getCurrentAction()
	{
		return $this->view($this->getUser());
	}

	/**
	 * Create new user
	 *
	 * @Post("/users")
	 */
	public function postAction(Request $request)
	{
		$user = new User();
		$user->setUsername($request->get('username'))
			 ->setFirstname($request->get('firstname'))
			 ->setLastname($request->get('lastname'))
			 ->setEmail($request->get('email'))
			 ->setPassword($request->get('password'))
			 ->setConfirmPassword($request->get('confirm_password'));
		$validationErrors = $this->get('validator')->validate($user);
		if (!count($validationErrors)) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();
			return $this->view(null, 201);
		}
		return $this->view(['errors' => $validationErrors], 400);		
	}
}
