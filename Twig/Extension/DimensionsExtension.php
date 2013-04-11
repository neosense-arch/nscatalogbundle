<?php
namespace NS\CatalogBundle\Twig\Extension;

/**
 * Class DimensionsExtension
 *
 * @package NS\CatalogBundle\Twig\Extension
 */
class DimensionsExtension extends \Twig_Extension
{
	/**
	 * @return array
	 */
	public function getFilters()
	{
		return array(
			'dimensions' => new \Twig_Filter_Method($this, 'dimensions'),
		);
	}

	/**
	 * @param string $dimensions
	 * @param string $metric
	 * @return string
	 */
	public function dimensions($dimensions, $metric = null)
    {
		$dimensions = str_replace(array(
			'*', 'х',
		), 'x', $dimensions);

		$dimensions = str_replace(array(
			'cm', 'см',
		), '', strtolower($dimensions));

		$dimensions = trim($dimensions, 'x');

		if ($metric) {
			$dimensions = str_replace('x', $metric . ' x ', $dimensions);
		}

		return $dimensions . $metric;
    }

	/**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
		return 'dimensions';
    }
}