<?php
session_start();
require ('Pdf/fpdf186/fpdf.php');
require_once 'model/model.php';
class PDF extends FPDF
{
// Chargement des données
// Tableau simple
    function BasicTable($header, $data)
    {
        // En-tête
        foreach($header as $col)
            $this->Cell(65,7,$col,1);
        $this->Ln();
        // Données
        foreach($data as $row)
        {
            foreach($row as $col)
                $this->Cell(65,6,$col,1);
            $this->Ln();
        }
    }

// Tableau amélioré
    function ImprovedTable($header, $data)
    {
        // Largeurs des colonnes
        $w = array(40, 35, 45, 40);
        // En-tête
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C');
        $this->Ln();
        // Données
        foreach($data as $row)
        {
            $this->Cell($w[0],6,$row[0],'LR');
            $this->Cell($w[1],6,$row[1],'LR');
            $this->Cell($w[2],6,number_format($row[2],0,',',' '),'LR',0,'R');
            $this->Cell($w[3],6,number_format($row[3],0,',',' '),'LR',0,'R');
            $this->Ln();
        }
        // Trait de terminaison
        $this->Cell(array_sum($w),0,'','T');
    }
// Tableau coloré
}

$undlg = new facture_model();

$deliveryAdress = $undlg->getDeliveryAddress();

$Adress='';
foreach ($deliveryAdress as $i){
    $Adress .= $i['add1']." ".$i['city']." ".$i['postcode'];
}
$order_id = $_SESSION['SESS_ORDERNUM'];
$Panier = $undlg->getPDF($_SESSION['SESS_ORDERNUM']);

$header = ['Nom','Prix','Quantite'];

$pdf = new PDF();
$fontName = 'DejaVueSerif';
$pdf->AddFont($fontName,'B','DejaVuSerifCondensed.ttf',true);
$pdf->SetFont($fontName,'B',12);

$pdf->AddPage();
if(isset($_SESSION['firstname']) && isset($_SESSION['lastname'])){
    $pdf->Cell(32,0,'Facture pour : '.$_SESSION['firstname']." ".$_SESSION['lastname']);
    $pdf->Ln(8);
}
else{
    $pdf->Cell(32,0,'Facture pour : '.$_SESSION['surname']." ".$_SESSION['forname']);
    $pdf->Ln(8);
}
$pdf->Cell(60,0,'Numero de la commande : '.$_SESSION['SESS_ORDERNUM'],0,0);
$pdf->Ln(8);
$pdf->Cell(50,0,'Adresse de livraison : '.$Adress,0,0);
$pdf->Ln(8);

$pdf->Cell(15,0,'Total :'.$_SESSION['total']." euros");
$pdf->Ln(10);

$pdf->BasicTable($header,$Panier);
$pdf->Ln(10);

$pdf->Output();

?>