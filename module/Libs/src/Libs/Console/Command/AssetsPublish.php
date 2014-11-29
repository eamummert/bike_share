<?php

namespace Libs\Console\Command;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Libs\Module\AbstractModule;
use Zend\ModuleManager\ModuleManager;

/**
 * Command line utility to publish module assets to public/modules.
 */
class AssetsPublish extends AbstractCommand
{
	protected $modules;

	public function __construct(ModuleManager $modules)
	{
		$this->modules = $modules;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$name = $input->getOption('module');
		if ($name)
		{
			$names = [$name];
		}
		else
		{
			$names = array_keys($this->modules->getLoadedModules());
		}

		$targetDir = 'public/modules';

		if (is_dir($targetDir) && !is_writable($targetDir))
		{
			throw new RuntimeException("Unable to write to the $targetDir directory");
		}
		if (!is_dir($targetDir))
		{
			$output->writeln("Creating <comment>{$targetDir}</comment> directory");
			mkdir($targetDir, 0755);
		}
		elseif ($input->getOption('cleanup'))
		{
			$files = scandir($targetDir);
			if (!empty($files))
			{
				$output->writeln("Cleaning up <comment>{$targetDir}</comment> directory");
				foreach ($files as $file)
				{
					if ($file == '.' || $file == '..')
					{
						continue;
					}

					$target = $targetDir .'/'. readlink($targetDir .'/'. $file);
					if (!file_exists($target))
					{
						unlink($targetDir .'/'. $file);
					}
				}
			}
		}

		$output->writeln('<info>Generating asset symlinks</info>');
		$nothing = true;

		foreach ($names as $name)
		{
			$module = $this->modules->getModule($name);
			if (!$module instanceof AbstractModule)
			{
				//continue;
			}
			$public = $module->getRootPath() .'/public';
			$link = $targetDir .'/'. $this->normalizePackageName($name);
			if (is_dir($public))
			{
				if (file_exists($link))
				{
					continue;
				}
				if (strpos($public, getcwd()) === 0)
				{
					$target = str_repeat('../', count(explode('/', $link)) - 1) . substr($public, strlen(getcwd()) + 1);
				}
				else
				{
					$target = $public;
				}
				$output->writeln(" - <info>{$name}</info> into <comment>{$link}</comment>");
				symlink($target, $link);

				$nothing = false;
			}
		}

		if ($nothing)
		{
			$output->writeln('Nothing to publish');
		}
	}
}

