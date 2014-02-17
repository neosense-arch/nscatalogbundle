<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use NS\CatalogBundle\QueryBuilder\ItemQueryBuilder;

/**
 * Class ItemRepository
 *
 * @package NS\CatalogBundle\Entity
 */
class ItemRepository extends EntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
	 * @param  int $id
	 * @return Item|null
	 */
	public function findOneById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}

	/**
	 * @param  string $slug
	 * @return Item|null
	 */
	public function findOneBySlug($slug)
	{
		return $this->findOneBy(array('slug' => $slug));
	}

	/**
	 * @param  int[] $ids
	 * @return Item[]
	 */
	public function findByIds(array $ids)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		$queryBuilder
			->where($queryBuilder->expr()->in('i.id', '?1'))
			->setParameter(1, $ids);

		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * @return Item[]
	 */
	public function findAll()
	{
		return $this
			->getFindByCategoryQuery()
			->execute();
	}

	/**
	 * @return Item[]
	 */
	public function findAllVisible()
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		return $queryBuilder
			->andWhere('i.visible = true')
			->getQuery()
			->getResult();
	}

	/**
	 * @param Category $category
	 * @return Item[]
	 */
	public function findByCategory(Category $category = null)
	{
		return $this
			->getFindByCategoryQuery($category)
			->execute();
	}

	/**
	 * @param Category $category
	 * @param string   $search
	 * @param Catalog  $catalog
	 * @return Query
	 */
	public function getFindByCategoryQuery(Category $category = null, $search = null, Catalog $catalog = null)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);

		if ($category) {
			$queryBuilder->andWhereCategory($category);
		}

		if ($search) {
			$queryBuilder->search($search);
		}

		if ($catalog) {
			$queryBuilder->andWhereCatalog($catalog);
		}

		return $queryBuilder->getQuery();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @param int    $skip
	 * @return Item[]
	 */
	public function findVisibleBySettings($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		$queryBuilder
			->leftJoin('i.rawSettings', 's')
			->andWhere('i.visible = true')
			->andWhere('s.name = :name')
			->setParameter('name', $key)
			->andWhere('s.value = :value')
			->setParameter('value', $value);

		if ($limit) {
			$queryBuilder
				->setMaxResults($limit)
				->setFirstResult($skip);
		}

		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return Query
	 */
	public function getFindBySettingsQuery($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = $this
			->getFindBySettingsQueryBuilder($key, $value, $limit, $skip);

		return $queryBuilder->getQuery();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return Query
	 */
	public function getFindVisibleBySettingsQuery($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = $this
			->getFindBySettingsQueryBuilder($key, $value, $limit, $skip)
			->andWhere('i.visible = true');

		return $queryBuilder->getQuery();
	}

	/**
	 * @param int $limit
	 * @param int $skip
	 * @return Query
	 */
	public function getFindFullCatalogQuery($limit = null, $skip = 0)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);

		$queryBuilder
			->join('i.category', 'c')
			->where('i.visible = true')
			->addOrderBy('c.title')
			->addOrderBy('i.title');

		if ($limit) {
			$queryBuilder
				->setMaxResults($limit)
				->setFirstResult($skip);
		}

		return $queryBuilder->getQuery();
	}

    /**
     * Retrieves items with pagination
     *
     * @param int      $page     page number
     * @param int      $limit    items per page
     * @param null     $visible  visible flag (item.visible)
     * @param Category $category item category (item.category)
     * @param array    $settings settings conditions (e.g. ['hit' => '1', 'price' => 10.2] or ['color' => ['red', 'yellow'], 'price' => 10.2])
     * @param array    $orderBy  order conditions (e.g. [['price', 'ASC', 'number'], ['createdAt', 'DESC']]
     * @return PaginationInterface|Item[]
     */
    public function findItemsPaged($page = 1, $limit = 20, $visible = null, Category $category = null,
                                   array $settings = array(), array $orderBy = array())
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        // building query
        $queryBuilder
            ->select('i', 'c', 's')
            ->from('NSCatalogBundle:Item', 'i')
            ->join('i.category', 'c')
            ->join('i.rawSettings', 's')
        ;

        // visibility condition
        if (!is_null($visible)) {
            $queryBuilder
                ->andWhere('i.visible = :visible')
                ->setParameter('visible', (bool)$visible);
        }

        // category condition
        if ($category) {
            $queryBuilder
                ->andWhere('c = :category')
                ->setParameter('category', $category);
        }

        // settings conditions
        foreach ($settings as $key => $value) {
            if (!is_array($value)) {
                $value = array($value);
            }
            if ($value) {
                $queryBuilder
                    ->join('i.rawSettings', "s{$key}", 'WITH', "s{$key}.name = :name_{$key} AND s{$key}.value IN (:value_{$key})")
                    ->setParameter("name_{$key}", $key)
                    ->setParameter("value_{$key}", $value);
            }
        }

        // ordering
        foreach ($orderBy as $order) {
            // settings default direction and order type
            if (empty($order[1])) {
                $order[1] = 'asc';
            }
            if (empty($order[2])) {
                $order[2] = 'string';
            }

            // order key (e.g. "price", "createdAt")
            $orderKey = $order[0];
            // order direction ("ASC" or "DESC")
            $orderDirection = $order[1];
            // order type ("string" or "numeric")
            $orderType = $order[2];

            // order key (e.g. "o_price")
            $name = 'o_' . $orderKey;
            // order expression (e.g. "o_price.value")
            $expr = $name . '.value';

            // processing order type
            if ($orderType === 'numeric') {
                // adding number operation in case of numeric type
                // e.g. "ORDER BY o_price.value + 0"
                $expr .= ' + 0';
            }

            // adding order expression
            $queryBuilder
                ->join('i.rawSettings', $name, 'WITH', "{$name}.name = :name_{$name}")
                ->setParameter("name_{$name}", $orderKey)
                ->addOrderBy($expr, $orderDirection);
        }

        $query = $queryBuilder->getQuery();

        return $this->paginator->paginate($query, $page, $limit, array('distinct' => true));
    }

    /**
     * @param PaginatorInterface $paginator
     */
    public function setPaginator(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return QueryBuilder
	 */
	private function getFindBySettingsQueryBuilder($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		$queryBuilder->andWhereSetting($key, $value);

		if ($limit) {
			$queryBuilder->limit($limit, $skip);
		}

		return $queryBuilder;
	}
}