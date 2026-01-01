<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Course Title',
                'attr' => ['placeholder' => 'e.g. Mathematics 101'],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Course description', 'rows' => 4],
            ])
            ->add('creditHours', null, [
                'label' => 'Credit Hours',
                'required' => false,
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => fn (Group $g) => $g->getName() . ' (' . $g->getYear() . ')',
                'label' => 'Assign to Group',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
