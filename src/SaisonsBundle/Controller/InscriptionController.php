<?php

namespace SaisonsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Entités utilisés :
use PersonnesBundle\Entity\Personne;
use PersonnesBundle\Entity\Adherent;
use SaisonsBundle\Entity\Forfait;
use SaisonsBundle\Entity\ForfaitDanseNiveau;
use SaisonsBundle\Entity\TypeForfait;

class InscriptionController extends Controller{
	//recupérer l'id max de la table Personne
	public function maxIdAdherent(){
		return null;
	}
	

	// Etape 2 du processus d'inscription (Etape 1 dans PersonnesBundle/Controller/AdherentController)
	// Choix du type du forfait
	public function inscription_coursForfaitAction(Request $request, $id_adherent){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			$forfait = new Forfait();
			
			// Récupération de l'adhérent souhaitant s'inscrire
			$adherent = $em->getRepository('PersonnesBundle:Adherent')->find($id_adherent);
			if (null === $adherent) {
				throw new NotFoundHttpException("L'adhérent (id=".$id_adherent.")pour lequel vous voulez créer un forfait n'existe pas.");
			}		
			// Affectation de l'adhérent au forfait
			$forfait->setAdherent($adherent);
			
			
			// Récupération de la saison actuelle 
			$anneeActuelle = \Date('Y');
			$moisActuelle = \Date('m');		
			if ($moisActuelle >= 1 and $moisActuelle <=9){		
				$anneeActuelle -= 1;
			}
			$saison = $em->getRepository('SaisonsBundle:Saison')->findOneByAnneeDebutSaison($anneeActuelle);
			if (null === $saison) {
				throw new NotFoundHttpException("Un problème est apparu dans la recherche de la saison actuelle.");
			}
			// Affectation de la saison actuelle
			$forfait->setSaison($saison);
					
			$form = $this->get('form.factory')->createBuilder('form', $forfait
				)
				->add('typeForfait',     'entity', array(
						'class'    => 'SaisonsBundle:TypeForfait',
						'property' => 'libelleTypeForfait',
					))
				->add('Confirmer',      'submit')
				->getForm();
			
			$form->handleRequest($request);


			if ($form->isValid()) {
				$em->persist($forfait);
				$em->flush();
				$request->getSession()->getFlashBag()->add('notice', 'Type de forfait pris en compte...');
				return $this->redirect($this->generateUrl('inscription_coursForfaitDanse', array('id_forfait' => $forfait->getId())));
			}

			return $this->render('SaisonsBundle:inscription_choixForfait.html.twig',array(
				'form' => $form->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}




	// Etape 3 du processus d'inscription : Affectation des danses et niveaux de danse (selon le type de forfait choisi)
	public function inscription_coursForfaitDanseAction(Request $request, $id_forfait){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			
			// Récupération du forfait en cours de création
			$forfait = $em->getRepository('SaisonsBundle:Forfait')->find($id_forfait);
			if (null === $forfait) {
				throw new NotFoundHttpException("Le forfait (id=".$id_forfait.") semble ne plus exister...");
			}
			
			// Récupération du type du forfait pour déterminer le nombre de danses :
			$typeForfait = $forfait->getTypeForfait();
			$nbDanses = $typeForfait->getNbDanses(); 
			
			// FORMULAIRE 
			// Nombre de champs selon le $nbDanses
			// groupe de champs (idDanse+idNiveau) différencier selon un n°
			// Ex : idDanse2 et idNiveau2 correspondent à un 2ème choix de danse dans le forfait
			//		Donc ce type de forfait inclut au minimum 2 danses.

			$form = $this->get('form.factory')->createBuilder('form')
					->add('idDanse1',     'entity', array(
							'label' => "Danse",
							'class'    => 'SaisonsBundle:Danse',
							'property' => 'nomDanse',
						))
					->add('idNiveau1',     'entity', array(
							'label' => "Niveau de danse",
							'class'    => 'SaisonsBundle:NiveauDanse',
							'property' => 'libelleNiveauDanse',
						))
				
				->getForm();

			// A l'aide du type de forfait (nb de danses inclus), ajout dynamique de groupe de champs (idDanse+idNiveau) :
			$cpt=2;
			while($cpt<=$nbDanses){
					$form->add('idDanse'.$cpt,     'entity', array(
							'label' => $cpt."ème danse",
							'class'    => 'SaisonsBundle:Danse',
							'property' => 'nomDanse',
						));
					$form->add('idNiveau'.$cpt,     'entity', array(
							'label' => "Niveau de la ".$cpt."ème danse",
							'class'    => 'SaisonsBundle:NiveauDanse',
							'property' => 'libelleNiveauDanse',
						));
				$cpt++;
			}

			$form->add('Suivant','submit');

			$form->handleRequest($request);
			if ($form->isValid()) {
				// Validation de chaque groupe de champs (idDanse+idNiveau)
				// Chaque groupe de champs donne naissance à une..
				// .. instance de ForfaitDanseNiveau (Association:Forfait-Danse-Niveau)
				$cptD = 1;
				while($cptD<=$nbDanses){
					$dansesNiveauChoisi = new ForfaitDanseNiveau();
					// Affetation du forfait, pareil peu importe l'instance ici.
					$dansesNiveauChoisi->setIdForfait($forfait);
					
					// Ci-dessous, $cptD permet de differencier chaque groupe de champs (cf ajout dynamique + haut)
					$danse = $form["idDanse".$cptD]->getData();
					$niveau = $form["idNiveau".$cptD]->getData();
					
					$dansesNiveauChoisi->setIdDanse($danse);
					$dansesNiveauChoisi->setIdNiveau($niveau);
					
					$em->persist($dansesNiveauChoisi);
					$cptD++;
				}
				
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', 'Danse(s) et niveau(x) de danse bien pris en compte.');
			  return $this->redirect($this->generateUrl('inscription_coursForfaitPaiement', array('id_forfait' => $id_forfait)));
			}

			return $this->render('SaisonsBundle:inscription_choixForfaitDanse.html.twig',array(
				'form' => $form->createView()
			));	
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}



	
	// Dernière étape du processus d'inscription : Le mode de paiement
	public function inscription_coursForfaitPaiementAction(Request $request, $id_forfait){
		if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
			$em = $this->getDoctrine()->getManager();
			
			// Récupération du forfait en cours de création
			$forfait = $em->getRepository('SaisonsBundle:Forfait')->find($id_forfait);
			if (null === $forfait) {
				throw new NotFoundHttpException("Le forfait (id=".$id_forfait.") semble ne plus exister...");
			}
			
			$form = $this->get('form.factory')->createBuilder('form', $forfait)
				->add('modePaiement', 'choice', array(
				    'choices' => array('Carte Bancaire' => 'Carte Bancaire', 'Espèce' => 'Espèce', 'Chèque' => 'Chèque')))
				->add('Confirmer',      'submit')
				->getForm();

			$form->handleRequest($request);
			
			if ($form->isValid()) {
			  $em->persist($forfait);
			  $em->flush();
			  $request->getSession()->getFlashBag()->add('notice', "Mode de paiement prévu bien pris en compte. Afin de finaliser l'inscription, le réglement doit être effectué.");
			  return $this->redirect($this->generateUrl('administration'));
			}

			return $this->render('SaisonsBundle:inscription_choixForfaitPaiement.html.twig',array(
				'form' => $form->createView()
			));
		}
		else{
			return $this->redirect($this->generateUrl('accueil'));
		}
	}



}



//~ $form = $this->get('form.factory')->createBuilder('form', $dansesNiveauChoisi)
				//~ ->add('idDanse',     'entity', array(
						//~ 'class'    => 'SaisonsBundle:Danse',
						//~ 'property' => 'nomDanse',
					//~ ))
				//~ ->add('idNiveau',     'entity', array(
						//~ 'class'    => 'SaisonsBundle:NiveauDanse',
						//~ 'property' => 'libelleNiveauDanse',
					//~ ))
			//~ ->add('Suivant','submit')
			//~ ->getForm();
		//~ $form->handleRequest($request);

//~ $em = $this->getDoctrine()->getManager();
		  //~ $em->persist($dansesNiveauChoisi);
		  //~ $em->flush();
		  //~ $request->getSession()->getFlashBag()->add('notice', 'Danse et niveau bien pris en compte.');
		  //~ //return $this->redirect('inscription_coursForfaitPaiement');
		  //~ return $this->redirect($this->generateUrl('inscription_coursForfaitPaiement', array('id_forfait' => $id_forfait)));
