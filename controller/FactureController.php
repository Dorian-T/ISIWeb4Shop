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


        // On récupère les informations de la commande
        $cartItems = $factureModel->getOrderItems($order['id']);
        $address = $factureModel->getAddressById($order['delivery_add_id']);

        // On génère la facture
        $pdf = new PDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->custom();
        $pdf->SetFont('Arial','B',15);
        $pdf->Cell(80);
        $pdf->Cell(30,10,'Commande ' . $order['id'] . ' - Facture',0,0,'C');

        $pdf->Ln(20);
        $pdf->SetFont('Arial','',12);

        $pdf->Cell(30,10,'Date : ' . $order['date'],0,0,'L');

        $pdf->Ln(10);
        $pdf->Cell(30,10,$address['firstname'] . ' ' . $address['lastname'],0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10,'TEL : ' . $address['phone'],0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10, 'MAIL : ' . $address['email'],0,0,'L');
        $pdf->Ln(5);
        $pdf->Cell(30,10,$address['add1']. ' '. $address['add2'],0,0,'L');

        $pdf->Ln(5);
        $pdf->Cell(30,10,$address['postcode'] . ' ' . $address['city'],0,0,'L');

        $pdf->Ln(10);

        $pdf->Cell(30,10,'Moyen de paiement : ' . $order['payment_type'],0,0,'L');

        $pdf->Ln(15);
        $pdf->Cell(30,10,'Produits : ',0,0,'L');


        $pdf->Ln(8);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(70,10,'Nom',0,0,'L');
        $pdf->Cell(30,10,'Quantite',0,0,'L');
        $pdf->Cell(30,10,'Prix',0,0,'L');
        $pdf->Cell(30,10,'Total',0,0,'L');

        $pdf->Ln(10);

        $pdf->SetFont('Arial','',12);

        $total = 0;

        foreach ($cartItems as $cartItem) {
            $product = getProductById($cartItem['id']);
            $pdf->Cell(70,10,$product['name'],0,0,'L');
            $pdf->Cell(30,10,$cartItem['quantity'],0,0,'L');
            $pdf->Cell(30,10,$product['price'] . ' EUR',0,0,'L');
            $pdf->Cell(30,10,$product['price'] * $cartItem['quantity'] . ' EUR',0,0,'L');
            $pdf->Ln(10);
            $total += $product['price'] * $cartItem['quantity'];
        }

        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',15);


        $pdf->Cell(30,10,'Total : ' . $total . ' EUR',0,0,'L');

        // On affiche les informations de paiement si le paiement est par chèque
        if ($order['payment_type'] == "cheque") {
            $pdf->Ln(20);
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(30,10,'Veuillez envoyer votre cheque a l\'adresse suivante : ',0,0,'L');
            $pdf->Ln(10);
            $pdf->Cell(30,10,'IsiWeb4Shop',0,0,'L');
            $pdf->Ln(5);
            $pdf->Cell(30,10,'18C rue de la Doua',0,0,'L');
            $pdf->Ln(5);
            $pdf->Cell(30,10,'69100 Villeurbanne',0,0,'L');

        }

        $pdf->Output();
    }
}