<?php


namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;

class UserType extends AbstractType
{
    protected $em;
    protected $util;
    public function __construct(EntityManagerInterface $em, Utils $util)
    {
        $this->em = $em;
        $this->util = $util;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name')
            ->add('email', EmailType::class, array(
                'constraints' => array(
                    new NotBlank(),
                    new Email()
                )
            ))
            ->add('phone',TextType::class, array(
                'constraints' => array(
                    new NotBlank(),
                )
            ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User',
            'locale' => 'en'
        ));
    }
}