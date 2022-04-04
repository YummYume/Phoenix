<?php

namespace App\Form;

use App\Entity\Milestone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MilestoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'milestone.name',
                'required' => true,
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'milestone.start_at',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'milestone.end_at',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'milestone.required',
                'required' => false,
            ])
            ->add('completed', CheckboxType::class, [
                'label' => 'milestone.completed',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Milestone::class,
        ]);
    }
}
