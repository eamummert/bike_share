<?php

namespace Libs\Paginator\Table;

use RuntimeException;

class Column
{
	/**
	 * @var string name/identifier of column
	 */
	protected $name;

	/**
	 * @var string column label
	 */
	protected $label;

	/**
	 * @var string class to apply to cells
	 */
	protected $class;

	/**
	 * @var integer number of columns to span
	 */
	protected $colspan = 1;

	/**
	 * @var string left|right|center alignment
	 */
	protected $align;

	/**
	 * @var int order in which column appears in table
	 */
	protected $order;

	/**
	 * @var string initial asc|desc order direction on sort
	 */
	protected $sortOrder = 'asc';

	/**
	 * @var array sort queries to appy for column
	 */
	protected $sortQuery;

	public function __construct($config = [])
	{
		$this->setOptions($config);
	}

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

	public function getColspan()
	{
		return $this->colspan;
	}

	public function setColspan($span)
	{
		$this->colspan = $span;
		return $this;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function setClass($class)
	{
		$this->class = $class;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	public function getAlign()
	{
		return $this->align;
	}

	public function setAlign($align)
	{
		if (!in_array($align, ['left', 'center', 'right']))
		{
			throw new RuntimeException('Invalid alignment provided');
		}
		$this->align = $align;
		return $this;
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function setOrder($order)
	{
		$this->order = $order;
		return $this;
	}

	public function getSortOrder()
	{
		return $this->sortOrder;
	}

	/**
	 * Set the default sort order when this column is activated
	 *
	 * @param string $order asc|desc
	 * @return Column
	 * @throws RuntimeException
	 */
	public function setSortOrder($order)
	{
		$order = strtolower($order);
		if (!in_array($order, ['asc', 'desc']))
		{
			throw new RuntimeException('Invalid sort order provided');
		}
		$this->sortOrder = $order;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getSortQuery()
	{
		return $this->sortQuery;
	}

	/**
	 * Set which columns to use to sort query when this column is activated
	 *
	 * @param string|array $query
	 * @return Column
	 */
	public function setSortQuery($query)
	{
		$this->sortQuery = (array) $query;
		return $this;
	}
}

