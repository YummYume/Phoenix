<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\Role;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'view.user.index')
            ->setPageTitle('new', 'view.user.create')
            ->setPageTitle('edit', 'view.user.edit')
            ->setPageTitle('detail', 'view.user.detail')
            ->setEntityLabelInSingular('view.user.single')
            ->setEntityLabelInPlural('view.user.plural')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'user.email'),
            ChoiceField::new('roles', 'user.roles')
                ->setChoices(Role::toArray())
                ->allowMultipleChoices(),
            TextField::new('fullName', 'user.full_name')
                ->onlyOnIndex(),
            TextField::new('firstName', 'user.first_name')
                ->onlyOnForms(),
            TextField::new('lastName', 'user.last_name')
                ->onlyOnForms(),
            TextField::new('plainPassword', 'user.password')
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'invalid_message' => 'user.password.mismatch',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options' => [
                        'label' => 'user.password',
                        'row_attr' => ['class' => 'password-field col-md-6'],
                    ],
                    'second_options' => [
                        'label' => 'user.password_repeat',
                        'row_attr' => ['class' => 'password-field col-md-6'],
                    ],
                ])
                ->onlyWhenCreating(),
            TextField::new('plainPassword', 'user.password')
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'invalid_message' => 'user.password.mismatch',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => false,
                    'first_options' => [
                        'label' => 'user.password',
                        'row_attr' => ['class' => 'password-field col-md-6'],
                    ],
                    'second_options' => [
                        'label' => 'user.password_repeat',
                        'row_attr' => ['class' => 'password-field col-md-6'],
                    ],
                ])
                ->onlyWhenUpdating(),
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
