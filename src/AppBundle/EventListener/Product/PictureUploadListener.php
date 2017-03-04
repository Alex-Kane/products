<?php

namespace AppBundle\EventListener\Product;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Services\FileManager;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploadListener
{
	protected $_fileManager;

	protected $_targetDir;

	public function __construct(FileManager $fileManager, $targetDir)
	{
		$this->_fileManager = $fileManager;
		$this->_targetDir = $targetDir;
	}

	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof Product) {
			$this->_uploadPicture($entity);
		}
	}

	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof Product) {
			$this->_uploadPicture($entity);
		}
	}

	protected function _uploadPicture(Product $product)
	{
		$picture = $product->getPicture();
		if (!$picture instanceof UploadedFile) {
			$product->setPicture(null);
			return;
		}
		$newFileName = $this->_fileManager->upload($picture, $this->_targetDir);
		$product->setPicture($newFileName);
	}
}
