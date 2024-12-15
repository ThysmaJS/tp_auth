<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $logger->info('Début du processus d\'inscription');

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logger->info('Formulaire soumis et valide');

            // Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);

            try {
                // Sauvegarder l'utilisateur dans la base de données
                $entityManager->persist($user);
                $entityManager->flush();

                $logger->info('Utilisateur enregistré avec succès');

                // Rediriger vers la page de connexion
                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                // Ajout d'un message d'erreur
                $logger->error('Erreur : Pseudo déjà utilisé.', ['exception' => $e]);
                $this->addFlash('error', 'Ce pseudo est déjà utilisé. Veuillez en choisir un autre.');
            }
        } elseif ($form->isSubmitted()) {
            $logger->warning('Formulaire invalide', [
                'errors' => (string) $form->getErrors(true, false),
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
