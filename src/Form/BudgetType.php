<?php

namespace App\Form;

use App\Entity\Budget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BudgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('initialAmount', MoneyType::class, [
                'label' => 'budget.initial_amount',
                'required' => true,
                'currency' => 'EUR',
                'html5' => true,
            ])
            ->add('spentAmount', MoneyType::class, [
                'label' => 'budget.spent_amount',
                'required' => true,
                'currency' => 'EUR',
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Budget::class,
        ]);
    }
}
