<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{
	/**
	 * Find all products, newest first
	 *
	 * @return array
	 */
	public function findAllNewest()
	{
		$query = $this->createQueryBuilder('p')
		              ->orderBy('p.createdAt', 'DESC')
		              ->getQuery();
		return $query->getResult();
	}
}
