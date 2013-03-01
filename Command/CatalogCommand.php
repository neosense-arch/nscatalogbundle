<?php

namespace NS\CatalogBundle\Command;

use Doctrine\ORM\EntityManager;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Kernel;

class CatalogCommand extends ContainerAwareCommand
{
	/**
	 * Configuration
	 *
	 */
	protected function configure()
	{
		$this
			->setName('ns:catalog:create')
			->setDescription('Creates new catalog')
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
		$output->writeln("<info>Creating new catalog</info>");

		$container = $this->getContainer();

		// catalog name
		/** @var $dialog DialogHelper */
		$dialog = $this->getHelperSet()->get('dialog');
		$name = $dialog->askAndValidate($output, '<comment>Catalog name</comment> (lat.): ', function($value) use($container) {
			if (!$value) {
				throw new \Exception('Please enter catalog name');
			}
			/** @var $catalogRepository CatalogRepository */
			$catalogRepository = $container->get('doctrine')->getRepository('NSCatalogBundle:Catalog');
			if ($catalogRepository->findOneByName($value)) {
				throw new \Exception("Catalog '{$value}' already exists");
			}
			return $value;
		});

		// catalog title
		$title = $dialog->ask($output, "<comment>Catalog title</comment> [{$name}]: ", $name);

		// form type
		$formTypeName = $dialog->askAndValidate($output, '<comment>Form type</comment>: ', function($value) use($container) {
			if (!$container->has($value)) {
				throw new \Exception("Form '{$value}' wasn't found in container");
			}
			$type = $container->get($value);
			if (!$type instanceof FormTypeInterface) {
				throw new \Exception("It seems like object '{$value}' is not a form");
			}
			return $value;
		});

		// confirm
		if ($dialog->askConfirmation($output, "Creating catalog '{$name}' with title '{$title}' and form '{$formTypeName}'. Continue? [yes/no] ")) {
			// saving catalog
			$catalog = new Catalog();
			$catalog->setName($name);
			$catalog->setTitle($title);
			$catalog->setFormTypeName($formTypeName);

			/** @var $em EntityManager */
			$em = $container->get('doctrine')->getManager();
			$em->persist($catalog);
			$em->flush();

			$output->writeln("<info>Done!</info> Catalog <comment>#{$catalog->getId()}</comment> created successful!");
		}
		else {
			$output->writeln('<error>Canceled</error>');
		}



		die();



		/**
		 * @var Kernel $kernel
		 */
		$kernel = $this
			->getContainer()
			->get('kernel');

		// deploy name
		$name = time() . str_pad(rand(0, 9999), 4, '0', \STR_PAD_LEFT);

		// deploy temp path
		$tempPath = sys_get_temp_dir() . "/NS/Deploy/{$name}.tmp";
		$this->createDir($input, $output, $tempPath);
		$output->writeln("Temp folder\n  <comment>{$tempPath}</comment>\n");

		// deploy zip path
		$zipPath = $kernel->getRootDir() . '/deploy';
		$this->createDir($input, $output, $zipPath);
		$output->writeln("Zip folder\n  <comment>{$zipPath}</comment>\n");

		// copiing files
		$root = realpath($kernel->getRootDir() . '/..');
		$output->writeln("Copiing files\n  from <comment>{$root}</comment>\n  to   <comment>{$tempPath}</comment>...");
		`cp -r {$this->escapePath($root)}/* {$this->escapePath($tempPath)}`;
		$output->writeln("  <info>ok</info>\n");

		// removing .git's
		$output->write("Removing <comment>.git</comment> subfolders...");
		`rm -rf \`find {$this->escapePath($tempPath)} -type d -name .git\``;
		$output->writeln(" <info>ok</info>");

		// removing .DS_Store
		$output->write("Removing <comment>.DS_Store</comment> subfolders...");
		`rm -rf \`find {$this->escapePath($tempPath)} -type d -name .DS_Store\``;
		$output->writeln(" <info>ok</info>");

		// removing dirs
		$this
			->removeDir($input, $output, "{$tempPath}/.idea")
			->removeDir($input, $output, "{$tempPath}/app/deploy")
			->removeDir($input, $output, "{$tempPath}/app/cache")
			->removeDir($input, $output, "{$tempPath}/app/config/parameters.yml")
			->removeDir($input, $output, "{$tempPath}/public_html/uploads")
			->removeDir($input, $output, "{$tempPath}/public_html/media")
		;

		// vendors
		if ($this->skipVendorsDir($input)) {
			$this->removeDir($input, $output, "{$tempPath}/vendor");
		}

		// zipping
		$output->write("Zipping to <comment>{$zipPath}/{$name}.zip</comment>...");
		$cwd = getcwd();
		`cd {$this->escapePath($tempPath)}; zip -r {$name}.zip ./*; cd {$this->escapePath($cwd)};`;

		rename("{$tempPath}/{$name}.zip", "{$zipPath}/{$name}.zip");
		$output->writeln(" <info>ok</info>");

		// removing temp files
		$output->write("Removing temp dir <comment>{$tempPath}</comment>...");
		`rm -rf {$this->escapePath($tempPath)}`;
		$output->writeln(" <info>ok</info>");

		// done
		$output->writeln("");
		$output->writeln("<info>Done</info>");
	}

	/**
	 * Creates dir
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param string $dir
	 * @throws \Exception
	 */
	private function createDir(InputInterface $input, OutputInterface $output, $dir)
	{
		if (!is_dir($dir)) {
			$output->write("Creating folder <comment>{$dir}</comment>... ");
			if (!@mkdir($dir, 0777, true)) {
				throw new \Exception("Unable to create dir '{$dir}'");
			}
			$output->writeln("<info>ok</info>\n");
		}
	}

	/**
	 * Removes $dir subdir
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param string $dir
	 * @return BuildCommand
	 */
	private function removeDir(InputInterface $input, OutputInterface $output, $dir)
	{
		$output->write("Removing <comment>{$dir}</comment> subfolder...");
		`rm -rf {$this->escapePath($dir)}`;
		$output->writeln(" <info>ok</info>");

		return $this;
	}

	/**
	 * Checks to skip vendors
	 *
	 * @param InputInterface $input
	 * @return bool
	 */
	private function skipVendorsDir(InputInterface $input)
	{
		return !$input->getOption('vendors');
	}

	/**
	 * Escapes spaces in path to use as a shell command argument
	 *
	 * @param string $path
	 * @return string
	 */
	private function escapePath($path)
	{
		return str_replace(" ", "\\ ", $path);
	}
}
