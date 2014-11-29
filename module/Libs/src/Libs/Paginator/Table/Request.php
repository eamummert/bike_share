<?php

namespace Libs\Paginator\Table;

use RuntimeException;

class Request
{
	/**
	 * @var Table
	 */
	protected $table;

	/**
	 * @var array $_GET request
	 */
	protected $params;

	public function __construct(Table $table)
	{
		$this->table = $table;
		$this->params = $_GET;
		$this->setParam('sort-column', $this->getParam('sort-column') ?: $table->getDefaultSortColumn());
		$this->setParam('sort-order', strtolower($this->getParam('sort-order')) ?: null);
		if (!$this->getParam('sort-order'))
		{
			if ($this->getParam('sort-column'))
			{
				$column = $this->table->getColumn($this->getParam('sort-column'));
				if ($column)
				{
					$this->setParam('sort-order', $column->getSortOrder());
				}
			}
		}
		elseif (!in_array($this->getParam('sort-order'), ['asc', 'desc']))
		{
			throw new RuntimeException('Invalid sort order requested for paginator table');
		}
		$this->setParam('page', $this->getParam('page') ?: 1);
	}

	/**
	 * Return the request param value for this table
	 *
	 * @param string $name sort-column|sort-order|page
	 * @return string|null
	 */
	public function getParam($name)
	{
		$key = $this->prefixParamKey($name);
		return isset($this->params[$key]) ? $this->params[$key] : null;
	}

	/**
	 * Set the request parameter for this table
	 *
	 * @param string $name
	 * @param string $value
	 * @return Request
	 */
	public function setParam($name, $value)
	{
		$key = $this->prefixParamKey($name);
		$this->params[$key] = $value;
		return $this;
	}

	protected function prefixParamKey($name)
	{
		$prefix = $this->table->getPrefix() ? $this->table->getPrefix() . '-' : '';
		switch ($name)
		{
			case 'page':
				return $prefix .'p';
				break;
			case 'sort-column':
				return $prefix .'sc';
				break;
			case 'sort-order':
				return $prefix .'so';
				break;
			default:
				throw new RuntimeException("Param '{$name}' not supported");
		}
	}

	/**
	 * Returns the parmaters in url-encoded query string format, when cast to a string
	 *
	 * @return string
	 */
	public function __toString()
	{
		$query = http_build_query($this->params);
		if ($frag = $this->table->getFragment())
		{
			$query .= '#'. $frag;
		}
		return $query;
	}
}

