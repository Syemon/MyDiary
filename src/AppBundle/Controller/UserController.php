<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserChangePasswordForm;
use AppBundle\Form\UserEditForm;
use AppBundle\Form\UserRegistrationForm;
use AppBundle\Security\LoginFormAuthenticator;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends Controller
{
    /**
     * @param User $user
     */
    public function sendConfirmationEmailMessage(User $user)
    {
        $transport = new Swift_SmtpTransport(
            $this->getParameter('mailer_transport'),
            465, 'ssl'
        );
        $transport->setHost($this->getParameter('mailer_host'));
        $transport->setUsername($this->getParameter('mailer_user'));
        $transport->setPassword($this->getParameter('mailer_password'));

        $mailer = new \Swift_Mailer($transport);

        $confirmationToken = $user->getConfirmationToken();
        $subject = "Account activation";
        $email = $user->getEmail();

        $renderedTemplate = $this->renderView('emails/registration.html.twig', [
            'user' => $user,
            'confirmationLink' => '127.0.0.1:8000/activate/'.$confirmationToken
        ]);

        $message = (new \Swift_Message($subject))
            ->setFrom($this->getParameter('mailer_user'))
            ->setTo($email)
            ->setBody($renderedTemplate,'text/html');

        $mailer->send($message);

        $this->addFlash(
            'success',
            'Mail confirmation has been sent on adress: '.$user->getEmail());
    }

    /**
     * @param Request $request
     * @param string $token
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function confirmAction(
        Request $request,
        $token,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')
            ->findOneBy([
                'confirmationToken' => $token
            ]);

        if (empty($user)) {
            $this->createNotFoundException('An account has not been found');
        }

        $user->setIsActive(true);

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Welcome '.$user->getNickname().'!');

        return $guardAuthenticatorHandler
            ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginFormAuthenticator,
                'main '
            );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function registerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserRegistrationForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setConfirmationToken(bin2hex(random_bytes(10)));
            $em->persist($user);
            $em->flush();

            $this->sendConfirmationEmailMessage($user);
        }

        return $this->render('user/register.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $oldUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(UserEditForm::class, $oldUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profile edited'.$user->getEmail());
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function changePasswordAction(
        Request $request,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $oldUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(UserChangePasswordForm::class, $oldUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $password = $passwordEncoder
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();
            $this->addFlash(
                'success',
                'Password has been changed'.$user->getEmail()
            );

            return $guardAuthenticatorHandler
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $loginFormAuthenticator,
                    'main'
                );
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
