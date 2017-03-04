<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
	/**
	 * Upload file into target directory
	 *
	 * @return string
	 */
	public function upload(UploadedFile $file, $targetDir)
	{
		$fileName = $this->_generateFileName($file);
		$file->move($targetDir, $fileName);
		return $fileName;
	}

	/**
	 * Generete unique file name
	 *
	 * @return string
	 */
	protected function _generateFileName(UploadedFile $file)
	{
		return md5(uniqid()) . '.' . $file->guessExtension();
	}
}
