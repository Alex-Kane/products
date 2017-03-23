<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

class UserController extends FOSRestController implements ClassResourceInterface
{
	/**
	 * Get user
	 *
	 * @Get("/users/current")
	 */
	public function getCurrentAction()
	{
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
			return $this->view($this->getUser(), 200);
		}
	   return $this->view(null, 401)
	   			   ->setHeader('WWW-Authenticate', 'Bearer');
	}

	/**
	 * Create new user
	 *
	 * @Post("/users")
	 * @ParamConverter("user", converter="fos_rest.request_body")
	 */
	public function postAction(User $user, Request $request)
	{
		$user->setPassword($request->get('password'))
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