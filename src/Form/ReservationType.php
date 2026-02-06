<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Reservation;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\BookRepository;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateReservation', DateType::class, [
                'widget' => 'single_text', // input type="date"
                'disabled' => true,
            ])
            ->add('dateReturn', DateType::class, [
                'widget' => 'single_text',
                'data' => (new \DateTime())->modify('+14 days'), // default return date is 14 days from now
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'pending',
                    'Confirmed' => 'confirmed',
                    'Cancelled' => 'cancelled',
                ],
            ])

            ->add('book', EntityType::class, [
                'class' => Book::class, // majuscule
                'choice_label' => 'title',
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title',
                'query_builder' => function (BookRepository $repo) {
                    return $repo->createQueryBuilder('b')
                        ->where('b.stock > 0');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,

        ]);
    }
}
