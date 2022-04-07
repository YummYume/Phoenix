<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $team = $builder->getData();

        $builder
            ->add('name', TextType::class, [
                'label' => 'team.name',
                'required' => true,
            ])
            ->add('members', EntityType::class, [
                'label' => 'team.members',
                'class' => User::class,
                'choice_label' => 'fullName',
                'multiple' => true,
                'required' => false,
                'query_builder' => static fn (UserRepository $userRepository): QueryBuilder => $userRepository->findAll(),
            ])
            ->add('parentTeam', EntityType::class, [
                'label' => 'team.parent_team',
                'class' => Team::class,
                'choice_label' => 'name',
                'required' => false,
                'query_builder' => static function (TeamRepository $teamRepository) use ($team): QueryBuilder {
                    $qb = $teamRepository->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC')
                    ;

                    if ($team->getId()) {
                        $qb
                            ->andWhere('t != :team')
                            ->setParameter('team', $team)
                        ;
                    }

                    return $qb;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
