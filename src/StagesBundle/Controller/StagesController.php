<?php

namespace StagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Entités utilisés :
use StagesBundle\Entity\Stage;
use SaisonsBundle\Entity\Salle;

class StagesController extends Controller{
	
	// Page d'accueil sur les stages (présentation générale) :
	public function accueilAction(){
		$em = $this->getDoctrine()->getManager();
		$lesStages = $em->getRepository('StagesBundle:Stage')->findAll();
		
		return $this->render('StagesBundle:accueil_stages.html.twig', array(
			'lesStages' => $lesStages
		));
	}
	
	
	
	public function ficheAction($titrestage){
		$em = $this->getDoctrine()->getManager();
		$stage = $em->getRepository('StagesBundle:Stage')->findOneByTitreStage($titrestage);
		if (null === $stage) {
			throw new NotFoundHttpException("Le stage ".$titrestage." n'existe pas.");
		}
		return $this->render('StagesBundle:fiche_stages.html.twig', array(
			'stage' => $stage
		));
	}
	
	
	
	// Page d'administration des stages :
	public function administrationAction(Request $request){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$lesStages = $em->getRepository('StagesBundle:Stage')->findAll();
			
			// Gestion du formulaire :
			$stage = new Stage();
			$stage->setDateStage(new \Datetime());
			$stage->setHeureDebutStage(new \Datetime());
			$stage->setHeureFinStage(new \Datetime());
			$stage->setDelaiPreinscription(new \Datetime());
			
			// Initialisation du champ "saison" à l'année courante (celle du début de saison) :
			$anneeActuelle = \Date('Y');
			$moisActuelle = \Date('m');		
			if ($moisActuelle >= 1 and $moisActuelle <=9){		
				$anneeActuelle -= 1;
			}
			$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeActuelle);
			if (null != $saison) {
				$stage->setSaison($saison);
			}
			
			$form = $this->get('form.factory')->createBuilder('form', $stage)
				->add('titreStage',      'text' ,array('label' => 'Titre du stage'))
				->add('dateStage',   'date')
				->add('heureDebutStage',    'time')
				->add('heureFinStage',    'time')
				->add('montantPreinscription', 'integer')
				->add('delaiPreinscription', 'date')
				->add('capaciteStage', 'integer' ,array('label' => 'Nombre de place'))
				->add('tarifStage',     'integer')
				->add('descriptifStage',     'textarea')
				->add('saison',     'entity', array(
					'class'    => 'SaisonsBundle:Saison',
					'property' => 'anneeDebutSaison',
				))
				->add('danse',     'entity', array(
					'class'    => 'SaisonsBundle:Danse',
					'property' => 'nomDanse',
				))
				->add('niveauxDanse',     'entity', array(
					'class'    => 'SaisonsBundle:NiveauDanse',
					'property' => 'libelleNiveauDanse',
					'multiple' => true
				))
				->add('salles',     'entity', array(
					'class'    => 'SaisonsBundle:Salle',
					'property' => 'uniqueName',
					'multiple' => true
				))
				
				->add('professeurs',     'entity', array(
					'class'    => 'PersonnesBundle:Professeur',
					'property' => 'personne.nomPersonne',
					'multiple' => true,
					'required' => false
				))
				->add('professeursAsso',     'entity', array(
					'class'    => 'PersonnesBundle:ProfesseurAssociation',
					'property' => 'personne.nomPersonne',
					'multiple' => true,
					'required' => false
				))
				->add('Confirmer',      'submit')
				->getForm()
			;
			
			$form->handleRequest($request);

			// On vérifie que les valeurs entrées sont correctes
			if ($form->isValid()) {
			  // On l'enregistre notre objet $stage dans la base de données, par exemple
			  $em = $this->getDoctrine()->getManager();

			  $dateStageForm = $form["dateStage"]->getData();
			  $saisonForm = $form["saison"]->getData();
			  $datePreInscriptionForm = $form["delaiPreinscription"]->getData();
			  $correct = verifInfoStage($dateStageForm,$saisonForm,$datePreInscriptionForm);
			  
			  if ($correct == false){
				$request->getSession()->getFlashBag()->add('notice', "Problème de date : Soit l'année du stage ne correspond pas avec celle de la saison soit la date de préinscription est supérieur/égale à celle du stage.");
			  }
			  else{  		  
				  $em->persist($stage);
				  $em->flush();

				  $request->getSession()->getFlashBag()->add('notice', 'Le stage '.$stage->getTitreStage().' a bien été ajouté.');

				  // On redirige vers la page de visualisation du stage nouvellement créé
				  return $this->redirect($this->generateUrl('stage_fiche', array('titrestage' => $stage->getTitreStage())));
				}
			}
			
			
			return $this->render('StagesBundle:administration_stages.html.twig', array(
				'lesStages' => $lesStages,
				'form' => $form->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	
	// Fiche d'administration d'un stage (édition et suppression)
	public function editSupprAction(Request $request, $titrestage){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			
			$stage = $em->getRepository('StagesBundle:Stage')->findOneByTitreStage($titrestage);
			if (null === $stage){
				throw new NotFoundHttpException("Le stage ".$titrestage." n'existe pas.");
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
			if (null != $saison) {
				$stage->setSaison($saison);
			}
			
			$form = $this->get('form.factory')->createBuilder('form', $stage)
				->add('titreStage',      'text')
				->add('dateStage',   'date')
				->add('heureDebutStage',    'time')
				->add('heureFinStage',    'time')
				->add('montantPreinscription', 'integer')
				->add('delaiPreinscription', 'date')
				->add('capaciteStage', 'integer')
				->add('tarifStage',     'integer')
				->add('descriptifStage',     'textarea')
				->add('saison',     'entity', array(
					'class'    => 'SaisonsBundle:Saison',
					'property' => 'anneeDebutSaison',
				))
				->add('danse',     'entity', array(
					'class'    => 'SaisonsBundle:Danse',
					'property' => 'nomDanse',
				))
				->add('niveauxDanse',     'entity', array(
					'class'    => 'SaisonsBundle:NiveauDanse',
					'property' => 'libelleNiveauDanse',
					'multiple' => true
				))
				->add('salles',     'entity', array(
					'class'    => 'SaisonsBundle:Salle',
					'property' => 'uniqueName',
					'multiple' => true
				))			
				->add('professeurs',     'entity', array(
					'class'    => 'PersonnesBundle:Professeur',
					'property' => 'personne.nomPersonne',
					'multiple' => true,
					'required' => false
				))
				->add('professeursAsso',     'entity', array(
					'class'    => 'PersonnesBundle:ProfesseurAssociation',
					'property' => 'personne.nomPersonne',
					'multiple' => true,
					'required' => false
				))
				->add('Confirmer',      'submit')
				->getForm()
			;
			
		
			if ($formSuppr->handleRequest($request)->isValid()) {
				$em->remove($stage);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "Le stage a bien été supprimé.");
				return $this->redirect($this->generateUrl('stages_accueil'));
			}
			elseif ($form->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Stage bien modifié.');
			  return $this->redirect($this->generateUrl('stage_fiche', array('titrestage' => $stage->getTitreStage())));
			}
			
			
			return $this->render('StagesBundle:editSuppr_stage.html.twig', array(
				'stage' => $stage,
				'form' => $form->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	// Appelé lorsqu'un stage n'est plus viable.
	public function stageNonViableAction($titreStage){
		return $this->render('StagesBundle:stage_nonviable.html.twig', array(
			'titreStage' => $titreStage,
		));
	}
	
	
	
	
	// Page d'inscription à un stage :
	public function inscriptionAction(Request $request, $titrestage){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$stage = $em->getRepository('StagesBundle:Stage')->findOneByTitreStage($titrestage);
			if (null === $stage) {
				throw new NotFoundHttpException("Le stage ".$titrestage." n'existe pas.");
			}
			// Récupération des inscrits à ce stage
			$lesStagiaires = $stage->getStagiaires();
			
			if (\count($lesStagiaires) >= $stage->getCapaciteStage()){
				// Stage non viable	
				return $this->render('StagesBundle:stage_nonviable.html.twig', array(
					'titreStage' => $stage->getTitreStage(),
				));
			}
			else{
			
				// Un stagiaire est soit un adhérent soit une personne extérieur à l'association
				
				// Formulaire pour l'ajout d'un adhérent au stage :		
				$formAddAdherent = $this->get('form.factory')->createBuilder('form')
					->add('Adherents',     'entity', array(
						'class'    => 'PersonnesBundle:Adherent',
						'property' => 'personne.nomPersonne',
					))
					->add('Inscrire cet adhérent au stage',      'submit')
					->getForm()
				;
				
				// Formulaire permettant la saisie d'informations d'une personne extérieur à l'assoc :
				$om = $this->container->get('doctrine.orm.entity_manager');
				$manager = $this->container->get('fos_user.user_manager');
				$personne = $manager->createUser();
				
				$formAddPersonne = $this->get('form.factory')->createBuilder('form', $personne)
							->add('username',      'text')
							->add('email',      'email')
							->add('password',      'password')
							->add('nomPersonne','text')
							->add('prenomPersonne','text')
							->add('adressePersonne','text')
							->add('dateNaissancePersonne', 'date')
							->add('telephonePersonne','integer')
							->add('Inscrire ce nouveau membre au stage',      'submit')
							->getForm()
				;
				
				
				// Formulaire permettant la modification des stagiaires :
				$formEditStagiaires = $this->get('form.factory')->createBuilder('form', $stage)
							->add('stagiaires',      'entity', array(
								'class'    => 'PersonnesBundle:Personne',
								'property' => 'nomPersonne',
								'multiple' => true
							))
							->add('Confirmer les modifications',      'submit')
							->getForm()
				;
				
				
				if ($formAddAdherent->handleRequest($request)->isValid()) {
					
					// Récupération du champ
					$adherent = $formAddAdherent["Adherents"]->getData();
					
					// Test si l'adhérent à ajouter n'est pas déjà stagiaire :
					$stagiairesTab = $lesStagiaires->toArray();
					$id_adh_form = $adherent->getPersonne();
					
					foreach ($stagiairesTab as $s) {
						if ($s == $id_adh_form){
							throw new NotFoundHttpException("L'adhérent est déjà stagiaire !!");
						}
					}
					
					// 
					$stage->addStagiaire($adherent->getPersonne());
					$em->persist($stage);
					$em->flush();
					$request->getSession()->getFlashBag()->add('info', "L'adhérent a bien été ajouté au stage.");
					return $this->redirect($this->generateUrl('stages_inscription', array('titrestage' => $titrestage)));
				}
				elseif ($formAddPersonne->handleRequest($request)->isValid()){
					$personne->setEnabled(true);
					$personne->setEmailPersonne($personne->getEmail());
					$personne->setPlainPassword($personne->getPassword());
					$personne->setRoles(array('ROLE_STAGIAIRE'));
					$em->persist($personne);
					$stage->addStagiaire($personne);
					$em->persist($stage);
					$em->flush();
				}	
				
				return $this->render('StagesBundle:inscriptionStagiaire.html.twig', array(
					'stage' => $stage,
					'lesStagiaires' => $lesStagiaires,
					'formAddAdherent' => $formAddAdherent->createView(),
					'formAddPersonne' => $formAddPersonne->createView(),
					'formEditStagiaires' => $formEditStagiaires->createView()
				));
			}
		}
		else{
				return $this->redirect($this->generateUrl('accueil'));
			}
	}	



	
	// Page de confirmation d'exclusion d'un stagiaire :
	public function virerStagiaireAction(Request $request, $titrestage, $idpersonne){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$stage = $em->getRepository('StagesBundle:Stage')->findOneByTitreStage($titrestage);
			if (null === $stage) {
				throw new NotFoundHttpException("Le stage ".$titrestage." n'existe pas.");
			}
			
			$stagiaire = $em->getRepository('PersonnesBundle:Personne')->find($idpersonne);
			if (null === $stagiaire) {
				throw new NotFoundHttpException("Le stagiaire ".$stagiaire->getNomPersonne()." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$stage->removeStagiaire($stagiaire);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "Le stage a bien été supprimé.");
				return $this->redirect($this->generateUrl('stages_inscription', array('titrestage' => $titrestage)));
			}
			
			return $this->render('StagesBundle:confirmSupprStagiaire.html.twig', array(
				'stage' => $stage,
				'stagiaire' => $stagiaire,
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}	




	// Page d'affectation d'un professeur à un stage :
	public function affectationAction(Request $request, $titrestage){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$stage = $em->getRepository('StagesBundle:Stage')->findOneByTitreStage($titrestage);
			if (null === $stage) {
				throw new NotFoundHttpException("Le stage ".$titrestage." n'existe pas.");
			}
			// Récupération des profs affectés à ce stage
			$lesProf = $stage->getProfesseurs();
			
			// Formulaire pour l'ajout au stage d'un professeur enregistré au sein de l'association :		
			$formAddProf = $this->get('form.factory')->createBuilder('form')
				->add('Professeurs',     'entity', array(
					'class'    => 'PersonnesBundle:Professeur',
					'property' => 'personne.nomPersonne',
					'multiple' => true
				))
				->add('Affecter ce(s) professeur(s) au stage',      'submit')
				->getForm()
			;
							
			if ($formAddProf->handleRequest($request)->isValid()) {
				
				// Les professeurs récupéré depuis le champ du formulaire :
				$prof_rec = $formAddProf["Professeurs"]->getData();		
				$lesprofs = $prof_rec->toArray();
							
				// Les professeurs déjà affectés au stage
				$profsTab = $lesProf->toArray();

				foreach ($lesprofs as $prof) {
					// Test si le prof fait déjà partie de ceux affecté à la soirée 
					if (\in_array($prof,$profsTab) == false){
						// Affectation du professeur
						$stage->addProfesseur($prof);
						$request->getSession()->getFlashBag()->add('info', "Le professeur a bien été affecté au stage.");
					}
					else{
						$request->getSession()->getFlashBag()->add('info', "Le professeur ".$prof->getPersonne()->getNomPersonne()." est déjà affecté au stage.");
					}
				}
				
	            $em->persist($stage);
	            $em->flush();
	            return $this->redirect($this->generateUrl('stages_affectation', array('titrestage' => $titrestage)));
	        }
			
			return $this->render('StagesBundle:affectationProf.html.twig', array(
				'stage' => $stage,
				'lesProf' => $lesProf,
				'formAddProf' => $formAddProf->createView(),
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}	



	// Page de confirmation d'exclusion d'un professeur :
	public function virerProfesseurAction(Request $request, $titrestage, $idprof){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$stage = $em->getRepository('StagesBundle:Stage')->findOneByTitreStage($titrestage);
			if (null === $stage){
				throw new NotFoundHttpException("Le stage ".$titrestage." n'existe pas.");
			}
			
			$prof = $em->getRepository('PersonnesBundle:Professeur')->find($idprof);
			if (null === $prof) {
				throw new NotFoundHttpException("Le professeur ".$prof->getPersonne()->getNomPersonne()." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$stage->removeProfesseur($prof);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "Le stage a bien été supprimé.");
				return $this->redirect($this->generateUrl('stages_affectation', array('titrestage' => $titrestage)));
			}
			
			return $this->render('StagesBundle:confirmSupprProfStagiaire.html.twig', array(
				'stage' => $stage,
				'prof' => $prof,
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}	



}

// Fonction vérifiant les informations liées aux date d'un stage
function verifInfoStage($dateStageForm,$saisonForm,$datePreInscriptionForm){
	
	// Premièrement : Vérif de la saison et de la date du stage 
	$annee = $dateStageForm->format('Y');
	$mois = $dateStageForm->format('m');		  
	$anneeSaison = $saisonForm->getAnneeDebutSaison();
		  
	// Test si la date de soirée correspond avec la saison sélectionnée :
	$correct = true;
	if ($annee==$anneeSaison or $annee == $anneeSaison+1){
		if ($annee == $anneeSaison+1){
			if ($mois >= 9 and $mois<=12){
				$correct = false;
			}
		}
		else{
			if ($mois >= 1 and $mois< 9){
				$correct = false;
			}
		}
	}
	else{			  
		$correct = false;
	}
	  
	// Deuxièment : Vérif de la date de préinscription et de celle du stage
	if ($correct == true){
		if($datePreInscriptionForm >= $dateStageForm){
			$correct = false;
		}
	}
	    
	return $correct;
}





// Remarque :
// Formulaire d'ajout/edition identique (nom = form, template  = formAddEdit.html.twig)
// Exception de duplicata non géré (ajout stagiaire, affectation prof)
