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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class CatalogCommand
 * @package NS\CatalogBundle\Command
 */
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
			$catalog->setSettingsFormTypeName($formTypeName);

			/** @var $em EntityManager */
			$em = $container->get('doctrine')->getManager();
			$em->persist($catalog);
			$em->flush();

			$output->writeln("<info>Done!</info> Catalog <comment>#{$catalog->getId()}</comment> created successful!");
		}
		else {
			$output->writeln('<error>Canceled</error>');
		}
	}
}
