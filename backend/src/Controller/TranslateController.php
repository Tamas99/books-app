<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('api/v1/translate')]
class TranslateController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private LocaleSwitcher $localeSwitcher,
    ) {
    }

    #[Route('/translate', methods: ['GET'])]
    public function translate(): JsonResponse
    {
        $this->localeSwitcher->setLocale('hu');
        $translated = $this->translator->trans('Symfony is great');
        return new JsonResponse($translated);
    }
    
    #[Route('/timezone', methods: ['GET'])]
    public function timezone(): JsonResponse
    {
        $this->localeSwitcher->setLocale('hu');
        $translated = $this->translator->trans('published_at', ['publication_date' => new \DateTime('2019-01-25 11:30:00')]);
        return new JsonResponse($translated); // "Publikálva ekkor: 2019. január 25., péntek - 11:30:00 UTC"
    }
    
    #[Route('/progress', methods: ['GET'])]
    public function progress(): JsonResponse
    {
        $this->localeSwitcher->setLocale('hu');
        $translated = $this->translator->trans('progress', ['progress' => 0.82]);
        return new JsonResponse($translated); // "82% a munkából kész"
    }

    #[Route('/currency', methods: ['GET'])]
    public function currency(): JsonResponse
    {
        $this->localeSwitcher->setLocale('hu');
        $translated = $this->translator->trans('value_of_object', ['value' => 9988776.65]);
        return new JsonResponse($translated); // "A műalkotás értéke mindössze 9 988 776,65 HUF"
    }
}
