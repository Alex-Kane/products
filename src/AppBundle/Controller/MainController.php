<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class MainController extends Controller
{
	/**
	 * @Route("/", name="main_page")
	 * @Method("GET")
	 */
	public function indexAction()
	{
		$products = $this->getDoctrine()->getRepository('AppBundle:Product')
		                 ->findAllNewest();
		return $this->render('main.html.twig', ['products' => $products]);
	}
}
