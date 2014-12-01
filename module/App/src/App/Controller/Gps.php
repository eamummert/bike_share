<?php

namespace App\Controller;

use App\Entity;
use DateTime;
use Libs\Controller\AbstractController;
use Libs\Paginator\Table\Table;

class Gps extends AbstractController
{
    public function checkoutAction()
    {
        $co = $this->entity('CheckOut')->find($this->params('checkout-id'));
        if (!$co)
        {
            $this->flash()->addErrorMessage('There is no check out with that ID');
            return $this->redirect()->toRoute('app/bicycles');
        }

        return compact('co');
    }

    public function bikeAction()
    {
        $bike = $this->entity('Bicycle')->find($this->params('bike-id'));
        if (!$bike)
        {
            $this->flash()->addErrorMessage('There is no bicycle with that ID');
            return $this->redirect()->toRoute('app/bicycles');
        }

        return compact('bike');
    }
}