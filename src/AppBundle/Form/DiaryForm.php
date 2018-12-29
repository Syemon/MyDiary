<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiaryForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class, [
                'label' => 'form.title'
            ])
            ->add('note', TextareaType::class, [
                    'label' => 'form.note'
            ])
            ->add('attachment', FileType::class, [
                'label' => 'form.file_upload',
                'data_class' => null

            ])
            ->add('user', HiddenType::class, [
                'data_class' => 'AppBundle\Entity\User',
                'mapped' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Diary'
        ]);
    }

    /**
     * @return string|null
     */
    public function getBlockPrefix()
    {
        return 'app_bundle_diary_form';
    }
}
