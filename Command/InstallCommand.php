<?php

namespace NS\CatalogBundle\Command;

use Doctrine\ORM\EntityManager;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CmsBundle\Entity\Area;
use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Entity\BlockType;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Entity\PageRepository;
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

		$output->writeln("\n");

		// pages
		$rootPage     = $this->getPageRepository()->findRootPageOrCreate();
		$mainPage     = $this->findPageOrCreate($output, 'main',     $rootPage,    '??????????????');
		$catalogPage  = $this->findPageOrCreate($output, 'catalog',  $mainPage,    '??????????????');
		$itemPage     = $this->findPageOrCreate($output, 'item',     $catalogPage, '???????????????? ????????????');
		$categoryPage = $this->findPageOrCreate($output, 'category', $catalogPage, '???????????????? ??????????????????');

		// adding blocks
		$area = new Area();
		$area->setTitle('??????????????');
		$area->setName('content');
		$output->writeln("Adding default blocks to area '<comment>content<comment>'");

		// @todo add blocks

		$output->writeln("<info>Done!</info>\n");

		return 0;
	}

	/**
	 * @param OutputInterface $output
	 * @param string          $name
	 * @param Page            $parent
	 * @param string          $title
	 * @return Page
	 */
	private function findPageOrCreate(OutputInterface $output, $name, Page $parent, $title)
	{
		$output->writeln("Searching for page '<comment>{$name}</comment>'");
		$page = $this->getPageRepository()->findOneByName($name);
		if (!$page) {
			$output->writeln("Page wasn't found. Creating page named '<comment>{$name}</comment>' with parent '<comment>{$parent->getName()}</comment>'");

			$page = new Page();
			$page->setName($name);
			$page->setParent($parent);
			$page->setTitle($title);

			$this->getEntityManager()->persist($page);
			$this->getEntityManager()->flush();
		}
		$output->writeln("Page '<comment>$name</comment>' <comment>#{$page->getId()}</comment> found\n");
		return $page;
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
	 * @return PageRepository
	 */
	private function getPageRepository()
	{
		return $this
			->getContainer()
			->get('ns_cms.repository.page');
	}

	/**
	 * @return EntityManager
	 */
	private function getEntityManager()
	{
		return $this
			->getContainer()
			->get('doctrine.orm.entity_manager');
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
