<?php

namespace App\Controller;

use App\Entity;
use Libs\Controller\AbstractController;
use Libs\Paginator\Table\Table;

class Fee extends AbstractController
{
    public function indexAction()
    {
    	$query = $this->entity('Fee')->createQueryBuilder('f')
            ->leftJoin('f.student', 's')
            ->leftJoin('f.checkout', 'c');

    	$fees = new Table([
    		'columns' => [
    			'student' => [
    				'label' => 'Student',
    				'sortQuery' => 's.username',
    			],
    			'charge' => [
    				'label' => 'Charge',
                    'sortQuery' => 'f.charge',
    			],
                'paid' => [
                    'label' => 'Paid',
                    'sortQuery' => 'f.paid',
                ],
                'date' => [
                    'label' => 'Date',
                    'sortQuery' => 'c.inTime',
                ],
    		],
    		'query' => $query,
    	]);
        return compact('fees');
    }
}
