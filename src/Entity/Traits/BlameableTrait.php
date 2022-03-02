<?php

namespace App\Entity\Traits;

use App\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

trait BlameableTrait {
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\Column(nullable: true)]
    #[Gedmo\Blameable(on: 'create')]
    private $createdBy;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\Column(nullable: true)]
    #[Gedmo\Blameable(on: 'update')]
    private $updatedBy;
}
