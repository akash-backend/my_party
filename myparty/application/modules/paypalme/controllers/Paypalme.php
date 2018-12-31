<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypalme extends CI_Controller{
    
     function  __construct(){
        parent::__construct();
        
        // Load paypal library & product model
        $this->load->library('paypal_lib');
        $this->load->model('product');
     }
     
    public function ipn_url()
    {
        $myfile = fopen("newfile1.txt", "w") or die("Unable to open file!");
        $txt = "John Doe\n".json_encode($_REQUEST);
        $data = $_REQUEST;

        $post['PaymentStatus'] = $data['payment_status'];
        $post['PayerEmail'] = $data['payer_email'];
        $post['ReceiverEmail'] = $data['receiver_email'];
        $post['paymentFee'] = $data['payment_fee'];
        $post['PaymentAmount'] = $data['payment_gross'];
        $post['TransactionID'] = $data['txn_id'];

        $post['paymentDate']  = date("date('Y-m-d H:i:s')", strtotime($data['payment_date']));
        $post['PaymentCurrency'] = $data['mc_currency'];
       
        $result = $this->common->insertData('customerrecords',$post);      
        // fwrite($myfile, $txt);
        // fclose($myfile);
    }
}