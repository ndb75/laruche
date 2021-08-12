<?php

namespace App\Input;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\File as File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class StockInput extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'required'  => true,
                'mapped'    => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [ // We want to let upload only csv or Excel files
                            'application/octet-stream',
                            'application/vnd.ms-excel',
                            'application/x-csv',
                            'application/csv',
                            'application/excel',
                            'application/vnd.msexcel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ],
                        'mimeTypesMessage' => "This document isn't valid.",
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'start' => 0,
        ]);
    }
}