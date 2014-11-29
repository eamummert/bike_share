<?php

namespace Libs\View\Helper;

use Libs\Paginator\Table;
use Zend\View\Helper\AbstractHelper as ZendAbstractHelper;

/**
 * Render a paginator table.
 */
class PaginatorTable extends ZendAbstractHelper
{
	protected $table;

	/**
	 * Renders a paginator table
	 *
	 * @param Table\Table $table paginator table
	 * @param string $template phtml file used to render each row
	 * @param array $options
	 *     - class: classname to apply to table
	 *     - emptyMessage: message to show when no rows are found
	 *     - variables: variables to populate the view when drawing each column
	 *
	 * @return string the rendered table
	 * @throws Exception\RuntimeException
	 */
	public function __invoke(Table\Table $table = null, $template = null, array $options = [])
	{
		if (func_num_args() == 0)
		{
			return $this;
		}
		elseif (!is_string($template))
		{
			throw new Exception\RuntimeException(sprintf(
				'Expected paginator table template to be a string, got %s instead'
				, gettype($template)
			));
		}
		$html = [];
		$html[] = '<div class="isu-pt Paginator overflow-scroll u-OverflowScroll">';
		$html[] = sprintf(
			'<table class="isu-pt-table Paginator-table %s" %s>',
			$options['class'],
			$table->getFragment() ? 'id="'. $table->getFragment() .'"' : ''
		);
		$html[] = '<thead>';
		$html[] = $this->renderHeader($table);
		$html[] = '</thead>';
		$html[] = '<tbody>';
		$html[] = $this->renderBody($table, $template, $options);
		$html[] = '</tbody>';
		$html[] = '</table>';
		$html[] = $this->renderPagination($table);
		$html[] = '</div>';
		return implode("\n", $html);
	}

	public function renderHeader(Table\Table $table)
	{
		$baseUrl = $this->view->url(null, [], true);
		$request = $table->getRequest();
		$reverseOrder = [
			'asc' => 'desc',
			'desc' => 'asc',
		];

		$html = [];
		$html[] = '<tr>';
		foreach ($table->getColumns() as $column)
		{
			$cellClass = trim($column->getClass() .' '. $column->getAlign());
			$html[] = '<th class="'. $cellClass .'" colspan="'. $column->getColspan() .'">';
			$header = $column->getLabel();
			if ($column->getSortQuery())
			{
				$class = 'sc-link';
				$sortRequest = clone $request;
				$sortRequest->setParam('sort-column', $column->getName());
				$sortRequest->setParam('page', null);
				if ($request->getParam('sort-column') == $column->getName())
				{
					// This column is being currently sorted
					$class .= ' active so-'. $request->getParam('sort-order');
					// Click to sort in reverse order
					$sortRequest->setParam('sort-order', $reverseOrder[$request->getParam('sort-order')]);
				}
				else
				{
					// Click to sort by this column
					$order = $column->getSortOrder();
					$sortRequest->setParam('sort-order', $order);
					$class .= ' so-'. $order;
				}
				$url = $baseUrl .'?'. $sortRequest;
				$header = '<a class="'. $class .'" href="'. $url .'">'. $header .'<span class="so-icon"></span></a>';
			}
			$html[] = $header;
			$html[] = '</th>';
		}
		$html[] = '</tr>';
		return implode("\n", $html);
	}

	public function renderBody(Table\Table $table, $render, array $options = [])
	{
		$paginator = $table->getIterator();
		$columns = $table->getColumns();
		$html = [];
		if ($paginator->count() == 0)
		{
			$html[] = '<tr><td colspan="'. $table->getTotalColspan() .'">';
			$html[] = isset($options['emptyMessage']) ? $options['emptyMessage'] : 'No entries found';
			$html[] = '</td></tr>';
		}
		foreach ($paginator as $item)
		{
			$html[] = '<tr>';
			foreach ($columns as $column)
			{
				$cellClass = trim($column->getClass() .' '. $column->getAlign());
				for ($i = 0, $l = $column->getColspan(); $i < $l; $i++)
				{
					$html[] = '<td class="'. $cellClass .'">';
					$html[] = $this->view->render($render, array_merge(
						isset($options['variables'])
							? $options['variables']
							: (isset($options['vars']) ? $options['vars'] : []),
						[
							'column' => $column->getName(),
							'span' => $i,
							'item' => $item,
						]
					));
					$html[] = '</td>';
				}
			}
			$html[] = '</tr>';
		}
		return implode("\n", $html);
	}

	public function renderPagination(Table\Table $table)
	{
		$paginator = $table->getIterator();
		$request = $table->getRequest();
		if (count($paginator) <= 1)
		{
			return '';
		}
		$html = [];
		$pageRange = 6;
		$pages = $paginator->setPageRange($pageRange)->getPages();
		$html[] = '<ul class="isu-pt-pagination Paginator-pagination Pagination">';
		if ($pages->firstPageInRange > $pages->first)
		{
			$html[] = $this->renderPaginationLink($request, $pages->first, 'First');
		}
		if (isset($pages->previous))
		{
			$html[] = $this->renderPaginationLink($request, $pages->previous, 'Prev');
		}
		foreach ($pages->pagesInRange as $p)
		{
			$html[] = $this->renderPaginationLink($request, $p, $p);
		}
		if (isset($pages->next))
		{
			$html[] = $this->renderPaginationLink($request, $pages->next, 'Next');
		}
		if ($pages->lastPageInRange < $pages->last)
		{
			$html[] = $this->renderPaginationLink($request, $pages->last, 'Last');
		}
		$html[] = '</ul>';
		$html[] = sprintf(
			'<span class="Button-text">
				<span class="first-item">%s</span> - <span class="last-item">%s</span> of <span class="total">%s</span>
			</span>',
			$pages->firstItemNumber,
			$pages->lastItemNumber,
			$pages->totalItemCount
		);
		return implode("\n", $html);
	}

	protected function renderPaginationLink(Table\Request $request, $page, $label)
	{
		$baseUrl = $this->view->url(null, [], true);
		$isActive = $request->getParam('page') == $page;
		$pageRequest = clone $request;
		$pageRequest->setParam('page', $page);
		$url = $baseUrl .'?'. $pageRequest;
		return '<a class="'. ($isActive ? 'active' : '') .'" href="'. $url .'">'. $label .'</a>';
	}
}

