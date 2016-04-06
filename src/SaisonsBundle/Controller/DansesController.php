<?php

namespace SaisonsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Entités utilisés :
use SaisonsBundle\Entity\Danse;
use SaisonsBundle\Entity\NiveauDanse;

class DansesController extends Controller{
	
	// Page d'administration des danses et niveaux :
	public function administrationAction(Request $request){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$lesDanses = $em->getRepository('SaisonsBundle:Danse')->findAll();
			$lesNiveaux = $em->getRepository('SaisonsBundle:NiveauDanse')->findAll();
			
			// Formulaire 'Danse'
			$danse = new Danse();		
			$form = $this->get('form.factory')->createBuilder('form', $danse)
				->add('nomDanse',      'text')
				->add('descriptifDanse',      'text')
				->add ('file',      'file')
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			
			// Formulaire 'Niveau de danse'
			$niveau = new NiveauDanse();		
			$formNiveau = $this->get('form.factory')->createBuilder('form', $niveau)
				->add('libelleNiveauDanse',      'text')
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			// On vérifie le formulaire d'ajout de danse s'il a été 'submitté'
			if ($form->handleRequest($request)->isValid()) {
			  // On l'enregistre notre objet $danse dans la base de données, par exemple
			  $danse -> upload();
			  $em = $this->getDoctrine()->getManager();
			  $em->persist($danse);
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'La danse '.$danse->getNomDanse().' a bien été ajouté.');
			  
			  // On redirige vers la page de visualisation du stage nouvellement créé
			  return $this->redirect($this->generateUrl('danses_administration'));	  
			}
			
			// Sinon on vérifie le formulaire d'ajout d'un niveau s'il a été 'submitté'
			elseif ($formNiveau->handleRequest($request)->isValid()) {
			  // On l'enregistre notre objet $niveau dans la base de données, par exemple
			  $em = $this->getDoctrine()->getManager();
			  $em->persist($niveau);
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Le niveau de danse '.$niveau->getLibelleNiveauDanse().' a bien été ajouté.');

			  // On redirige vers la page de visualisation du stage nouvellement créé
			  return $this->redirect($this->generateUrl('danses_administration'));
			}
			
			return $this->render('SaisonsBundle:administration_dansesNiveaux.html.twig', array(
				'lesDanses' => $lesDanses,
				'lesNiveaux' => $lesNiveaux,
				'form' => $form->createView(),
				'formNiveau' => $formNiveau->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	// Page d'édition/suppression d'une danse :
	public function editSupprDanseAction(Request $request, $nomdanse){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$danse = $em->getRepository('SaisonsBundle:Danse')->findOneByNomDanse($nomdanse);
			
			if (null === $danse) {
				throw new NotFoundHttpException("La danse ".$nomdanse." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			$form = $this->get('form.factory')->createBuilder('form', $danse)
				->add('nomDanse',      'text')
				->add('descriptifDanse',      'text')
				->add ('file',      'file')
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$em->remove($danse);
				$em->flush();		
				$request->getSession()->getFlashBag()->add('info', "La danse a bien été supprimée.");
				return $this->redirect($this->generateUrl('danses_administration'));
			}
			elseif ($form->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $danse -> upload();
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Danse bien modifiée.');
			  return $this->redirect($this->generateUrl('danses_administration'));
			}
			
			return $this->render('SaisonsBundle:editSuppr_Danse.html.twig', array(
				'danse' => $danse,
				'form' => $form->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	
	// Page d'édition/suppression d'un niveau de danse :
	public function editSupprNiveauAction(Request $request, $libelleniveau){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$niveau = $em->getRepository('SaisonsBundle:NiveauDanse')->findOneByLibelleNiveauDanse($libelleniveau);
			
			if (null === $niveau) {
				throw new NotFoundHttpException("Le niveau ".$niveaudanse." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			$formNiveau = $this->get('form.factory')->createBuilder('form', $niveau)
				->add('libelleNiveauDanse',      'text')
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$em->remove($niveau);
				$em->flush();		
				$request->getSession()->getFlashBag()->add('info', "Le niveau a bien été supprimé.");
				return $this->redirect($this->generateUrl('danses_administration'));
			}
			elseif ($formNiveau->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Niveau bien modifié.');
			  return $this->redirect($this->generateUrl('danses_administration'));
			}
			
			return $this->render('SaisonsBundle:editSuppr_Niveau.html.twig', array(
				'niveau' => $niveau,
				'formNiveau' => $formNiveau->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}

	public function accueilAction(){
		$em = $this->getDoctrine()->getManager();
		$lesDanses = $em->getRepository('SaisonsBundle:Danse')->findAll();

		
		return $this->render('SaisonsBundle:accueil_danses.html.twig', array(
			'lesDanses' => $lesDanses
		));
	}
	
	

}
