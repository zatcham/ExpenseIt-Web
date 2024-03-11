<?php

namespace App\Form;

use App\Entity\Budget;
use App\Entity\Department;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userCompany = $options['user']->getCompany(); // TODO : Error handling
        $builder
            ->add('name')
            ->add('total_budget')
            ->add('per_employee_budget')
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($userCompany) {
                    return $er->createQueryBuilder('d')
                        ->where('d.company = :company')
                        ->setParameter('company', $userCompany);
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Budget::class,
            'user' => null // If user is not set
        ]);
    }
}
