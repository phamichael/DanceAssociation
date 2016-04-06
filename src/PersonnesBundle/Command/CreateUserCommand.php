<?php
 
namespace PersonnesBundle\Command;
 
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Command\CreateUserCommand as BaseCommand;
 
class CreateUserCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('acme:user:create')
            ->getDefinition()->addArguments(array(
                new InputArgument('prenomPersonne', InputArgument::REQUIRED, 'prenomPersonne'),
                new InputArgument('nomPersonne', InputArgument::REQUIRED, 'nomPersonne'),
                new InputArgument('adressePersonne', InputArgument::REQUIRED, 'adressePersonne'),
                new InputArgument('dateNaissancePersonne', InputArgument::REQUIRED, 'dateNaissancePersonne'),
                new InputArgument('telephonePersonne', InputArgument::REQUIRED, 'telephonePersonne')
            ))
        ;
        $this->setHelp(<<<EOT
// L'aide qui va bien
EOT
            );
    }
 
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username   = $input->getArgument('username');
        $email      = $input->getArgument('email');
        $password   = $input->getArgument('password');
        $firstname  = $input->getArgument('prenomPersonne');
        $lastname   = $input->getArgument('nomPersonne');
        $adresse   = $input->getArgument('adressePersonne');
        $datenaiss   = $input->getArgument('dateNaissancePersonne');
        $tel   = $input->getArgument('telephonePersonne');
        $inactive   = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');
 
        /** @var \FOS\UserBundle\Model\UserManager $user_manager */
        $user_manager = $this->getContainer()->get('fos_user.user_manager');
 
        /** @var \Acme\AcmeUserBundle\Entity\User $user */
        $user = $user_manager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setEmailPersonne($email);
        $user->setPlainPassword($password);
        $user->setEnabled((Boolean) !$inactive);
        $user->setSuperAdmin((Boolean) $superadmin);
        $user->setPrenomPersonne($firstname);
        $user->setNomPersonne($lastname);
        $user->setAdressePersonne($adresse);
        $user->setDateNaissancePersonne(\DateTime::createFromFormat('d-m-Y', $datenaiss));
        $user->setTelephonePersonne($tel);
 
        $user_manager->updateUser($user);
 
        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }
 
    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);
        
        if (!$input->getArgument('prenomPersonne')) {
            $firstname = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a firstname:',
                function($firstname) {
                    if (empty($firstname)) {
                        throw new \Exception('Firstname can not be empty');
                    }
 
                    return $firstname;
                }
            );
            $input->setArgument('prenomPersonne', $firstname);
        }
        
        if (!$input->getArgument('nomPersonne')) {
            $lastname = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a lastname:',
                function($lastname) {
                    if (empty($lastname)) {
                        throw new \Exception('Lastname can not be empty');
                    }
 
                    return $lastname;
                }
            );
            $input->setArgument('nomPersonne', $lastname);
        }
        
        if (!$input->getArgument('adressePersonne')) {
            $adresse = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose an adress :',
                function($adresse) {
                    if (empty($adresse)) {
                        throw new \Exception('adress can not be empty');
                    }
 
                    return $adresse;
                }
            );
            $input->setArgument('adressePersonne', $adresse);
        }
        
        if (!$input->getArgument('dateNaissancePersonne')) {
            $datenaiss = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a birth date (format : jours-mois-annee) :',
                function($datenaiss) {
                    if (empty($datenaiss)) {
                        throw new \Exception('birth date can not be empty');
                    }
 
                    return $datenaiss;
                }
            );
            $input->setArgument('dateNaissancePersonne', $datenaiss);
        }
        
        if (!$input->getArgument('telephonePersonne')) {
            $tel = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a telefone number :',
                function($tel) {
                    if (empty($tel)) {
                        throw new \Exception('telefone number can not be empty');
                    }
 
                    return $tel;
                }
            );
            $input->setArgument('telephonePersonne', $tel);
        }
    }
}
