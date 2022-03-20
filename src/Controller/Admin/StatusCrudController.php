<?php

namespace App\Controller\Admin;

use App\Entity\Status;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class StatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Status::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'status.title'),
            NumberField::new('position', 'status.position'),
            ColorField::new('color', 'status.color'),
            DateTimeField::new('createdAt', 'common.created_at')->onlyOnIndex(),
            DateTimeField::new('updatedAt', 'common.updated_at')->onlyOnIndex(),
        ];
    }
}
