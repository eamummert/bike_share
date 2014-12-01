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
    			'locked' => [
    				'label' => 'Locked',
    			],
    		],
    		'query' => $query,
    	]);
        return compact('docks');
    }


    public function LockallAction()
    {
        $docks = $this->entity('Dock')->findAll();

	foreach($docks as $dock)
	{
		$dock->lock();
	}
        $this->entity()->flush();
        return $this->redirect()->toRoute('app/docks');
    }
    public function UnlockallAction()
    {
        $docks = $this->entity('Dock')->findAll();
	foreach($docks as $dock)
	{
		$dock->unlock();
	}
        $this->entity()->flush();
        return $this->redirect()->toRoute('app/docks');
    }

}
