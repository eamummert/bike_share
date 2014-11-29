<?php

namespace Libs\Paginator\Table;

use Countable;
use IteratorAggregate;
use RuntimeException;
use Doctrine\ORM\QueryBuilder;
use Libs\Paginator\Adapter;
use Libs\Paginator\Paginator;

class Table implements Countable, IteratorAggregate
{
	/**
	 * @var string optional prefix for all parameter names
	 */
	protected $prefix;

	/**
	 * @var Column[] table columns
	 */
	protected $columns = [];

	/**
	 * @var string default sort column name (optional)
	 */
	protected $defaultSortColumn;

	/**
	 * @var int number of rows per page
	 */
	protected $itemsPerPage = 10;

	/**
	 * @var QueryBuilder query used to populate table
	 */
	protected $query;

	/**
	 * @var Paginator result iterator
	 */
	protected $paginator;

	/**
	 * @var Request prefix agnostic helper object to figure out request parameters for table
	 */
	protected $request;

	/**
	 * @var string Fragment identifier to append to URL
	 */
	protected $fragment;

	public function __construct($config = [])
	{
		$this->setOptions($config);
	}

	/**
	 * Set options for this table
	 *
	 * @param array $config
	 * @return Table
	 * @throws RuntimeException
	 */
	public function setOptions($config = [])
	{
		foreach ($config as $key => $val)
		{
			$method = 'set'. ucfirst($key);
			if (strtolower($key) == 'options')
			{
				continue;
			}
			if (!method_exists($this, $method))
			{
				throw new RuntimeException("Option '{$key}' not supported");
			}
			$this->{$method}($val);
		}
		return $this;
	}

	public function count()
	{
		return count($this->getIterator());
	}

	/**
	 * Loop over results with a paginator with proper order, limit and offset clause
	 *
	 * @return Paginator
	 */
	public function getIterator()
	{
		if ($this->paginator === null)
		{
			if ($this->query === null)
			{
				throw new RuntimeException('No query set for paginator table');
			}
			$query = clone $this->query;
			$request = $this->getRequest();
			if ($request->getParam('sort-column'))
			{
				$column = $this->getColumn($request->getParam('sort-column'));
				if (!$column || !$column->getSortQuery())
				{
					throw new RuntimeException('Invalid sort column selected for paginator table');
				}
				foreach ($column->getSortQuery() as $sortQuery)
				{
					$query->addOrderBy($sortQuery, $request->getParam('sort-order'));
				}
			}
			$adapter = new Adapter\Doctrine($query->getQuery());
			$this->paginator = new Paginator($adapter);
			$this->paginator
				->setItemCountPerPage($this->getItemsPerPage())
				->setCurrentPageNumber($request->getParam('page'))
			;
		}
		return $this->paginator;
	}

	/**
	 * Sets up and returns the request object with params for this table
	 *
	 * @return Request
	 */
	public function getRequest()
	{
		if ($this->request === null)
		{
			$this->request = new Request($this);
		}
		return $this->request;
	}

	public function getDefaultSortColumn()
	{
		return $this->defaultSortColumn;
	}

	public function setDefaultSortColumn($name)
	{
		$this->defaultSortColumn = $name;
		return $this;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
		return $this;
	}

	public function getFragment()
	{
		return $this->fragment;
	}

	public function setFragment($fragment)
	{
		$this->fragment = $fragment;
		return $this;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function setQuery(QueryBuilder $query)
	{
		$this->query = $query;
		return $this;
	}

	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}

	public function setItemsPerPage($perPage)
	{
		$this->itemsPerPage = $perPage;
		return $this;
	}

	public function getTotalColspan()
	{
		$span = 0;
		foreach ($this->columns as $column)
		{
			$span += $column->getColspan();
		}
		return $span;
	}

	/**
	 * @param string $name
	 * @return Column
	 */
	public function getColumn($name)
	{
		return $this->columns[$name];
	}

	/**
	 * Get table columns sorted by their order property
	 *
	 * @return Column[]
	 */
	public function getColumns()
	{
		$columns = [];
		$index = [];
		$i = 0;
		foreach ($this->columns as $name => $col)
		{
			$order = $col->getOrder();
			$index[$name] = $order !== null ? $order : $i++;
		}
		asort($index);
		foreach (array_keys($index) as $name)
		{
			$columns[$name] = $this->columns[$name];
		}
		return $columns;
	}

	public function setColumns($spec)
	{
		$this->clearColumns();
		foreach ($spec as $key => $val)
		{
			$this->addColumn($key, $val);
		}
		return $this;
	}

	/**
	 * Add a new column to the table
	 *
	 * @param string|Column $nameOrColumn
	 * @param array $options (optional)
	 * @return Table
	 */
	public function addColumn($nameOrColumn, $options = [])
	{
		if ($nameOrColumn instanceof Column)
		{
			$column = $nameOrColumn;
			$this->columns[$column->getName()] = $column;
		}
		else
		{
			$options['name'] = $nameOrColumn;
			$this->columns[$nameOrColumn] = new Column($options);
		}
		return $this;
	}

	/**
	 * Add a new column to the table
	 *
	 * @param string|Column $nameOrColumn
	 * @param array $options
	 *
	 * @deprecated since v2.2.12
	 * @return Table
	 */
	public function setColumn($nameOrColumn, $options = [])
	{
		trigger_error('setColumn() deprecated as of v2.2.12 use the addColumn() method instead', E_USER_DEPRECATED);
		return $this->addColumn($nameOrColumn, $options);
	}

	/**
	 * Remove a column by its name
	 *
	 * @param string $name
	 * @return Table
	 */
	public function removeColumn($name)
	{
		unset($this->columns[$name]);
		return $this;
	}

	public function clearColumns()
	{
		$this->columns = [];
		return $this;
	}
}

