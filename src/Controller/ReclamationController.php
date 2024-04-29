<?php

namespace App\Controller;

use App\Service\SmsService;
use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Mime\Email;

class ReclamationController extends AbstractController
{
    #[Route('/listerec', name: 'app_reclamation_index_front', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }
    #[Route('back/listerec', name: 'app_reclamation_index_back', methods: ['GET'])]
    public function index2(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index2.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }


    #[Route('reclamation/sendmail/{id}', name: 'mailing', methods: ['GET'])]
    private function sendEmail(MailerInterface $mailer): void
    {
        $email = (new Email())
            ->from('kharrat.raed@esprit.tn')
            ->to('Zouari.Salma@esprit.tn')
            ->subject('Reclamation')
            ->text('Sending emails is fun again!')
            ->html('<p>Hello Salma, your project is almost done.</p><p>Thanks.</p>');

        $mailer->send($email);
    }

    
    #[Route('/reclamation', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, SmsService $smsService): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Send email notification
            $this->sendEmail($mailer);

            // Send SMS notification
            $smsRecipient = '+21695220959'; // Provide recipient's phone number
            $smsMessage = 'New reclamation created: ' . $reclamation->getMessage(); // Customize the message as needed
            $smsService->sendSms($smsRecipient, $smsMessage);

            return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/_form.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }


    #[Route('/{idReclamation}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
   

    #[Route('/{idReclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{idReclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdReclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index_front', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/reclamation/{id}/traite', name: 'reclamation_traite', methods: ['POST'])]
    public function traiteReclamation(Request $request, Reclamation $reclamation): Response
    {
        // Set the 'statut' to "traite"
        $reclamation->setStatut('traite');
        
        // Persist the changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        
        // Redirect back to the page or return a response
        return $this->redirectToRoute('app_reclamation_index_back',['idReclamation' => $reclamation->getIdReclamation()]);

    }
}

