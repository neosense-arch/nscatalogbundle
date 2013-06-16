<?php

namespace NS\CatalogBundle\Command;

use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use Sensio\Bundle\GeneratorBundle\Command\GenerateBundleCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class InstallCommand
 * @package NS\CatalogBundle\Command
 */
class InstallCommand extends ContainerAwareCommand
{
	/**
	 * Configuration
	 *
	 */
	protected function configure()
	{
		$this
			->setName('ns:catalog:install')
			->setDescription('Installs catalog bundle')
		;
	}

	/**
	 * Execution
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @throws \Exception
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("<info>Installing catalog bundle</info>\n");

		/** @var Kernel $kernel */
		$kernel = $this->getContainer()->get('kernel');

		// src dir
		$srcDir = realpath($kernel->getRootDir() . '/../src');
		if (!file_exists($srcDir)) {
			throw new \Exception("Src directory '{$srcDir}' wasn't found");
		}
		$srcDir = realpath($srcDir);
		$output->writeln("User bundles (src) directory:\n  <comment>{$srcDir}</comment>\n");


		/** @var DialogHelper $dialog */
		$dialog = $this->getHelper('dialog');

		// creating new user bundle if prefix is empty
		$prefix = $this->getDefaultUserBundlePrefix($srcDir);
		if (!$prefix) {
			$output->writeln("It seems like your src directory is empty.");
			if (!$dialog->askConfirmation($output, "Would you like to create a new bundle now? [<comment>yes</comment>] ")) {
				$output->writeln("<error>Canceled! No user bundle found</error>");
				return 1;
			}

			// launching bundle generation utility
			$output->writeln("");
			/** @var GenerateBundleCommand $bundleGenerationCommand */
			$bundleGenerationCommand = $this->getApplication()->find('generate:bundle');
			$bundleGenerationCommand->run(new ArrayInput(array('command' => 'generate:bundle')), $output);
			$output->writeln("");

			$prefix = $this->getDefaultUserBundlePrefix($srcDir);
			if (!$prefix) {
				$output->writeln("<error>It seems like your src directory is still empty</error>");
				return 2;
			}

			$output->writeln("Your bundle was created. You need to restart command");
			return 0;
		}

		// getting bundles list
		$bundles = array();
		$defaultBundle = null;
		foreach ($kernel->getBundles() as $bundle) {
			$bundles[] = $bundle->getName();
			if (substr($bundle->getName(), 0, strlen($prefix)) === $prefix) {
				$defaultBundle = $bundle->getName();
			}
		}

		// getting user bundle to add new form
		$userBundle = $dialog->ask(
			$output,
			"Please enter the name of your main user bundle: [<comment>{$defaultBundle}</comment>] ",
			$defaultBundle,
			$bundles
		);

		// @todo generate catalog settings form type
		// @todo generate catalog settings model
		// @todo add 'goods' catalog

		return 0;
	}

	/**
	 * @return CatalogRepository
	 */
	private function getCatalogRepository()
	{
		return $this
			->getContainer()
			->get('ns_catalog.repository.catalog');
	}

	/**
	 * @param  string $srcDir
	 * @return null|string
	 */
	private function getDefaultUserBundlePrefix($srcDir)
	{
		/** @var \SplFileInfo $dir */
		$prefix = null;
		foreach (new \DirectoryIterator($srcDir) as $dir) {
			if (substr($dir->getFilename(), 0, 1) !== '.' && $dir->isDir()){
				return $dir->getFilename();
			}
		}
		return null;
	}
}
