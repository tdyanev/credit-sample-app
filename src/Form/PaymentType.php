<?php

namespace App\Form;

use App\Entity\Payment;
use App\Entity\Credit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //dd($options);
        // $builder
        //     ->add('owner', EntityType::class, [
        //         'class' => User::class,
        //         'choice_label' => 'full_name',
        //     ]);

        if ($options['credit_id']) {

            // $builder->add('credit', HiddenType::class, [
            //     //'class' => Credit::class,
            //     //'choice_label' => 'id',
            //     'data' => intval($options['credit_id']),
            // ]);

        } else {

        $builder
            ->add('Credit', EntityType::class, [
                'class' => Credit::class,
                //'choices' => $options['credits'],
                'choice_label' => 'getName',
                'choice_value' => 'id',
            ]);
        }
            $builder->add('amount')
        ;

        //dump($options['credit_id']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
            'credit_id' => 0,
        ]);
    }
}
