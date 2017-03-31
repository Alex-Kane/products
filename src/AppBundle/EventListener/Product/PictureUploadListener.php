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

	const DEFAULT_PICTURE_PATH = 'images/default/products/picture/default.png';

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
			$product->setPicture(self::DEFAULT_PICTURE_PATH);
			return;
		}
		$newFileName = $this->_fileManager->upload($picture, $this->_targetDir);
		$webPath = substr($this->_targetDir, strpos($this->_targetDir, 'web/') + 4);
		$product->setPicture($webPath . '/' . $newFileName);
	}
}
