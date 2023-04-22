<?php

/**
 * ------------------------------------------------------------
 * How to use, you can refer to the following test
 * ------------------------------------------------------------
 * 1.Add route:
 *  Router::any('/src/pdf/test', 'src\fpdf\Test@test');
 * 2. Access URI: 
 *  /src/pdf/test
 */

namespace src\fpdf;

class Test
{
    public function test()
    {
        $imagePath = PROJECT_ROOT_PATH . '/Tests/871475.jpeg';
        $nowDateName = date('Ymd');
        // open pdf on browser
        $PDFGenerator = (new Rpdf)->makePicture($imagePath)->Output('I', 'draw_' . $nowDateName . '.pdf');
        // download pdf
        // $PDFGenerator = (new \src\PDFGenerator\PDFGenerator())->makePicture($imagePath)->Output('D', 'draw_' . $nowDateName . '.pdf');
    }
}
