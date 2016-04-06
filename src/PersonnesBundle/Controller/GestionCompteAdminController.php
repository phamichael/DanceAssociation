<?php

namespace PersonnesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PersonnesBundle\Entity\Personne;

class GestionCompteAdminController extends Controller{
	public function moncompteAdminAction(Request $request){
		$utilisateur = $this->container->get('security.context')->getToken()->getUser();
		$form = $this->get('form.factory')->createBuilder('form', $utilisateur)
				->add('Modifier mes informations','submit')
				->getForm();
		$form->handleRequest($request);
		if ($form->isValid()){
			return $this->redirect($this->generateUrl('adherent_editsuppr',array('idPersonne' => $utilisateur->getId())));	
		}
		return $this->render('PersonnesBundle:moncompte_admin.html.twig',array('utilisateur'=>$utilisateur,
				'form' => $form->createView()));
	}
}