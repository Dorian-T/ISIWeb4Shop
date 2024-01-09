<?php

class FactureController {

    /**
     * The user model instance.
     */
    private $userModel;

    /**
     * The Twig environment used for rendering templates.
     */
    private $twig;
    
    /**
     * AdminController constructor.
     *
     * @param $twig
     */
    public function __construct($twig) {
        $this->userModel = new UserModel();
        $this->twig = $twig;
    }

    // Génère la facture d'une commande
    function facture($orderId) {

        $factureModel = new FactureModel();
        $order = $factureModel->getOrderById($orderId);
        $cartItems = $factureModel->getOrderItems($order['id']);

        // On génère la facture
        $pdf = new PDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->custom();
        $pdf->SetFont('Arial','B',15);
        $pdf->Cell(80);
        $pdf->Cell(30,10,'Commande ' . $order['id'] . ' - Facture',0,0,'C');

        $pdf->Ln(20);
        $pdf->SetFont('Arial','',12);

        // Afficher les instructions pour envoyer le chèque
        $pdf->Cell(30,10,'Veuillez envoyer votre chèque à l\'adresse suivante : ',0,0,'L');
        $pdf->Ln(10);
        $pdf->Cell(30,10,'IsiWeb4Shop',0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10,'18C rue de la Doua',0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10,'69100 Villeurbanne',0,0,'L');

        $pdf->Output();
    }
}
?>
