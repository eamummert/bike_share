<?php

namespace Libs\Paginator\Adapter;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Allows pagination of Doctrine\ORM\Query objects.
 */
class Doctrine implements AdapterInterface
{
	/**
     * A query for the paginator
     *
	 * @var Query
	 */
    protected $query;

    /**
     * Count of items from query
     *
     * @var integer
     */
    protected $count;

    /**
     * Creates an adapter with a query.
     *
     * @param Query $query
     */
	public function __construct(Query $query)
	{
		$this->query = $query;
	}

	/**
     * Returns a collection of items for a page.
     *
     * @param integer $offset page offset
     * @param integer $itemCountPerPage number of items per page
     * @return Paginator
	 */
	public function getItems($offset, $itemCountPerPage)
	{
		$this->query->setMaxResults($itemCountPerPage)->setFirstResult($offset);
		return new Paginator($this->query);
	}

	/**
     * Counts the total number of items in the result
     *
	 * @return int
	 */
	public function count()
	{
		if ($this->count === null)
		{
			$this->query->setMaxResults(null)->setFirstResult(null);
			$paginator = new Paginator($this->query);
			$this->count = $paginator->count();
		}
		return $this->count;
	}
}

