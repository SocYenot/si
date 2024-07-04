<?php
/**
 * Password Change Type.
 */
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Password Change Type
 */
class PasswordChangeType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'label.current_password',
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'label.new_password',
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'label.confirm_password',
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
