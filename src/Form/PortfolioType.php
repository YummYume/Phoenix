<?php

namespace App\Form;

use App\Entity\Portfolio;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

final class PortfolioType extends AbstractType
{
    public function __construct(private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('name', TextType::class, [
                'label' => 'portfolio.name',
                'required' => true,
            ])
            ->add('projects', EntityType::class, [
                'label' => 'portfolio.projects',
                'class' => Project::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'query_builder' => static function (ProjectRepository $projectRepository) use ($user): QueryBuilder {
                    return $projectRepository->getProjectsByUser($user);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Portfolio::class,
        ]);
    }
}
