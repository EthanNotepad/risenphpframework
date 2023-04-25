<?php

namespace src\fpdf;

use src\fpdf\Core\FPDF;

class Rpdf extends FPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $margin = 10)
    {
        parent::__construct($orientation, $unit, $format);
        $this->SetTopMargin($margin);
        $this->SetLeftMargin($margin);
        $this->SetRightMargin($margin);
        $this->SetAutoPageBreak(true, $margin);
    }

    public function make($data, $resourceFile, $isNew = true)
    {
        if ($isNew) {
            // create a new pdf file
        } else {
            // import a pdf file, and edit it
            // install FPDI first, url: https://www.setasign.com/products/fpdi/about/;
        }
    }

    public function makePicture($resourceFile, $isNew = true)
    {
        if ($isNew) {
            if (self::processImage($resourceFile)) {
                $this->AliasNbPages();
                $this->AddPage();

                $this->SetFont('Times', '', 12);
                // Get the dimensions of the image
                list($imageWidth, $imageHeight) = getimagesize($resourceFile);

                // Calculate the appropriate size to fit on the page
                $maxWidth = $this->GetPageWidth() - $this->lMargin - $this->rMargin;
                $maxHeight = $this->GetPageHeight() - $this->tMargin - $this->bMargin;
                $ratio = min($maxWidth / $imageWidth, $maxHeight / $imageHeight);
                $imageWidth *= $ratio;
                $imageHeight *= $ratio;

                // Place the image on the page
                $this->Image($resourceFile, $this->lMargin, $this->tMargin, $imageWidth, $imageHeight);

                return $this;
            } else {
                return $this;
            }
        } else {
            // import a pdf file, and edit it
            // install FPDI first, url: https://www.setasign.com/products/fpdi/about/;
        }
    }

    public static function processImage($resourceFile)
    {
        if (file_exists($resourceFile) && getimagesize($resourceFile)) {
            return $resourceFile;
        } else {
            return false;
        }
    }
}
