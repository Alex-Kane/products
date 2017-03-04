<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

class UserController extends Controller
{
	/**
	 * @Route("/login", name="login")
	 * @Method({"GET", "POST"})
	 */
	public function loginAction(Request $request)
	{
		$authUtils = $this->get('security.authentication_utils');
		$error = $authUtils->getLastAuthenticationError();
		$lastUsername = $authUtils->getLastUsername();

		return $this->render('user/login_form.html.twig', [
				'error' => $error,
				'last_username' => $lastUsername
			]);
	}

	/**
	 * @Route("/signup", name="signup")
	 * @Method({"GET", "POST"})
	 */
	public function signupAction(Request $request)
	{
		if ($request->getMethod() == Request::METHOD_POST) {
			$user = new User();
			$user->setUsername($request->get('username'))
				 ->setPlainPassword($request->get('password'))
				 ->setConfirmPlainPassword($request->get('confirm_password'))
				 ->setEmail($request->get('email'))
				 ->setFirstName($request->get('firstname'))
				 ->setLastname($request->get('lastname'));

			$validationErrors = $this->get('validator')->validate($user);
			if (!count($validationErrors)) {
				$encoder = $this->get('security.password_encoder');
				$password = $encoder->encodePassword($user, $user->getPlainPassword());
				$user->setPassword($password);

				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($user);
				$entityManager->flush();
				$this->addFlash('notice', 'You was successfully registered. Now you can login.');
				return $this->redirectToRoute('login');
			}
		}
		$formData = [
			'errors' => isset($validationErrors) ? $validationErrors : [],
			'user' => isset($user) ? $user : null
		];
		return $this->render('user/signup_form.html.twig', $formData);
	}
}
