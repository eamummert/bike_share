<?php

namespace Libs\Upload;

use Libs\Exception;
use Libs\Upload\Adapter\AdapterInterface;
use Libs\Upload\File\FileInterface;

class Container implements ContainerInterface
{
	/**
	 * Adapter used to perform the actual file operations.
	 *
	 * @var AdapterInterface
	 */
	protected $adapter;

	/**
	 * @param AdapterInterface $adapter
	 */
	public function __construct(AdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	public function upload($input)
	{
		if (is_array($input))
		{
			$input = new File\Input($input);
		}

		if (!$input instanceof File\FileInterface)
		{
			throw new Exception\InvalidArgumentException(sprintf(
				"Input must be an object implementing %s"
				, __NAMESPACE__ .'\File\FileInterface'
			));
		}

		$id = $this->generateSequence($input->getBasename());
		if ($this->has($id))
		{
			return $this->upload($input);
		}

		$this->adapter->upload($id, $input);

		return $id;
	}

	public function get($id)
	{
		if (empty($id))
		{
			throw new Exception\InvalidArgumentException('ID cannot be empty');
		}

		return $this->adapter->get($id);
	}

	public function download($id)
	{
		if (empty($id))
		{
			throw new Exception\InvalidArgumentException('ID cannot be empty');
		}

		$object = $this->adapter->get($id);
		$output = new File\File($object->getBasename(), $this->generateTempPath());

		$this->adapter->download($id, $output);

		return $output;
	}

	public function has($id)
	{
		if (empty($id))
		{
			throw new Exception\InvalidArgumentException('ID cannot be empty');
		}

		return $this->adapter->has($id);
	}

	public function delete($id)
	{
		if (empty($id))
		{
			throw new Exception\InvalidArgumentException('ID cannot be empty');
		}

		return $this->adapter->delete($id);
	}

	/**
	 * Generate a unique/random sequence.
	 *
	 * @param string $basename
	 *
	 * @return string
	 */
	protected function generateSequence($basename)
	{
		$basename = $this->sanitize($basename);

		$hash = hash('sha1', uniqid('', true) . mt_rand() . $basename);
		$prefix = implode(DIRECTORY_SEPARATOR, str_split(substr($hash, 0, 3)));

		return $prefix . DIRECTORY_SEPARATOR . $hash . DIRECTORY_SEPARATOR . $basename;
	}

	/**
	 * @return string
	 */
	protected function generateTempPath()
	{
		return tempnam(ini_get('upload_tmp_dir') ?: sys_get_temp_dir(), 'wdu');
	}

	/**
	 * @param string $basename
	 *
	 * @return string
	 */
	protected function sanitize($basename)
	{
		$filename = pathinfo($basename, PATHINFO_FILENAME);
		$extension = pathinfo($basename, PATHINFO_EXTENSION);

		$filename = str_replace(' ', '-', $filename);
		$filename = preg_replace('/[^a-z0-9\.\-\_]/i', '', $filename);
		$filename = substr($filename, 0, 100);

		$filename = trim($filename, '-_.');

		return $filename .'.'. $extension;
	}
}

