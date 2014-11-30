<?php

namespace App\Controller;

use App\Entity;
use Libs\Controller\AbstractController;
use Libs\Paginator\Table\Table;

class Dock extends AbstractController
{
    public function indexAction()
    {
    	$query = $this->entity('Dock')->createQueryBuilder('d');
    	$docks = new Table([
    		'columns' => [
    			'name' => [
    				'label' => 'Name',
    				'sortQuery' => 'd.name',
    			],
    			'bicycle' => [
    				'label' => 'Bicycle',
    			],
    		],
    		'query' => $query,
    	]);
        return compact('docks');
    }
}
