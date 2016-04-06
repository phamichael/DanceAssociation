<?php

namespace SaisonsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Entités utilisés :
use SaisonsBundle\Entity\Salle;


class SallesController extends Controller{
	
	
	// Page d'administration des salles :
	public function administrationAction(Request $request){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$lesSalles = $em->getRepository('SaisonsBundle:Salle')->findAll();
			
			// Formulaire 'Salle'
			$salle = new Salle();		
			$form = $this->get('form.factory')->createBuilder('form', $salle)
				->add('nomSalle',      'text')
				->add('capaciteSalle',      'integer')
				->add('adresseSalle',      'text')
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			
			// On vérifie le formulaire d'ajout de salle s'il a été 'submitté'
			if ($form->handleRequest($request)->isValid()) {
			  // On l'enregistre notre objet $salle dans la base de données, par exemple
			  $em = $this->getDoctrine()->getManager();
			  $em->persist($salle);
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'La salle '.$salle->getNomSalle().' a bien été ajouté.');
			  
			  return $this->redirect($this->generateUrl('salles_administration'));	  
			}
			
			return $this->render('SaisonsBundle:administration_salles.html.twig', array(
				'lesSalles' => $lesSalles,
				'form' => $form->createView(),
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	// Page d'édition/suppression d'une salle :
	public function editSupprSalleAction(Request $request, $nomsalle){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$salle = $em->getRepository('SaisonsBundle:Salle')->findOneByNomSalle($nomsalle);
			
			if (null === $salle) {
				throw new NotFoundHttpException("La salle ".$nomsalle." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			$form = $this->get('form.factory')->createBuilder('form', $salle)
				->add('nomSalle',      'text')
				->add('capaciteSalle',      'integer')
				->add('adresseSalle',      'text')
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$em->remove($salle);
				$em->flush();		
				$request->getSession()->getFlashBag()->add('info', "La salle a bien été supprimée.");
				return $this->redirect($this->generateUrl('salles_administration'));
			}
			elseif ($form->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Salle bien modifiée.');
			  return $this->redirect($this->generateUrl('salles_administration'));
			}
			
			return $this->render('SaisonsBundle:editSuppr_Salle.html.twig', array(
				'salle' => $salle,
				'form' => $form->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	

	//~ public function accueilAction(){
		//~ $em = $this->getDoctrine()->getManager();
		//~ $lesDanses = $em->getRepository('SaisonsBundle:Danse')->findAll();
//~ 
		//~ 
		//~ return $this->render('SaisonsBundle:accueil_danses.html.twig', array(
			//~ 'lesDanses' => $lesDanses
		//~ ));
	//~ }
	
	

}
