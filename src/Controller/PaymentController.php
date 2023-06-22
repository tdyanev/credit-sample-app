<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\CreditRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile/payment')]
class PaymentController extends AbstractController
{
    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository): Response
    {
        return $this->render('payment/index.html.twig', [
            'payments' => $paymentRepository->findBy([
                'owner' => $this->getUser(),
            ], [
                'id' => 'DESC',
            ]),
        ]);
    }

    #[Route('/new/{credit_id}', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PaymentRepository $paymentRepository, CreditRepository $cr, int $credit_id = 0): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment, [
            'credit_id' => $credit_id,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($credit_id) {
                $payment->setCredit($cr->find($credit_id));
            }

            $payment->setDate(new \DateTimeImmutable('now'));
            $payment->setOwner($this->getUser());

            $amountLeft = $payment->getCredit()->getAmountLeft() - $payment->getAmount();

            //dd($amountLeft);

            if ($amountLeft <= 0) {
                $credit = $payment->getCredit();

                $credit->setFinishedAt(new \DateTimeImmutable('now'));
                $cr->save($credit, true);

                $message = 'This credit is done!';

                if ($amountLeft < 0) {
                    $message .= sprintf(' You overpaid and %d was returned to your account', abs($amountLeft));
                    $payment->setAmount($payment->getAmount() + $amountLeft);
                }

                $this->addFlash('success', $message);
            }

            $paymentRepository->save($payment, true);

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
            'credit_id' => $credit_id,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Payment $payment, PaymentRepository $paymentRepository): Response
    {
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentRepository->save($payment, true);

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, PaymentRepository $paymentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
            $paymentRepository->remove($payment, true);
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
