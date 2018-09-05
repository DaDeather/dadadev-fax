<?php

namespace App\Form;

use App\Entity\Fax;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class FaxSendType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @SuppressWarnings(PHPMD)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'toPhoneNumber',
                TextType::class,
                [
                    'label' => 'fax_to',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'fax_file',
                    'multiple' => false,
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(),
                        new File([
                            'mimeTypes' => ['application/pdf'],
                        ]),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fax::class,
            'csrf_protection' => false,
        ]);
    }
}
