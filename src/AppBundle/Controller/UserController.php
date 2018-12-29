<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserChangePasswordForm;
use AppBundle\Form\UserEditForm;
use AppBundle\Form\UserRegistrationForm;
use AppBundle\Security\LoginFormAuthenticator;
use AppBundle\Service\EmailMessageFactory;
use AppBundle\Service\RegistrationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends Controller
{
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
        $translator = $this->get('translator');
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

        $this->addFlash('success', sprintf('%s %s!',
            $translator->trans('welcome') ,$user->getNickname()));

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
     * @param EmailMessageFactory $emailMessageFactory
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function registerAction(Request $request, EmailMessageFactory $emailMessageFactory)
    {
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserRegistrationForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setConfirmationToken(bin2hex(random_bytes(10)));
            $em->persist($user);
            $em->flush();

            /** @var RegistrationEmail $emailMessenger */
            $emailMessenger = $emailMessageFactory->getMessenger(EmailMessageFactory::TYPE_REGISTRATION);
            $emailMessenger
                ->setAddressee($user)
                ->sendMail();

            $this->addFlash(
                'success',
                sprintf('%s: %s',
                    $translator->trans('alert.user.mail_confirmation'), $user->getEmail()));
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
        $translator = $this->get('translator');

        $oldUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(UserEditForm::class, $oldUser);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', $translator->trans('alert.user.edited'));
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
        $translator = $this->get('translator');
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
                $translator->trans('alert.user.changed_password')
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
