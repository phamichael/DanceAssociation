<?php

namespace PersonnesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfesseurType extends AbstractType
{

    /**
     * @param OptionsResolver $resolver
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder
    	  ->add('username',      'text')
    	  ->add('password',      'text')
	   	  ->add('nomPersonne',      'text')
	      ->add('prenomPersonne',   'text')
	      ->add('adressePersonne',   'text')
	      ->add('dateNaissancePersonne',  'date')
	      ->add('telephonePersonne', 'integer')
	      ->add('emailPersonne', 'text')
	      ->add('Ajouter',      'submit')
	    ;
	 }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PersonnesBundle\Entity\Professeur'
        ));
    }



    public function getName()
	{
		return 'professeurs_administration';
	}

}
