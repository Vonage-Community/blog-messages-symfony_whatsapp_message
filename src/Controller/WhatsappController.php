<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Vonage\Client;
use Vonage\Client\Credentials\Keypair;
use Vonage\Messages\Channel\WhatsApp\WhatsAppText;

class WhatsappController extends AbstractController
{
    #[Route('/whatsapp', name: 'app_whatsapp_form', methods: ['GET'])]
    public function form(): Response
    {
        return $this->render('whatsapp/form.html.twig', ['sent' => false]);
    }

    #[Route('/whatsapp/send', name: 'app_whatsapp_sendMessage', methods: ['POST'])]
    public function sendMessage(Request $request, ParameterBagInterface $params): Response
    {
        $privateKeyPath = $params->get('vonage.privateKeyPath');
        $projectDir = $this->getParameter('kernel.project_dir');
        $resolvedPath = $projectDir . '/' . ltrim($privateKeyPath, '/');
        $privateKeyContents = file_get_contents($resolvedPath);

        $keypair = new Keypair(
            $privateKeyContents,
            $params->get('vonage.applicationId')
        );

        $vonage = new Client($keypair, ['base_api_url' => 'https://messages-sandbox.nexmo.com']);
        
        $message = new WhatsAppText(
            $request->request->get('to'),
            $params->get('vonage.from'),
            $request->request->get('message')
        );
        
        $result = $vonage->messages()->send($message);
        dd($result);

        return $this->render('whatsapp/form.html.twig', ['sent' => true]);
    }
}


