<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

final class ProjectType extends AbstractType
{
    public function __construct(private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $project = $builder->getData();

        $builder
            ->add('name', TextType::class, [
                'label' => 'project.name',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'project.description',
                'required' => false,
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'project.start_at',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'project.end_at',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('archived', CheckboxType::class, [
                'label' => 'project.archived',
                'required' => false,
            ])
            ->add('private', CheckboxType::class, [
                'label' => 'project.private',
                'required' => false,
            ])
            ->add('status', EntityType::class, [
                'label' => 'project.status',
                'class' => Status::class,
                'choice_label' => 'title',
                'choice_attr' => function (?Status $status): array {
                    return $status ? ['style' => 'color: '.$status->getColor().';'] : [];
                },
            ])
            ->add('team', EntityType::class, [
                'label' => 'project.team',
                'class' => Team::class,
                'choice_label' => 'name',
                'required' => true,
                'query_builder' => static function (TeamRepository $teamRepository) use ($user): QueryBuilder {
                    return $teamRepository->findAllByUser($user, true);
                },
            ])
            ->add('clientTeam', EntityType::class, [
                'label' => 'project.client_team',
                'class' => Team::class,
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('budget', BudgetType::class, [
                'label' => 'project.budget',
                'required' => true,
            ])
        ;

        if ($project->getId()) {
            $builder->add('code', TextType::class, [
                'label' => 'project.code',
                'required' => false,
                'disabled' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
