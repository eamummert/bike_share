<?php

namespace Libs\Paginator;

use Countable;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Traversable;
use Zend\Paginator\Paginator as ZendPaginator;

class Paginator extends ZendPaginator
{
	public function getItemCount($items)
	{
		$itemCount = 0;

		if (is_array($items) || ($items instanceof Countable && !$items instanceof DoctrinePaginator))
		{
			$itemCount = count($items);
		}
		elseif ($items instanceof Traversable)
		{
			$itemCount = iterator_count($items);
		}

		return $itemCount;
	}
}

