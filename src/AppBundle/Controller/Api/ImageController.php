<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Route;

class ImageController extends FOSRestController implements ClassResourceInterface
{
	/**
	 * Get image
	 *
	 * @Route(requirements={"_format"="jpeg|png|gif"})
	 * @View(statusCode=200)
	 */
	public function getAction($name)
	{
		return $name;
	}
}
