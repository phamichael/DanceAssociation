<?php

namespace PersonnesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Entités utilisés :
use PersonnesBundle\Entity\Personne;
use PersonnesBundle\Entity\Professeur;
use PersonnesBundle\Entity\ProfesseurAssociation;

class ProfesseurController extends Controller{
	
	// Page administration professeur :
	public function accueil_adminAction(Request $request){
        if( $this->container->get('security.context')->isGranted("ROLE_ADMIN") ){
    		$em = $this->getDoctrine()->getManager();
    		
    		// Affichage du formulaire d'ajout

            $om = $this->container->get('doctrine.orm.entity_manager');
            $manager = $this->container->get('fos_user.user_manager');

            $user = $manager->createUser();

            $form = $this->get('form.factory')->createBuilder('form')
                            ->add('username',      'text')
                            ->add('email',      'email')
                            ->add('password',      'password')
                			->add('nomPersonne','text')
    			            ->add('prenomPersonne','text')
    			            ->add('adressePersonne','text')
    			            ->add('dateNaissancePersonne', 'date')
    			            ->add('telephonePersonne','integer')
                            ->add('Type', 'choice', 
                                array('choices' => 
                                    array('Professeur association' => 'Professeur association', 'Professeur exterieur' => 'Professeur exterieur'))
                            )
    			            ->add('Valider',      'submit')
    			            ->getForm();

            $form->handleRequest($request);


            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()){
            	// On l'enregistre notre objet $stage dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $username = $form["username"]->getData();
                $mail = $form["email"]->getData();
                $user->setUsername($username);
                $user->setEmail($mail);
                            
                // Vérification de l'existence ou non d'une personne avec le même username/mail
				$duplicata = false;				
				$username_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByUsername($username);
				if (null != $username_bd) {
					$duplicata = true;
				}
				$mail_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByEmailPersonne($mail);
				if (null != $mail_bd) {
					$duplicata = true;
				}
				
                
                if ($duplicata == false ){
                
					$user->setPassword($form["password"]->getData());
					$user->setNomPersonne($form["nomPersonne"]->getData());
					$user->setPrenomPersonne($form["prenomPersonne"]->getData());
					$user->setAdressePersonne($form["adressePersonne"]->getData());
					$user->setDateNaissancePersonne($form["dateNaissancePersonne"]->getData());
					$user->setTelephonePersonne($form["telephonePersonne"]->getData());
					$user->setEnabled(true);
					$user->setEmailPersonne($user->getEmail());
					$user->setPlainPassword($user->getPassword());
					$type = $form["Type"]->getData();

					if($type == "Professeur association"){
						$user->setRoles(array('ROLE_PROFASSOC','ROLE_PROF'));
						$professeur = new ProfesseurAssociation();
					}
					else{
						$user->setRoles(array('ROLE_PROF'));
						$professeur = new Professeur();
					}
					$professeur->setPersonne($user);

					$em->persist($user);
					$em->persist($professeur);
					$em->flush();
				}
				else{
					// Existence d'une personne avec le même pseudo/mail dans la base de données :
					$request->getSession()->getFlashBag()->add('notice', "Attention, pseudo/mail déjà enregistré au sein de l'association.");
				}
            }


            // Pour afficher les professeurs de la base de donnée
            $query = $this->getDoctrine()->getEntityManager()
                           ->createQuery('SELECT u FROM PersonnesBundle:Personne u WHERE u.roles LIKE :role or u.roles LIKE  :role2')
                           ->setParameter('role', '%"ROLE_PROFASSOC"%')
                           ->setParameter('role2', '%"ROLE_PROF"%');
                           // ->setParameter('role' => array('%"ROLE_PROFASSOC"%','%"ROLE_PROF"%'));
            $lesProfesseurs = $query->getResult();
            // $lesProfesseurs = $em->getRepository('PersonnesBundle:Professeur')->findAll();

    		return $this->render('PersonnesBundle:Professeur:administration_professeurs.html.twig', array(
    			'lesProfesseurs' => $lesProfesseurs, 'form' => $form->createView()
    		));
        }
        else{
            return $this->redirect($this->generateUrl('accueil'));
        }
	}




    // Fiche d'administration d'un professeur (édition et suppression)
    public function editSupprAction(Request $request, $idPersonne){
        if( $this->container->get('security.context')->isGranted("IS_AUTHENTICATED_REMEMBERED") ){
        $em = $this->getDoctrine()->getManager();
        
        $professeur = $em->getRepository('PersonnesBundle:Personne')->findOneById($idPersonne);
        $user_current = $professeur;
        if ($professeur === null ) {
            throw new NotFoundHttpException("Le professeur ".$idPersonne." n'existe pas.");
        }
        

        $form = $this->get('form.factory')->createBuilder('form')
                        ->add('username',      'text')
                        ->add('email',      'email')
                        ->add('password',      'password')
                        ->add('nomPersonne','text')
                        ->add('prenomPersonne','text')
                        ->add('adressePersonne','text')
                        ->add('dateNaissancePersonne', 'date')
                        ->add('telephonePersonne','integer')
                        ->add('Type', 'choice', 
                            array('choices' => 
                                array('Professeur association' => 'Professeur association', 'Professeur exterieur' => 'Professeur exterieur'))
                        )
                        ->add('Valider',      'submit')
                        ->getForm();

        
        $form->get('username')->setData($professeur->getUsername());
        $form->get('email')->setData($professeur->getEmail());
        $form->get('nomPersonne')->setData($professeur->getNomPersonne());
        $form->get('prenomPersonne')->setData($professeur->getPrenomPersonne());
        $form->get('adressePersonne')->setData($professeur->getAdressePersonne());
        $form->get('dateNaissancePersonne')->setData($professeur->getDateNaissancePersonne());
        $form->get('telephonePersonne')->setData($professeur->getTelephonePersonne());
        //on verifie le role de la personne connectée pour definir si profAsso ou non
        // $form->get('Type')->setData($professeur->getUsername());


        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $username = $form["username"]->getData();
            $mail = $form["email"]->getData();        
            
            // Vérification de l'existence ou non d'une personne avec le même username/mail
			$duplicata = false;			
			$username_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByUsername($username);
			if (null != $username_bd and $username_bd->getId()!=$idPersonne) {
				$duplicata = true;
			}
			
			$mail_bd  = $em->getRepository('PersonnesBundle:Personne')->findOneByEmailPersonne($mail);
			if (null != $mail_bd and $mail_bd->getId()!=$idPersonne) {
				$duplicata = true;
			}
            
            if ($duplicata == false){
            
				$professeur->setUsername($username);
				$professeur->setEmail($mail);
				$professeur->setPassword($form["password"]->getData());
				$professeur->setNomPersonne($form["nomPersonne"]->getData());
				$professeur->setPrenomPersonne($form["prenomPersonne"]->getData());
				$professeur->setAdressePersonne($form["adressePersonne"]->getData());
				$professeur->setDateNaissancePersonne($form["dateNaissancePersonne"]->getData());
				$professeur->setTelephonePersonne($form["telephonePersonne"]->getData());
				$professeur->setEnabled(true);
				$professeur->setEmailPersonne($professeur->getEmail());
				$professeur->setPlainPassword($professeur->getPassword());
				$type = $form["Type"]->getData();

				$role_cur = $professeur->getRoles();

				if($type == "Professeur association" && \in_array("ROLE_PROFASSOC", $role_cur)==false){
					$professeur->setRoles(array('ROLE_PROFASSOC'));
					$professeur_new = new ProfesseurAssociation();
					$professeur_new->setPersonne($professeur);
				
					$professeur_old = $em->getRepository('PersonnesBundle:Professeur')->findOneBy(array('personne' => $idPersonne));
					if ($professeur_old != null ) {
						$em->remove($professeur_old);
					}
					$em->persist($professeur_new);
				}
				elseif($type == "Professeur exterieur" && \in_array("ROLE_PROF", $role_cur)==false){
					$professeur->setRoles(array('ROLE_PROF'));
					// $professeur->removeRole('ROLE_PROFASSOC');
					$professeur_new = new Professeur();
					$professeur_new->setPersonne($professeur);
					
					$professeur_old = $em->getRepository('PersonnesBundle:ProfesseurAssociation')->findOneBy(array('personne' => $idPersonne));
					if ($professeur_old != null ) {
						$em->remove($professeur_old);
					}
					$em->persist($professeur_new);
				}

				$em->persist($professeur);
				// $em->persist($professeur_new);
				$em->flush();



				$professeur->setPlainPassword($professeur->getPassword());
				$em->flush();
				$request->getSession()->getFlashBag()->add('notice', 'professeur bien modifié.');

				//-----------------------------
				//Si l'utilisateur connecté est un adhérent et qu'il a modifié ses informations, on le redirige dans l'accueil 
				$utilisateurConnectee = $this->container->get('security.context')->getToken()->getUser();
				if($utilisateurConnectee->hasRole('ROLE_PROF')){
					return $this->redirect($this->generateUrl('mon_compte'));
				}
				//-----------------------------

				return $this->redirect($this->generateUrl('professeurs_administration'));
			}
			else{
				// Existence d'une personne avec le même pseudo/mail dans la base de données :
				$request->getSession()->getFlashBag()->add('notice', "Attention, pseudo/mail déjà enregistré au sein de l'association.");
			}
        }

        return $this->render('PersonnesBundle:Professeur:editSuppr_professeur.html.twig', array(
            'professeur' => $professeur,
            'form' => $form->createView(),
        ));
    }
    else{
        return $this->redirect($this->generateUrl('accueil'));
    }
    }
    

    


}
	
