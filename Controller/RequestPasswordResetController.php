<?php

namespace SumoCoders\FrameworkMultiUserBundle\Controller;

use Doctrine\ORM\EntityNotFoundException;
use SumoCoders\FrameworkMultiUserBundle\Command\RequestPasswordResetHandler;
use SumoCoders\FrameworkMultiUserBundle\Exception\UserNotFound;
use SumoCoders\FrameworkMultiUserBundle\Form\RequestPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class RequestPasswordResetController
{
    /** @var EngineInterface */
    private $templating;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RequestPasswordResetHandler */
    private $requestPasswordResetHandler;

    /** @var Router */
    private $router;

    /** @var Translator */
    private $translator;

    /**
     * @param EngineInterface $templating
     * @param FormFactoryInterface $formFactory
     * @param RequestPasswordResetHandler $requestPasswordResetHandler
     * @param Router $router
     * @param Translator $translator
     */
    public function __construct(
        EngineInterface $templating,
        FormFactoryInterface $formFactory,
        RequestPasswordResetHandler $requestPasswordResetHandler,
        Router $router,
        Translator $translator
    ) {
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->requestPasswordResetHandler = $requestPasswordResetHandler;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function requestAction(Request $request)
    {
        $form = $this->formFactory->create(RequestPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->requestPasswordResetHandler->handle($form->getData());

                return new RedirectResponse($this->router->generate('multi_user_login'));
            } catch (UserNotFound $exception) {
                $errorMessage = $this->translator->trans(
                    'sumocoders.multiuserbundle.request_password_reset.username_not_found',
                    [
                        '%username%' => $form->getData()->userName
                    ],
                    'validators'
                );
                $form->addError(new FormError($errorMessage));
            }
        }

        return $this->templating->renderResponse(
            'SumoCodersFrameworkMultiUserBundle:PasswordReset:request.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
