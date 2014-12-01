<?php

namespace App\Controller;

use App\Entity;
use DateTime;
use Libs\Controller\AbstractController;
use Libs\Paginator\Table\Table;

class Bicycle extends AbstractController
{
    public function indexAction()
    {
    	$query = $this->entity('Bicycle')->createQueryBuilder('b')
            ->leftJoin('b.dock', 'd');

    	$bikes = new Table([
    		'columns' => [
    			'id' => [
    				'label' => 'ID',
    				'sortQuery' => 'b.id',
    			],
    			'dock' => [
    				'label' => 'Dock',
                    'sortQuery' => 'd.name',
    			],
                'data' => [
                    'label' => 'Data',
                ],
                'actions' => [
                    'label' => 'Actions',
                ],
    		],
    		'query' => $query,
            'defaultSortColumn' => 'id',
    	]);
        return compact('bikes');
    }

    public function checkoutAction()
    {
        $bike = $this->entity('Bicycle')->find($this->params('bike-id'));
        if (!$bike)
        {
            $this->flash()->addErrorMessage('There is no bicycle with that ID');
            return $this->redirect()->toRoute('app/bicycles');
        }
        if (!$bike->getDock())
        {
            $this->flash()->addErrorMessage('The specified bicycle is already checked out');
            return $this->redirect()->toRoute('app/bicycles');
        }

        $checkOut = new Entity\CheckOut;
        $student = $this->entity('Student')->findOneBy(['username' => $this->auth()->getIdentity()->getUsername()]);
        if (!$student)
        {
            $student = new Entity\Student;
            $student->setUsername($this->auth()->getIdentity()->getUsername());
        }
        $student->addCheckout($checkOut);


        $bike->addCheckOut($checkOut);
        $bike->unDock($checkOut);
        $this->createGpsData($checkOut, rand(1, 4));

        $this->entity()->persist($student);
        $this->entity()->persist($checkOut);
        $this->entity()->flush();

        $this->flash()->addSuccessMessage('Bicycle has been checked out successfully');
        return $this->redirect()->toRoute('app/bicycles');
    }

    public function checkinAction()
    {
        $bike = $this->entity('Bicycle')->find($this->params('bike-id'));
        if (!$bike)
        {
            $this->flash()->addErrorMessage('There is no bicycle with that ID');
            return $this->redirect()->toRoute('app/bicycles');
        }
        if ($bike->getDock())
        {
            $this->flash()->addErrorMessage('The specified bicycle is already at a dock');
            return $this->redirect()->toRoute('app/bicycles');
        }

        //just check it in to a random available dock for the demo
        $docks = $this->entity('Dock')->findBy(['bicycle' => null]);
        $rand = rand(0, count($docks)-1);
        $i = 0;
        foreach ($docks as $dock) 
        {
            if ($i == $rand) break;
            $i++;
        }

       $bike->setDock($dock);

        $checkout = $bike->getCurrentCheckout();
        //this is for new bikes being docked initially but weren't checked out
        if ($checkout)
        {
            $checkout->setInTime(new DateTime);
            $dock->addCheckIn($checkout);

            $fee = $checkout->assignFees();
            if ($fee)
            {
                $checkout->getStudent()->addFee($fee);
            }
        }
        $this->entity()->flush();

        $this->flash()->addSuccessMessage('Bicycle has been checked in successfully');
        return $this->redirect()->toRoute('app/bicycles');
    }

    public function historyAction()
    {
        $bike = $this->entity('Bicycle')->find($this->params('bike-id'));
        if (!$bike)
        {
            $this->flash()->addErrorMessage('There is no bicycle with that ID');
            return $this->redirect()->toRoute('app/bicycles');
        }

        return compact('bike');
    }
    public function LockAction()
    {
        $bike = $this->entity('Bicycle')->find($this->params('bike-id'));
        if (!$bike)
        {
            $this->flash()->addErrorMessage('There is no bicycle with that ID');
        }
        else
	{
            $bike->switchLock();
            $this->entity()->flush();
        }
        return $this->redirect()->toRoute('app/bicycles');
    }

    public function LockallAction()
    {
        $bikes = $this->entity('Bicycle')->findAll();

	foreach($bikes as $bike)
	{
		$bike->lock();
	}
        $this->entity()->flush();
        return $this->redirect()->toRoute('app/bicycles');
    }
    
    public function UnlockallAction()
    {
        $bikes = $this->entity('Bicycle')->findAll();

	foreach($bikes as $bike)
	{
		$bike->unlock();
	}
        $this->entity()->flush();
        return $this->redirect()->toRoute('app/bicycles');
    }

    public function createGpsData($checkout, $count)
    {
        for ($i = 0; $i < $count; $i++)
        {
            $gps = new Entity\Gps;
            $gps->setRandLongitude(true);
            $gps->setRandLatitude(true);
            $checkout->addGps($gps);
            $checkout->getBicycle()->addGps($gps);
        }
    }
}
