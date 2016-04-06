<?php

namespace PersonnesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Entités utilisés :
use PersonnesBundle\Entity\Personne;
use PersonnesBundle\Entity\Adherent;

class AdherentController extends Controller{
	
	// Page administration adherent :
	public function accueil_adminAction(Request $request){
        if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
            $em = $this->getDoctrine()->getManager();
            // Pour afficher les adherents de la base de donnée
            $query = $this->getDoctrine()->getEntityManager()
                           ->createQuery('SELECT u FROM PersonnesBundle:Personne u WHERE u.roles LIKE :role')->setParameter('role', '%"ROLE_ADHERENT"%');
            $lesadherents = $query->getResult();

    		return $this->render('PersonnesBundle:Adherent:administration_adherents.html.twig', array(
    			'lesadherents' => $lesadherents//,'adherent' => $adherent, 'form' => $form->createView()
    		));
        }
        else{
            return $this->redirect($this->generateUrl('accueil'));
        }
	}


    public function inscription_coursAction(Request $request){
        if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
            $em = $this->getDoctrine()->getManager();
            
            // Affichage du formulaire d'ajout
            $adherent = new Adherent();

            $om = $this->container->get('doctrine.orm.entity_manager');
            $manager = $this->container->get('fos_user.user_manager');

            $user = $manager->createUser();

            $form = $this->get('form.factory')->createBuilder('form', $user)
                            ->add('username',      'text')
                            ->add('email',      'email')
                            ->add('password',      'password')
                            ->add('nomPersonne','text')
                            ->add('prenomPersonne','text')
                            ->add('adressePersonne','text')
                            ->add('dateNaissancePersonne', 'date')
                            ->add('telephonePersonne','integer')
                            ->add('Suivant',      'submit')
                            ->getForm();

            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()){
                // On l'enregistre notre objet $stage dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
				
				// Vérification de l'existence ou non d'une personne avec le même username/mail
				$duplicata = false;
				$username = $form["username"]->getData();				
				$username_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByUsername($username);
				if (null != $username_bd) {
					$duplicata = true;
				}
				if ($username_bd != null){
					$mail = $form["email"]->getData();
					$mail_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByEmailPersonne($mail);
					if (null != $mail_bd) {
						$duplicata = true;
					}
				}
				
				if ($duplicata == false){
				
					$user->setEnabled(true);
					$user->setEmailPersonne($user->getEmail());
					$user->setPlainPassword($user->getPassword());
					$user->setRoles(array('ROLE_ADHERENT'));
					$em->persist($user);
					$adherent->setPersonne($user);
					$em->persist($adherent);

					$em->flush();
					return $this->redirect($this->generateUrl('inscription_coursForfait', array('id_adherent' => $adherent->getId())));
				}
				else{
					// Existence d'une personne avec le même pseudo/mail dans la base de données :
					$request->getSession()->getFlashBag()->add('notice', "Attention, pseudo/mail déjà enregistré au sein de l'association.");
				}
            }

            return $this->render('PersonnesBundle:Adherent:admin_inscription_cours.html.twig', array('professeur' => $adherent, 'form' => $form->createView()
            ));
        }
        else{
            return $this->redirect($this->generateUrl('accueil'));
        }

    }


    public function editSupprAction(Request $request, $idPersonne){
        if( $this->container->get('security.context')->isGranted("IS_AUTHENTICATED_REMEMBERED") ){
            $em = $this->getDoctrine()->getManager();
            
            $adherent = $em->getRepository('PersonnesBundle:Personne')->findOneById($idPersonne);
            $nom = $adherent->getNomPersonne();
            if ($adherent === null ) {
                throw new NotFoundHttpException("L'adhérent ".$nom." n'existe pas.");
            }
            
            
            $form = $this->get('form.factory')->createBuilder('form', $adherent)
                            ->add('username',      'text')
                            ->add('email',      'email')
                            ->add('password',      'password')
                            ->add('nomPersonne','text')
                            ->add('prenomPersonne','text')
                            ->add('adressePersonne','text')
                            ->add('dateNaissancePersonne', 'datetime')
                            ->add('telephonePersonne','integer')
                            ->add('Valider',      'submit')
                            ->getForm();





            if ($form->handleRequest($request)->isValid()) {
				
				// Vérification de l'existence ou non d'une personne avec le même username/mail
				$duplicata = false;
				$username = $form["username"]->getData();				
				$username_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByUsername($username);
				if (null != $username_bd and $username_bd->getId()!=$idPersonne) {
					$duplicata = true;
				}
				
				$mail = $form["email"]->getData();
				$mail_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByEmailPersonne($mail);
				if (null != $mail_bd and $mail_bd->getId()!=$idPersonne) {
					$duplicata = true;
				}
								
				if ($duplicata == false ){			
					// Inutile de persister ici, Doctrine connait déjà notre annonce
					$adherent->setPlainPassword($adherent->getPassword());
					$em->flush();

					//-----------------------------
					//Si l'utilisateur connecté est un adhérent et qu'il a modifié ses informations, on le redirige dans l'accueil 
					$utilisateurConnectee = $this->container->get('security.context')->getToken()->getUser();
					if($utilisateurConnectee === $adherent){
						$request->getSession()->getFlashBag()->add('notice', 'Vos informations ont bien été modifiées.');
						return $this->redirect($this->generateUrl('mon_compteAdmin'));
					}
					else{
						$request->getSession()->getFlashBag()->add('notice', 'Adhérent bien modifié.');
						return $this->redirect($this->generateUrl('adherents_administration'));
					}
					//-----------------------------

					return $this->redirect($this->generateUrl('adherents_administration'));
            } else{
				// Existence d'une personne avec le même pseudo/mail dans la base de données :
				$request->getSession()->getFlashBag()->add('notice', "Attention, pseudo/mail déjà enregistré au sein de l'association.");
			}
         }



            return $this->render('PersonnesBundle:Adherent:editSuppr_adherent.html.twig', array(
                'adherent' => $adherent,
                'form' => $form->createView(),
                
            ));
        }
        else{
            return $this->redirect($this->generateUrl('accueil'));
        }
    }

}
	
