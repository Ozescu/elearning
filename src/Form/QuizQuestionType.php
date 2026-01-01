<?php

namespace App\Form;

use App\Entity\QuizQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, [
                'label' => 'Question Text',
                'attr' => ['rows' => 3, 'class' => 'form-control'],
            ])
            ->add('questionType', ChoiceType::class, [
                'label' => 'Question Type',
                'choices' => [
                    'Multiple Choice' => 'multiple_choice',
                    'True/False' => 'true_false',
                    'Short Answer' => 'short_answer',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('correctAnswer', TextType::class, [
                'label' => 'Correct Answer',
                'attr' => ['class' => 'form-control', 'placeholder' => 'For MC, enter option letter (A/B/C/D). For T/F: true or false'],
            ]);

        // Dynamically add options field for multiple choice questions
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data && $data->getQuestionType() === 'multiple_choice') {
                $form->add('options', TextareaType::class, [
                    'label' => 'Options (one per line, format: A: Option 1)',
                    'attr' => ['rows' => 4, 'class' => 'form-control', 'placeholder' => 'A: First option' . PHP_EOL . 'B: Second option'],
                    'required' => false,
                    'mapped' => false,
                    'data' => isset($data) && $data->getOptions() ? implode(PHP_EOL, $data->getOptions()) : '',
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($form->has('options')) {
                $optionsText = $form->get('options')->getData();
                if ($optionsText) {
                    $options = array_filter(array_map('trim', explode(PHP_EOL, $optionsText)));
                    $data->setOptions($options);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuizQuestion::class,
        ]);
    }
}
