<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use AppBundle\Entity\Product;

class ProductController extends FOSRestController implements ClassResourceInterface
{
	/**
	 * Get all products
	 *
	 * @View(statusCode=200)
	 */
	public function cgetAction()
	{
		$products = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product')->findAllNewest();
		return $products;
	}

	/**
	 * Get product
	 *
	 * @Get("/products/{id}")
	 * @View(statusCode=200)
	 */
	public function getAction(Product $product)
	{
		return $product;
	}

	/**
	 * Update product
	 *
	 * @Put("/products/{id}")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 */
	public function putAction(Product $product, Request $request)
	{
		if ($this->getUser()->getId() != $product->getUser()->getId()) {
			throw $this->createAccessDeniedException();
		}
		$product->setName($request->get('name'))
		        ->setDescription($request->get('description'))
		        ->setPicture($request->files->get('picture'));
		$validationErrors = $this->get('validator')->validate($product);
		if (count($validationErrors) > 0) {
			return $this->view(['errors' => $validationErrors], 400);
		}
		$this->getDoctrine()->getManager()->flush();

		return $this->view(null, 204);
	}

	/**
	 * Create new product
	 *
	 * @Post("/products")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 */
	public function postAction(Request $request)
	{
		$product = new Product();
		$product->setName($request->get('name'))
		        ->setDescription($request->get('description'))
		        ->setPicture($request->files->get('picture'))
		        ->setUser($this->getUser());
		$validationErrors = $this->get('validator')->validate($product);
		if (count($validationErrors) > 0) {
			return $this->view(['errors' => $validationErrors], 400);
		}
		$em = $this->getDoctrine()->getManager();
		$em->persist($product);
		$em->flush();

		$locationUrl = $this->generateUrl('get_product', ['id' => $product->getId()]);
		return $this->view(null, 201)
		            ->setHeader('Location', $locationUrl);
	}

	/**
	 * Delete product
	 *
	 * @Delete("/products/{id}")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 */
	public function deleteAction(Product $product)
	{
		if ($this->getUser()->getId() != $product->getUser()->getId()) {
			throw $this->createAccessDeniedException();
		}
		$em = $this->getDoctrine()->getManager();
		$em->remove($product);
		$em->flush();
		return $this->view(null, 204);
	}
}
