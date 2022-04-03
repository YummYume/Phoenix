<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Milestone;
use App\Repository\MilestoneRepository;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EventType extends AbstractType
{
    public function __construct(private MilestoneRepository $milestoneRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $builder->getData()->getProject();

        $builder
            ->add('name', TextType::class, [
                'label' => 'event.name',
                'required' => true,
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'event.date',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'event.description',
                'required' => false,
            ])
            ->add('milestone', EntityType::class, [
                'label' => 'event.milestone',
                'class' => Milestone::class,
                'choice_label' => 'name',
                'required' => false,
                'query_builder' => function (SortableRepository $sortableRepository) use ($project): QueryBuilder {
                    return $this->milestoneRepository->getMilestonesByProject($project);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
