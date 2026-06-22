<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\DeploymentRequest;
use App\Entity\Environment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeploymentRequestType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('application', EntityType::class, [
                'class' => Application::class,
                'choice_label' => 'name',
                'label' => 'Application',
            ])
            ->add('targetEnvironment', EntityType::class, [
                'class' => Environment::class,
                'choice_label' => fn (Environment $environment) => (string) $environment,
                'label' => 'Environnement cible',
            ])
            ->add('version', TextType::class, ['label' => 'Version'])
            ->add('scheduledAt', DateTimeType::class, [
                'label' => 'Planifie le',
                'required' => false,
                'widget' => 'single_text',
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeploymentRequest::class,
        ]);
    }
}
