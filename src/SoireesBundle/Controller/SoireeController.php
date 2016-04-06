<?php

namespace SoireesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Entités utilisés :
use SoireesBundle\Entity\Soiree;
use SaisonsBundle\Entity\Salle;
use CoursBundle\Entity\Cours;

class SoireeController extends Controller{
	
	// Page d'accueil :
	public function accueilAction(){
		$em = $this->getDoctrine()->getManager();
		$lesSoirees = $em->getRepository('SoireesBundle:Soiree')->findAll();
		
		return $this->render('SoireesBundle:accueil_soirees.html.twig', array(
			'lesSoirees' => $lesSoirees
		));
	}
	
	public function ficheAction($titresoiree){
		$em = $this->getDoctrine()->getManager();
		$soiree = $em->getRepository('SoireesBundle:Soiree')->findOneByTitreSoiree($titresoiree);
		if (null === $soiree) {
			throw new NotFoundHttpException("La soirée ".$titresoiree." n'a pas encore été crée.");
		}
		return $this->render('SoireesBundle:fiche_soirees.html.twig', array(
			'soiree' => $soiree
		));
	}

	// Page d'administration des soirees :
	public function administrationAction(Request $request){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$lesSoirees = $em->getRepository('SoireesBundle:Soiree')->findAll();
			
			// Gestion du formulaire :
			$soiree = new Soiree();
			$soiree->setDateSoiree(new \Datetime());
			$soiree->setHeureDebutSoiree(new \Datetime());
			$soiree->setHeureFinSoiree(new \Datetime());
			

			$form = $this->get('form.factory')->createBuilder('form', $soiree)
				->add('titreSoiree',      'text')
				->add('dateSoiree',   'date')
				->add('heureDebutSoiree',    'time')
				->add('heureFinSoiree',    'time')
				->add('tarifSoiree',     'integer')
				->add('descriptifSoiree',     'textarea')
				->add('saison',     'entity', array(
					'class'    => 'SaisonsBundle:Saison',
					'property' => 'anneeDebutSaison',
				))
				->add('salles',     'entity', array(
					'class'    => 'SaisonsBundle:Salle',
					'property' => 'nomSalle',
					'multiple' => true
				))
				->add('danses',     'entity', array(
					'class'    => 'SaisonsBundle:Danse',
					'property' => 'nomDanse',
					'multiple' => true
				))
				->add('professeurs',     'entity', array(
					'class'    => 'PersonnesBundle:ProfesseurAssociation',
					'property' => 'personne.nomPersonne',
					'multiple' => true
				))
				->add('Ajouter',      'submit')
				->getForm()
			;
			
			$form->handleRequest($request);

			// On vérifie que les valeurs entrées sont correctes
			if ($form->isValid()) {
			  
			  // Récupération date depuis formulaire : 
			  $dateSoiree = $form["dateSoiree"]->getData();
			  $annee = $dateSoiree->format('Y');
			  $mois = $dateSoiree->format('m');
			  
			  // Récupération saison depuis formulaire :
			  $saison = $form["saison"]->getData();
			  $anneeSaison = $saison->getAnneeDebutSaison();
			  
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
			  // Fin du test.
			  
			  if ($correct == false){
				$request->getSession()->getFlashBag()->add('notice', 'La saison sélectionnée :'.$anneeSaison.' et la date de la soirée ('.$annee.' '.$mois.') ne correspondent pas.');
			  }
			  else{
			  
				  // On l'enregistre notre objet $soiree dans la base de données, par exemple
				  $em->persist($soiree);
				  $em->flush();

				  $request->getSession()->getFlashBag()->add('notice', 'Soiree bien ajouté.');

				  // On redirige vers la page de visualisation du soiree nouvellement créé
				  return $this->redirect($this->generateUrl('soiree_fiche', array('titresoiree' => $soiree->getTitreSoiree())));
				}
			}
			
			
			return $this->render('SoireesBundle:administration_soirees.html.twig', array(
				'lesSoirees' => $lesSoirees,
				'form' => $form->createView(),
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
		
	}
	
	
	public function editSupprAction(Request $request, $titresoiree){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			
			$soiree = $em->getRepository('SoireesBundle:Soiree')->findOneByTitreSoiree($titresoiree);
			if (null === $soiree) {
				throw new NotFoundHttpException("La soirée ".$titresoiree." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			// Formulaire d'édition :
			$form = $this->get('form.factory')->createBuilder('form', $soiree)
				->add('titreSoiree',      'text')
				->add('dateSoiree',   'date')
				->add('heureDebutSoiree',    'time')
				->add('heureFinSoiree',    'time')
				->add('tarifSoiree',     'integer')
				->add('descriptifSoiree',     'textarea')
				->add('saison',     'entity', array(
					'class'    => 'SaisonsBundle:Saison',
					'property' => 'anneeDebutSaison',
				))
				->add('salles',     'entity', array(
					'class'    => 'SaisonsBundle:Salle',
					'property' => 'nomSalle',
					'multiple' => true
				))
				->add('danses',     'entity', array(
					'class'    => 'SaisonsBundle:Danse',
					'property' => 'nomDanse',
					'multiple' => true
				))
				->add('professeurs',     'entity', array(
					'class'    => 'PersonnesBundle:ProfesseurAssociation',
					'property' => 'personne.nomPersonne',
					'multiple' => true
				))
				->add("Ajouter",      'submit')
				->getForm()
			;
			
			$cours_init = new Cours();
			// Initialisation du champ "saison" à l'année courante (celle du début de saison) :
			$anneeActuelle = \Date('Y');
			$moisActuelle = \Date('m');		
			if ($moisActuelle >= 1 and $moisActuelle <=9){		
				$anneeActuelle -= 1;
			}
			$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeActuelle);
			if (null != $saison) {
				$cours_init->addSaison($saison);
			}
			// Formulaire d'ajout cours d'initiation :
			$formAddCoursInit = $this->get('form.factory')->createBuilder('form', $cours_init)
				->add('jourCours',   'text')
				->add('heureDebutCours',    'time')
				->add('heureFinCours',    'time')
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
				$em->remove($soiree);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "La soirée a bien été supprimé.");
				return $this->redirect($this->generateUrl('soirees_accueil'));
			}
			if ($formAddCoursInit->handleRequest($request)->isValid()) {  
				// Danses de la soirée
				$coursinit_cur = $soiree->getDanses()->toArray();
				if (\in_array($formAddCoursInit["danse"]->getData(),$coursinit_cur) == false){
					// La danse du cours d'initiation à ajouter n'appartient pa à celles pratiquées lors de la soirée :
					$request->getSession()->getFlashBag()->add('notice', "La danse du cours d'initiation doit faire partie de celles exercées lors de la soirée.");
					//throw new NotFoundHttpException("La danse du cours d'initiation doit faire partie de celles exercées lors de la soirée.");
				}
				else{	
					$em->persist($cours_init);		   
					$soiree->addCoursinitiation($cours_init);
					$em->persist($soiree);
					$em->flush();
					$request->getSession()->getFlashBag()->add('notice', "Cours d'initiation bien ajouté à la soirée !");
					return $this->redirect($this->generateUrl('soirees_administration'));
				}
			}
			elseif ($form->handleRequest($request)->isValid()) {
			  // Inutile de persister ici, Doctrine connait déjà notre annonce
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Soirée bien modifiée.');
			  return $this->redirect($this->generateUrl('soirees_administration'));
			}
			
			return $this->render('SoireesBundle:editSuppr_Soiree.html.twig', array(
				'soiree' => $soiree,
				'form' => $form->createView(),
				'formSuppr' => $formSuppr->createView(),
				'formAddCoursInit' => $formAddCoursInit->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
		
	
	}
	
	
	
	// Page d'inscription d'un adhérent à une soirée :
	public function inscriptionAction(Request $request, $titresoiree){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$soiree = $em->getRepository('SoireesBundle:Soiree')->findOneByTitreSoiree($titresoiree);
			if (null === $soiree) {
				throw new NotFoundHttpException("La soirée ".$titresoiree." n'existe pas.");
			}
			// Récupération des profs affectés à cet soirée
			$lesInscrits = $soiree->getInscrits();
			
			// Formulaire pour l'ajout à la soirée d'un professeur enregistré au sein de l'association :		
			$formAddAdherent = $this->get('form.factory')->createBuilder('form')
				->add('Adherents',     'entity', array(
					'class'    => 'PersonnesBundle:Adherent',
					'property' => 'personne.nomPersonne',
				))
				->add('Inscrire cet adhérent à la soirée',      'submit')
				->getForm()
			;
							
			if ($formAddAdherent->handleRequest($request)->isValid()) {
				
				// Récupération du champ
				$adherent = $formAddAdherent["Adherents"]->getData();
	            
	            // Test si l'adhérent à ajouter n'est pas déjà inscrit à la soirée :
	            $inscritsTab = $lesInscrits->toArray();
				$id_adh_form = $adherent->getId();
				
				foreach ($inscritsTab as $s) {
					if ($s == $adherent){
						throw new NotFoundHttpException("L'adhérent est déjà inscrit à la soirée !!");
					}
				}
	            
	            $soiree->addInscrit($adherent);
	            $em->persist($soiree);
	            $em->flush();
	            $request->getSession()->getFlashBag()->add('info', "L'adhérent a bien été inscrit à la soirée.");
	            return $this->redirect($this->generateUrl('soirees_inscription', array('titresoiree' => $titresoiree)));
	        }
			
			return $this->render('SoireesBundle:inscriptionSoiree.html.twig', array(
				'soiree' => $soiree,
				'lesInscrits' => $lesInscrits,
				'formAddAdherent' => $formAddAdherent->createView(),
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	
	
	// Page de confirmation d'exclusion d'un stagiaire :
	public function virerAdherentAction(Request $request, $titresoiree, $idadherent){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$soiree = $em->getRepository('SoireesBundle:Soiree')->findOneByTitreSoiree($titresoiree);
			if (null === $soiree) {
				throw new NotFoundHttpException("La soirée ".$titresoiree." n'existe pas.");
			}
			
			$inscrit = $em->getRepository('PersonnesBundle:Adherent')->find($idadherent);
			if (null === $inscrit) {
				throw new NotFoundHttpException("L'inscrit (adhérent) ".$inscrit->getPersonne()->getNomPersonne()." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$soiree->removeInscrit($inscrit);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "L'adhérent a bien été exclus du la soirée.");
				return $this->redirect($this->generateUrl('soirees_inscription', array('titresoiree' => $titresoiree)));
			}
			
			return $this->render('SoireesBundle:confirmvirerAdherent.html.twig', array(
				'soiree' => $soiree,
				'inscrit' => $inscrit,
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	
	
	
	
	// Page d'affectation d'un professeur à une soirée :
	public function affectationAction(Request $request, $titresoiree){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$soiree = $em->getRepository('SoireesBundle:Soiree')->findOneByTitreSoiree($titresoiree);
			if (null === $soiree) {
				throw new NotFoundHttpException("La soirée ".$titresoiree." n'existe pas.");
			}
			// Récupération des profs affectés à cette soirée
			$lesProf = $soiree->getProfesseurs();
			
			// Formulaire pour l'ajout à la soirée d'un professeur enregistré au sein de l'association :		
			$formAddProf = $this->get('form.factory')->createBuilder('form')
				->add('Professeurs',     'entity', array(
					'class'    => 'PersonnesBundle:ProfesseurAssociation',
					'property' => 'personne.nomPersonne',
					'multiple' => true
				))
				->add('Affecter ce(s) professeur(s) à la soirée',      'submit')
				->getForm()
			;
							
			if ($formAddProf->handleRequest($request)->isValid()) {
				
				// Récupération des professeurs du champ du formulaire :
				$prof_rec = $formAddProf["Professeurs"]->getData();		
				$lesprofs = $prof_rec->toArray();

				
				// Les professeurs déjà affectés à la soirée
				$profsTab = $lesProf->toArray();

				foreach ($lesprofs as $prof) {
					// Test si le prof fait déjà partie de ceux affecté à la soirée 
					if (\in_array($prof,$profsTab) == false){
						// Affectation du professeur
						$soiree->addProfesseur($prof);
						$request->getSession()->getFlashBag()->add('info', "Le professeur a bien été affecté à la soirée.");
					}
					else{
						$request->getSession()->getFlashBag()->add('info', "Le professeur ".$prof->getPersonne()->getNomPersonne()." est déjà affecté à la soirée.");
					}
				}

	            $em->persist($soiree);
	            $em->flush();
	            return $this->redirect($this->generateUrl('soirees_affectation', array('titresoiree' => $titresoiree)));
	        }
			
			return $this->render('SoireesBundle:affectationSoiree.html.twig', array(
				'soiree' => $soiree,
				'lesProf' => $lesProf,
				'formAddProf' => $formAddProf->createView(),
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}
	
	
	
	// Page de confirmation d'exclusion d'un professeur :
	public function virerProfesseurAction(Request $request, $titresoiree, $idprof){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$soiree = $em->getRepository('SoireesBundle:Soiree')->findOneByTitreSoiree($titresoiree);
			if (null === $soiree){
				throw new NotFoundHttpException("La soirée ".$titresoiree." n'existe pas.");
			}
			
			$prof = $em->getRepository('PersonnesBundle:ProfesseurAssociation')->find($idprof);
			if (null === $prof) {
				throw new NotFoundHttpException("Le professeur ".$prof->getPersonne()->getNomPersonne()." n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
			
			if ($formSuppr->handleRequest($request)->isValid()) {
				$soiree->removeProfesseur($prof);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "La soiree a bien été supprimé.");
				return $this->redirect($this->generateUrl('soirees_affectation', array('titresoiree' => $titresoiree)));
			}
			
			return $this->render('SoireesBundle:confirmVirerProfSoiree.html.twig', array(
				'soiree' => $soiree,
				'prof' => $prof,
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}	
	
	
	
	
	
	public function editSupprCoursInitAction(Request $request, $titresoiree, $idcours){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			
			$soiree = $em->getRepository('SoireesBundle:Soiree')->findOneByTitreSoiree($titresoiree);
			if (null === $soiree) {
				throw new NotFoundHttpException("La soirée ".$titresoiree." n'existe pas.");
			}
			
			$lescours_soiree = $soiree->getCoursinitiation()->toArray();
			
			$cours_init = $em->getRepository('CoursBundle:Cours')->find($idcours);
			if (null != $cours_init){
				$cours_init = $em->getRepository('CoursBundle:Cours')->find($idcours);
			}
			else{
				throw new NotFoundHttpException("Le cours d'initiation (id:".$idcours.") n'existe pas.");
			}
			
			// Formulaire de suppression :
			$formSuppr = $this->createFormBuilder()->getForm();
				
			// Formulaire d'edition du cours d'initiation :
			$formAddCoursInit = $this->get('form.factory')->createBuilder('form', $cours_init)
				->add('jourCours',   'text')
				->add('heureDebutCours',    'time')
				->add('heureFinCours',    'time')
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
				$em->remove($cours_init);
				$em->flush();
			
				$request->getSession()->getFlashBag()->add('info', "Le cours d'initiation a bien été supprimé.");
				return $this->redirect($this->generateUrl('soiree_editsuppr',array('titresoiree'=>$titresoiree)));
			}
			// $formAddCoursInit = form d'édition du cours d'initiation
			elseif ($formAddCoursInit->handleRequest($request)->isValid()) {  
				// Danses de la soirée
				$coursinit_cur = $soiree->getDanses()->toArray();
				if (\in_array($formAddCoursInit["danse"]->getData(),$coursinit_cur) == false){
					// La danse du cours d'initiation à ajouter n'appartient pa à celles pratiquées lors de la soirée :
					$request->getSession()->getFlashBag()->add('notice', "La danse du cours d'initiation doit faire partie de celles exercées lors de la soirée.");
					//throw new NotFoundHttpException("La danse du cours d'initiation doit faire partie de celles exercées lors de la soirée.");
				}
				else{	
					$em->flush();
					$request->getSession()->getFlashBag()->add('notice', "Cours d'initiation bien ajouté à la soirée !");
					return $this->redirect($this->generateUrl('soiree_editsuppr',array('titresoiree'=>$titresoiree)));
				}
			}
			
			return $this->render('SoireesBundle:editSuppr_CoursInit.html.twig', array(
				'soiree' => $soiree,
				'cours' => $cours_init,
				'formAddCoursInit' => $formAddCoursInit->createView(),
				'formSuppr' => $formSuppr->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
		
	
	}
	


}
