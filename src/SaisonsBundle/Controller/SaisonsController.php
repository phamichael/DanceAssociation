<?php

namespace SaisonsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Entités utilisés :
use SaisonsBundle\Entity\Saison;
use SaisonsBundle\Entity\TypeForfait;

class SaisonsController extends Controller{
	
	// Fiche récapitulatif d'une saison
	public function ficheSaisonAction(Request $request, $anneeDebutSaison){
		$em = $this->getDoctrine()->getManager();
		$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeDebutSaison);
		if (null === $saison) {
			throw new NotFoundHttpException("La saison ".$anneeDebutSaison." n'existe pas.");
		}
		return $this->render('SaisonsBundle:fiche_saison.html.twig', array(
			'saison' => $saison
		));
	}
	
	
	// Voir toutes les saisons et tous les types de forfait
	// Démarrer une nouvelle saison et créer un nouveau type de forfait
	public function administrationAction(Request $request){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$lesSaisons = $em->getRepository('SaisonsBundle:Saison')->findAll();
			$lesTypesForfait = $em->getRepository('SaisonsBundle:TypeForfait')->findAll();


			$saison = new Saison();
			// Formulaire pour la création d'une saison :
			$form = $this->get('form.factory')->createBuilder('form', $saison)
				->add('anneeDebutSaison','integer',array('label' => 'Année de début de saison'))
				->add('tarifSaison','integer',array('label' => "Tarif d'adhésion à la saison"))
				->add('typesForfait',     'entity', array(
							'label' => "Type(s) de forfait proposé(s)",
							'class'    => 'SaisonsBundle:TypeForfait',
							'property' => 'libelleTypeForfait',
							'multiple' => true
						))
	            ->add('Valider',      'submit')
	            ->getForm();
	            
			$typeForfait = new TypeForfait();
			// Formulaire pour la création d'un type de forfait :
			$formTypeForfait = $this->get('form.factory')->createBuilder('form', $typeForfait)
				->add('libelleTypeForfait','text',array('label' => 'Libellé du type de forfait'))
				->add('nbDanses','integer',array('label' => "Nombre de danses incluses"))
				->add('tarifTypeForfait','integer',array('label' => "Tarif du forfait"))
	            ->add('Creer',      'submit')
	            ->getForm();

			if ($form->handleRequest($request)->isValid()) { 
	            $em = $this->getDoctrine()->getManager();
	            // Vérification que l'année de début n'existe pas déjà : NORMALEMENT PAR PK
	            $saison_post = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($form["anneeDebutSaison"]->getData());		
				if (null != $saison_post) {
					throw new NotFoundHttpException("Il existe déjà une saison commençant à l'année saisie !");
				}
				else{			
					$em->persist($saison);
					$em->flush();
					return $this->redirect($this->generateUrl('saison_creationSuccess', array('anneeDebutSaison' => $saison->getAnneeDebutSaison())));
				}
	        }
	        elseif ($formTypeForfait->handleRequest($request)->isValid()) {
				$em = $this->getDoctrine()->getManager();
	            $em->persist($typeForfait);
	            $em->flush();
			} 


			return $this->render('SaisonsBundle:administration_saisons.html.twig', array(
			'lesSaisons' => $lesSaisons, 
			'lesTypesForfait' => $lesTypesForfait, 
			'form' => $form->createView(),
			'formTypeForfait' => $formTypeForfait->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	// Page après création d'une saison invitant l'administrateur à mettre à jour le contenu de cette saison :
	public function creationSuccesAction($anneeDebutSaison){
		$em = $this->getDoctrine()->getManager();
		$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeDebutSaison);
		
		return $this->render('SaisonsBundle:saisonCreeSuccess.html.twig', array(
			'saison' => $saison
		));
	}
	
	// Editer (/Supprimer) une saison
	public function editSupprSaisonAction(Request $request, $anneeDebutSaison){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			
			$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeDebutSaison);
			
			if (null === $saison) {
				throw new NotFoundHttpException("La saison d'année de début ".$anneeDebutSaison." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			// Formulaire pour la modification d'un type de forfait :
			$form = $this->get('form.factory')->createBuilder('form', $saison)
				->add('anneeDebutSaison','integer',array('label' => 'Année de début de saison'))
				->add('tarifSaison','integer',array('label' => "Tarif d'adhésion à la saison"))
				->add('typesForfait',     'entity', array(
							'label' => "Type(s) de forfait proposé(s)",
							'class'    => 'SaisonsBundle:TypeForfait',
							'property' => 'libelleTypeForfait',
							'multiple' => true
						))
	            ->add('Valider',      'submit')
	            ->getForm();
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$em->remove($saison);
				$em->flush();		
				$request->getSession()->getFlashBag()->add('info', "La saison a bien été supprimée.");
				return $this->redirect($this->generateUrl('saisons_administration'));
			}
			elseif ($form->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Les informations de la saison ont bien été modifié.');
			  return $this->redirect($this->generateUrl('saisons_administration'));
			}
			
			return $this->render('SaisonsBundle:editSuppr_Saison.html.twig', array(
				'saison' => $saison,
				'form' => $form->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	} 
	
	
	// Editer/Supprimer un type de forfait
	public function editSupprTypeForfaitAction(Request $request, $libelleTypeForfait){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$type = $em->getRepository('SaisonsBundle:TypeForfait')->findOneByLibelleTypeForfait($libelleTypeForfait);
			
			if (null === $type) {
				throw new NotFoundHttpException("Le type de forfait ".$libelleTypeForfait." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			// Formulaire pour la modification d'un type de forfait :
			$formTypeForfait = $this->get('form.factory')->createBuilder('form', $type)
				->add('libelleTypeForfait','text',array('label' => 'Libellé du type de forfait'))
				->add('nbDanses','integer',array('label' => "Nombre de danses incluses"))
				->add('tarifTypeForfait','integer',array('label' => "Tarif du forfait"))
	            ->add('Creer',      'submit')
	            ->getForm();
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$em->remove($type);
				$em->flush();		
				$request->getSession()->getFlashBag()->add('info', "Le type de forfait a bien été supprimé.");
				return $this->redirect($this->generateUrl('saisons_administration'));
			}
			elseif ($formTypeForfait->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Type de forfait bien modifié.');
			  return $this->redirect($this->generateUrl('saisons_administration'));
			}
			
			return $this->render('SaisonsBundle:editSuppr_TypeForfait.html.twig', array(
				'type' => $type,
				'formTypeForfait' => $formTypeForfait->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	} 

}
