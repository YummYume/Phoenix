<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TeamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Team::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            AssociationField::new('responsible')->formatValue(static fn (string $value, Team $entity): string => $entity->getResponsible()->getEmail()),
            AssociationField::new('members')->formatValue(static fn (string $value, Team $entity): string => \count($entity->getMembers())),
            DateTimeField::new('createdAt', 'common.created_at'),
            DateTimeField::new('updatedAt', 'common.updated_at'),
        ];
    }
}
