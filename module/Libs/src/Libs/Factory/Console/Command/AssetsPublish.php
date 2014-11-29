<?php

namespace Libs\Factory\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Libs\Console\Command;

class AssetsPublish extends AbstractCommandFactory
{
	public function configure()
	{
		$this->adapter
			->setName('assets:publish')
			->setDescription('Publish assets from all modules to the <comment>public/modules</comment> directory')
			->addOption(
				'module',
				null,
				InputOption::VALUE_REQUIRED,
				'The module to use for this command.'
			)
			->addOption(
				'cleanup',
				null,
				InputOption::VALUE_NONE,
				'Remove items from the destination directory that no longer exist.'
			)
		;
	}

	protected function create()
	{
		return new Command\AssetsPublish($this->service('ModuleManager'));
	}
}

