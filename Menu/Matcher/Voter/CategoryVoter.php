<?php

namespace NS\CatalogBundle\Menu\Matcher\Voter;

use Knp\Menu\Matcher\Voter\VoterInterface;
use Knp\Menu\ItemInterface;
use NS\CatalogBundle\Entity\Category;
use NS\CmsBundle\Entity\Page;

/**
 * Category voter
 *
 */
class CategoryVoter implements VoterInterface
{
	/**
	 * @var Category
	 */
	private $category;

	/**
	 * @param Category $category
	 */
	public function __construct(Category $category)
	{
		$this->category = $category;
	}

	/**
	 * Checks whether an item is current.
	 *
	 * If the voter is not able to determine a result,
	 * it should return null to let other voters do the job.
	 *
	 * @param ItemInterface $item
	 *
	 * @return boolean|null
	 */
	public function matchItem(ItemInterface $item)
	{
		return $this->category->getId() == $item->getName();
	}

}
