<?php

namespace Libs\Entity\Manager;

use Doctrine\ORM\EntityManager;

interface EntityManagerAwareInterface
{
	public function getEntityManager();

	public function setEntityManager(EntityManager $entities);
}


