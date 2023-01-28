<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;


class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => isset($options['data']) &&
                    $options['data']->getPublishedAt() != null ? $options['data']->getPublishedAt() : new DateTime('now'),
                'label' =>'date',
                'constraints' => [
                    new LessThanOrEqual([
                        'value'=>date('YmdHis'),
                        'message' => 'La date et l\'heure doivent être inferieures ou égales à maintenant'
                    ]),                  
                        
                ]
            ])
            ->add('title', TextType::class,[
                'label' =>'Titre',
                'required' => true,                
                'constraints' => [
                new NotBlank([
                  'message' => 'Veuillez saisir un titre'
                ]),
                new Length([
                  'max' => 100,
                  'maxMessage' => 'Le titre doit contenir moins de {{ limit }} caractères'
                ])
            ]])
            ->add('description')
            ->add('videoId', TextType::class,[
                'label' =>'Id de la vidéo',
                'required' => true,                
                'constraints' => [
                new NotBlank([
                  'message' => 'Veuillez saisir un id'
                ]),
                new Length([
                  'max' => 20,
                  'maxMessage' => 'Le titre doit contenir moins de {{ limit }} caractères'
                ])
            ]])
            ->add('playlist', EntityType::class, [
                'required' => true,
                'class' => Playlist::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une playlist'
            ])
            ->add('categories',  EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])            
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
