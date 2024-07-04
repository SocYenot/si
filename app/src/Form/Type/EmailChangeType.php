<?php
/**
 * Email Change Type.
 */
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Email Change Type
 */
class EmailChangeType extends AbstractType
{
    /**
     * Form builder
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newEmail', EmailType::class, [
                'label' => 'label.new_email',
            ])
            ->add('confirmEmail', EmailType::class, [
                'label' => 'label.confirm_email',
            ]);
    }
    /**
     * Configures options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
