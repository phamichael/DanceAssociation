<?php

namespace CoursBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


// Entités utilisés :
use CoursBundle\Entity\Cours;
use SaisonsBundle\Entity\Salle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CoursController extends Controller{
	
	// Page d'accueil sur les Cours (présentation générale) :
	public function accueilAction(){
		$em = $this->getDoctrine()->getManager();
		$lesCours = $em->getRepository('CoursBundle:Cours')->findAll();
		
		return $this->render('CoursBundle:accueil_cours.html.twig', array(
			'lesCours' => $lesCours
		));
	}
	
	public function planningAction(){
		$em = $this->getDoctrine()->getManager();
		$lesCours = $em->getRepository('CoursBundle:Cours')->findAll();
		$leslundi = $em->getRepository('CoursBundle:Cours')->findByJourCours("lundi");
		$lesmardi = $em->getRepository('CoursBundle:Cours')->findByJourCours("mardi");
		$lesmercredi = $em->getRepository('CoursBundle:Cours')->findByJourCours("mercredi");
		$lesjeudi = $em->getRepository('CoursBundle:Cours')->findByJourCours("jeudi");
		$lesvendredi = $em->getRepository('CoursBundle:Cours')->findByJourCours("vendredi");
		$lessamedi = $em->getRepository('CoursBundle:Cours')->findByJourCours("samedi");
		$lesdimanche = $em->getRepository('CoursBundle:Cours')->findByJourCours("dimanche");

		return $this->render('CoursBundle:planning_cours.html.twig', array(
			'leslundi' => $leslundi, 'lesmardi' => $lesmardi, 'lesmercredi' => $lesmercredi,
			'lesjeudi' => $lesjeudi, 'lesvendredi' => $lesvendredi, 'lessamedi' => $lessamedi,
			'lesdimanche' => $lesdimanche, 'lesCours' => $lesCours
		));
	}
	

	public function ficheAction($id){
		$em = $this->getDoctrine()->getManager();
		$cours = $em->getRepository('CoursBundle:Cours')->findOneById($id);
		if (null === $cours) {
			throw new NotFoundHttpException("Le cours ".$id." n'existe pas.");
		}
		return $this->render('CoursBundle:fiche_cours.html.twig', array(
			'cours' => $cours
		));
	}
	
	// Page d'administration des cours :
	public function administrationAction(Request $request){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$lesCours = $em->getRepository('CoursBundle:Cours')->findAll();
			
			// Gestion du formulaire :
			$cours = new Cours();
			//$cours->setDateCours(new \Datetime());
			//$cours->setDateCours(new \Datetime());
			$cours->setHeureDebutCours(new \Datetime());
			$cours->setHeureFinCours(new \Datetime());
			
			// Initialisation du champ "saison" à l'année courante (celle du début de saison) :
			$anneeActuelle = \Date('Y');
			$moisActuelle = \Date('m');		
			if ($moisActuelle >= 1 and $moisActuelle <=9){		
				$anneeActuelle -= 1;
			}
			$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeActuelle);
			if (null === $saison) {
				throw new NotFoundHttpException("Saison inexestante:". $anneeActuelle);
			}
			
			$form = $this->get('form.factory')->createBuilder('form', $cours)
				->add('jourCours',   'text')
				->add('heureDebutCours',    'time')
				->add('heureFinCours',    'time')
				->add('saison',     'entity', array(
					'class'    => 'SaisonsBundle:Saison',
					'property' => 'anneeDebutSaison',
					'multiple' => true
				))
				->add('danse',     'entity', array(
					'class'    => 'SaisonsBundle:Danse',
					'property' => 'nomDanse',
				))
				->add('niveauxDanse',     'entity', array(
					'class'    => 'SaisonsBundle:NiveauDanse',
					'property' => 'libelleNiveauDanse',
				))
				->add('salle',     'entity', array(
					'class'    => 'SaisonsBundle:Salle',
					'property' => 'uniqueName',
				))
				
				->add('professeurAssociation',     'entity', array(
					'class'    => 'PersonnesBundle:ProfesseurAssociation',
					'property' => 'personne.nomPersonne',
					'multiple' => true
				))
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			$form->handleRequest($request);

			// On vérifie que les valeurs entrées sont correctes
			if ($form->isValid()) {
			  // On l'enregistre notre objet $cours dans la base de données, par exemple
			  $em = $this->getDoctrine()->getManager();
			  $em->persist($cours);
			  $em->flush();

			  $request->getSession()->getFlashBag()->add('notice', 'Le cours '.$cours->getId().' a bien été ajouté.');

			  // On redirige vers la page de visualisation du cours nouvellement créé
			  return $this->redirect($this->generateUrl('cours_fiche', array('id' => $cours->getId())));
			}
			
			
			return $this->render('CoursBundle:administration_cours.html.twig', array(
				'lesCours' => $lesCours,
				'form' => $form->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	
	// Fiche d'administration d'un cours (édition et suppression)
	public function editSupprAction(Request $request, $id){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			
			$cours = $em->getRepository('CoursBundle:Cours')->findOneById($id);
			if (null === $cours) {
				throw new NotFoundHttpException("Le cours ".id." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			// Initialisation du champ "saison" à l'année courante (celle du début de saison) :
			$anneeActuelle = \Date('Y');
			$moisActuelle = \Date('m');		
			if ($moisActuelle >= 1 and $moisActuelle <=9){		
				$anneeActuelle -= 1;
			}
			$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeActuelle);
			if (null === $saison) {
				throw new NotFoundHttpException("Saison inexistante");
			}
			
			$form = $this->get('form.factory')->createBuilder('form', $cours)
				->add('jourCours',   'text')
				->add('heureDebutCours',    'time')
				->add('heureFinCours',    'time')
				->add('saison',     'entity', array(
					'class'    => 'SaisonsBundle:Saison',
					'property' => 'anneeDebutSaison',
					'multiple' => true
				))
				->add('danse',     'entity', array(
					'class'    => 'SaisonsBundle:Danse',
					'property' => 'nomDanse',
				))
				->add('niveauxDanse',     'entity', array(
					'class'    => 'SaisonsBundle:NiveauDanse',
					'property' => 'libelleNiveauDanse',
				))
				->add('salle',     'entity', array(
					'class'    => 'SaisonsBundle:Salle',
					'property' => 'uniqueName',
				))			
				->add('professeurAssociation',     'entity', array(
					'class'    => 'PersonnesBundle:ProfesseurAssociation',
					'property' => 'personne.nomPersonne',
					'multiple' => true
				))
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$em->remove($cours);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "Le cours a bien été supprimé.");
				return $this->redirect($this->generateUrl('cours_accueil'));
			}
			elseif ($form->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Cours bien modifié.');
			  return $this->redirect($this->generateUrl('cours_fiche', array('id' => $cours->getId())));
			}
			
			
			return $this->render('CoursBundle:editSuppr_cours.html.twig', array(
				'cours' => $cours,
				'form' => $form->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}


}
