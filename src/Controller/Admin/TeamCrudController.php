<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'view.team.index')
            ->setPageTitle('new', 'view.team.create')
            ->setPageTitle('edit', 'view.team.edit')
            ->setPageTitle('detail', 'view.team.detail')
            ->setEntityLabelInSingular('view.team.single')
            ->setEntityLabelInPlural('view.team.plural')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'team.name'),
            AssociationField::new('responsible', 'team.responsible')
                ->formatValue(static fn (string $value, Team $entity): string => $entity->getResponsible()->getEmail()),
            AssociationField::new('members', 'team.members')
                ->formatValue(static fn (string $value, Team $entity): string => \count($entity->getMembers()))
                ->onlyOnIndex(),
            AssociationField::new('members', 'team.members')
                ->onlyOnForms(),
            AssociationField::new('parentTeam', 'team.parentTeam'),
            DateTimeField::new('createdAt', 'common.created_at')
                ->onlyOnIndex(),
            DateTimeField::new('updatedAt', 'common.updated_at')
                ->onlyOnIndex(),
            TextField::new('createdBy', 'common.created_by')
                ->formatValue(static fn (?User $user): string|null => $user)
                ->onlyOnIndex(),
            TextField::new('updatedBy', 'common.updated_by')
                ->formatValue(static fn (?User $user): string|null => $user)
                ->onlyOnIndex(),
        ];
    }
}
