<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\StudentGroup;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class StudentGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('student', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Select Student',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('JSON_CONTAINS(u.roles, :role) = 1')
                        ->setParameter('role', '"ROLE_STUDENT"')
                        ->orderBy('u.email', 'ASC');
                },
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => fn (Group $g) => $g->getName() . ' (' . $g->getYear() . ')',
                'label' => 'Assign to Group',
            ])
            ->add('approved', CheckboxType::class, [
                'label' => 'Approved',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StudentGroup::class,
        ]);
    }
}
