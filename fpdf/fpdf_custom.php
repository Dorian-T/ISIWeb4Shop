<?php
require('fpdf.php');

class PDF extends FPDF
{
    function custom()
    {
        // Logo
        $this->Image('assets/img/Web4ShopHeader.png',15,9,10);
        $this->SetFont('Arial','',15);

        $this->Cell(28);
        $this->Cell(30,10,'IsiWeb4Shop ',0,0,'C');
        $this->Ln(20);
        
        
        $this->SetY(265);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Cookie Lovers Inc. - 2023',0,0,'C');
        $this->Ln();
        $this->Cell(0,0,'ALL RIGHTS RESERVED',0,0,'C');

        $this->SetY(30);

    }
}

?>