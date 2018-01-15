<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserChangePasswordForm;
use AppBundle\Form\UserEditForm;
use AppBundle\Form\UserRegistrationForm;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;

class UserController extends Controller
{
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
     * @Route("/activate/{token}")
     */
    public function confirmAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
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

        return $this->get('security.authentication.guard_handler')
            ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->get('app.security.login_form_authenticator'),
                'main '
            );
    }

    /**
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(UserRegistrationForm::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setConfirmationToken(bin2hex(random_bytes(10)));
            $this->sendConfirmationEmailMessage($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('user/register.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit", name="user_edit")
     */
    public function editAction(Request $request)
    {
        $userId = $this->getUser();

        $em = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $em->findOneBy([
            "id" => $userId
        ]);

        $form = $this->createForm(UserEditForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profile edited'.$user->getEmail());
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/changePassword", name="user_change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $userId = $this->getUser();

        $em = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $em->findOneBy(
            array("id" => $userId)
        );

        $form = $this->createForm(UserChangePasswordForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $encoder = $this->get("security.password_encoder")
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoder);

            $em->persist($user);
            $em->flush();
            $this->addFlash(
                'success',
                'Password has been changed'.$user->getEmail()
            );

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
