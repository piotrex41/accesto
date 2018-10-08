<?php

namespace AppBundle\Form;

use AppBundle\Entity\BlogPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('tags', TextType::class)
        ;

        $builder->get('tags')
            ->addModelTransformer(new CallbackTransformer(
                    function ($tagsAsArray) {
                        return implode(', ', $tagsAsArray);
                    },
                    function ($tagsAsString) {
                        return explode(', ', $tagsAsString);
                    }
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}