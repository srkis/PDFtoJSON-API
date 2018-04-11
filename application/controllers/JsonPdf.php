<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class JsonPdf extends CI_Controller {

    public function index()
    {
        $data['title'] = 'Bens Consulting';
        $data['description'] = 'Click on button "UPLOAD PDF" to upload PDF file. After that "PREVIEW button will appear.
         If you click on that button, on the right side, you will see uploaded PDF file. If everything is "OK", click "CONVERT TO JSON" or "CONVERT TO TEXT" to convert PDF file. ';
        $data['footer_text'] = 'Copyright &copy; Srdjan Stojanovic 2018';

        $this->load->view('pdf_view', $data);
    }


    public function pdf_upload()
    {
        if(isset($_FILES['pdf']['name'])){

            $pdfName = $_FILES['pdf']['name'];

            $pdfName = preg_replace('/\s+/', '_', $pdfName);

            $config['upload_path'] = UPLOAD_PATH;
            $config['allowed_types'] = 'pdf';
           // $config['remove_spaces'] = FALSE;
            $this->load->library('upload',$config);
            if(!$this->upload->do_upload('pdf'))
            {
                echo $this->upload->display_errors();

            }else{

                $data = $this->upload->data();

                // Call only if $_POST array contain json action
                if(isset($_POST['action']) && $_POST['action'] == 'json'){
                    $this->_callApi($pdfName);

                    //otherwise we parse PDF to text
                }else{

                    $this->_parsToText($pdfName);
                }

            }

        }

    }


    protected function _callApi($pdfName)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "isOverlayRequired=true&url=http://bens-consulting.icodes.rocks/upload/".$pdfName."&language=eng");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "apikey:".API_KEY;
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);

        header("Content-type: application/json");
        echo  $result;

    }

    protected function _parsToText($pdfName)
    {

        require FCPATH.'vendor/autoload.php';
        $uploadPath = 'upload/'.$pdfName;
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($uploadPath);

        // Loop over each property to extract details of PDF Document (Creator,CreationDate,Pages..)
               $details  = $pdf->getDetails();
                foreach ($details as $property => $value) {
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    echo $property . ' => ' . $value . "\n";
                }

        /*$pages = $pdf->getPages();

           foreach ($pages as $page) {
            echo $page->getText();
           }*/
    }
}