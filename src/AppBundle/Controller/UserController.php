<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 06.01.18
 * Time: 12:51
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserEditForm;
use AppBundle\Form\UserRegistrationForm;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use AppBundle\Service\PasswordEncoder;

class UserController extends Controller
{
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Welcome'.$user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main '
                );
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
        $user = $em->findOneBy(
            array("id" => $userId)
        );

        $form = $this->createForm(UserEditForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();

            //$encoder = $this->get("app.password_encoder");
            $encoder = $this->get("security.password_encoder")
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoder);

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profile edited'.$user->getEmail());

            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}