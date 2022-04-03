<?php

namespace App\Form;

use App\Entity\Risk;
use App\Enum\Probability;
use App\Enum\Severity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RiskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'risk.name',
                'required' => true,
            ])
            ->add('identifiedAt', DateTimeType::class, [
                'label' => 'risk.identified_at',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('resolvedAt', DateTimeType::class, [
                'label' => 'risk.resolved_at',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('severity', EnumType::class, [
                'class' => Severity::class,
                'label' => 'risk.severity',
                'required' => true,
                'choice_label' => fn (Severity $severity): string => 'severity.'.$severity->value,
            ])
            ->add('probability', EnumType::class, [
                'class' => Probability::class,
                'label' => 'risk.probability',
                'required' => true,
                'choice_label' => fn (Probability $probability): string => 'probability.'.$probability->value,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Risk::class,
        ]);
    }
}
