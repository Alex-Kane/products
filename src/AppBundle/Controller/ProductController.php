<?php 

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use AppBundle\Entity\Product;

class ProductController extends Controller
{
	/**
	 * @Route("/product", name="product_create")
	 * @Method({"GET", "POST"})
	 */
	public function createAction(Request $request)
	{
		if ($request->getMethod() == Request::METHOD_POST
			&& $this->isCsrfTokenValid('product_form', $request->get('_csrf_token'))) {
			$product = new Product();
			$product->setName($request->get('name'))
				    ->setDescription($request->get('description'))
				    ->setPicture($request->files->get('picture'))
				    ->setUser($this->getUser());

			$validationErrors = $this->get('validator')->validate($product);
			if (!count($validationErrors)) {
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($product);
				$entityManager->flush();
				$this->addFlash('notice', 'Product was saved');
				return $this->redirectToRoute('main_page');
			}
		}
		$formData = [
			'errors' => isset($validationErrors) ? $validationErrors : [],
			'product' => isset($product) ? $product : null
		];
		return $this->render('products/form.html.twig', $formData);
	}

	/**
	 * @Route("/product/{id}", name="product_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAction(Request $request, $id)
	{
		$entityManager = $this->getDoctrine()->getManager();
	    $product = $entityManager->getRepository('AppBundle:Product')->find($id);
		if (!$product) {
			throw $this->createNotFoundException('Product not found');
		}
		if ($product->getUser()->getId() != $this->getUser()->getId()) {
			throw $this->createAccessDeniedException();
		}

		if ($request->getMethod() == Request::METHOD_POST
		    && $this->isCsrfTokenValid('product_form', $request->get('_csrf_token'))) {
			$product->setName($request->get('name'))
				    ->setDescription($request->get('description'))
				    ->setPicture($request->files->get('picture'));

			$validationErrors = $this->get('validator')->validate($product);
			if (!count($validationErrors)) {
				$entityManager->persist($product);
				$entityManager->flush();
				$this->addFlash('notice', 'Product was saved');
				return $this->redirectToRoute('main_page');
			}
		}
		$formData = [
			'errors' => isset($validationErrors) ? $validationErrors : [],
			'product' => $product
		];
		return $this->render('products/form.html.twig', $formData);
	}

	/**
	 * @Route("/product/{id}/delete", name="product_delete")
	 * @Method("GET")
	 */
	public function deleteAction(Request $request, $id)
	{
		if ($this->isCsrfTokenValid('product_delete', $request->get('_csrf_token'))) {
			$entityManager = $this->getDoctrine()->getManager();
			$product = $entityManager->getRepository('AppBundle:Product')->find($id);
			if (!$product) {
				throw $this->createNotFoundException('Product not found');
			}
			if ($product->getUser()->getId() != $this->getUser()->getId()) {
				throw $this->createAccessDeniedException();
			}
			$entityManager->remove($product);
			$entityManager->flush();
			$this->addFlash('notice', 'Product was deleted');
		}
		return $this->redirectToRoute('main_page');
	}
}
