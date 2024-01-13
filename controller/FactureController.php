<?php

class FactureController {

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
        $this->twig = $twig;
    }

    function facture() {

        // On génère la facture
        $pdf = new PDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->custom();
        $pdf->SetFont('Arial','B',15);
        $pdf->Cell(80);
        $pdf->Cell(30,10,'Voici les consignes pour le paiement par cheque',0,0,'C');

        $pdf->Ln(20);
        $pdf->SetFont('Arial','',12);

        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Merci beaucoup pour votre achat sur IsiVeb4Shop.', 0, 1, 'C');

        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',15);

        $pdf->Ln(20);
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(30,10,'Veuillez envoyer votre cheque a l\'adresse suivante : ',0,0,'L');
        $pdf->Ln(10);
        $pdf->Cell(30,10,'IsiWeb4Shop',0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10,'18C rue de la Doua',0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10,'69100 Villeurbanne',0,0,'L');
        $pdf->Ln(10);
        $pdf->Cell(30,10,'Merci de bien mettre IsiWeb4Shop en tant qu\'ordre sur le cheque.',0,0,'L');
        $pdf->Ln(10);
        $pdf->Cell(30,10,'Votre commande sera expediee des qu\'elle sera acceptee et que le cheque sera recu.',0,0,'L');
        $pdf->Ln(10);
        $pdf->Cell(30,10,'Si vous avez des questions, n\'hesitez pas a nous contacter.',0,0,'L');
        $pdf->Ln(10);
        $pdf->Cell(30,10,'Cordialement,',0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10,'L\'equipe IsiWeb4Shop',0,0,'L');

        $pdf->Output();
        exit();
    }
}
