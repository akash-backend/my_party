<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paypal extends CI_Controller{

    private $sandBox =  0; // 0-Sandbox / 1-Live
    private $pem_Dev ='/home3/ctinf0eg/public_html/CT06/sport/assets/CertificatesApns13July.pem';
    private $pem_Pro ='/home3/ctinf0eg/public_html/CT06/sport/assets/CertificatesApns13July.pem';
    private $passPhrase = '123456';

    
    
     function  __construct(){
        parent::__construct();
        
        // Load paypal library & product model
        $this->load->library('paypal_lib');
        $this->load->model('product');
        $this->load->model('common');
     }
     
    function success()
    {
        // Pass the transaction data to view
        $this->load->view('products/success');
    }
     
     function cancel(){
        // Load payment failed view
        $this->load->view('products/cancel');
     }


     function push_iOS($token, $msg, $alert) 
      {
        if (!empty($this->pem_Pro) && !empty($this->passPhrase)) 
        {
            
            if (!empty($this->sandBox))
            {
                $tHost = 'gateway.push.apple.com';
                $tCert = $this->pem_Pro;
            }
            else
            {
                $tHost = 'gateway.sandbox.push.apple.com';
                $tCert = $this->pem_Dev;
            }
            
            $tPort = 2195;
            
            // Provide the Private Key Passphrase
            $tPassphrase = $this->passPhrase;

            // Provide the Device Identifier (Ensure that the Identifier does not have spaces in it).
            $tToken = $token;

            // The message that is to appear on the dialog.
            $tAlert = $alert;

            // Audible Notification Option.
            $tSound = 'default';

            // The content that is returned by the LiveCode "pushNotificationReceived" message.

            $tPayload = 'Notification sent';

            // Create the message content that is to be sent to the device.

            $tBody['aps'] = array(
                'alert' => $tAlert,
                'msg' => $msg,
                'sound' => $tSound,
            );

            $tBody ['payload'] = $tPayload;

            // Encode the body to JSON.
            $tBody = json_encode($tBody);

            
            // Create the Socket Stream.

            $tContext = stream_context_create();
            stream_context_set_option($tContext, 'ssl', 'local_cert', 'my_party.pem');
            stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);

            // Open the Connection to the APNS Server.

            $tSocket = stream_socket_client('ssl://' . $tHost . ':' . $tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $tContext);

            // Check if we were able to open a socket.

            if (!$tSocket)
                exit("APNS Connection Failed: $error $errstr" . PHP_EOL);

            // Build the Binary Notification.

            $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', $tToken) . pack('n', strlen($tBody)) . $tBody;

            // Send the Notification to the Server.

                $tResult = fwrite($tSocket, $tMsg, strlen($tMsg));

        //       if (!$tResult){
        // echo 'Message not delivered' . PHP_EOL;
        //  }
        
        // else{
        //      echo 'Message successfully delivered' . PHP_EOL;
        //  }

        // die();


           //  if ($tResult)
           //  echo 'Delivered Message to APNS' . PHP_EOL;
           // else
           // echo 'Could not Deliver Message to APNS' . PHP_EOL;
           // // Close the Connection to the Server.
           // die();

            fclose($tSocket);
        }
    }


     
     function ipn()
     {
        $paypalInfo = $_REQUEST;
        file_put_contents("test.txt", $paypalInfo);
        if(!empty($paypalInfo))
        {
            $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);

            // Check whether the transaction is valid
            if($ipnCheck)
            {
                $data['user_id']         = $paypalInfo["custom"];
                $data['event_id']        = $paypalInfo["item_number"];
                $data['txn_id']          = $paypalInfo["txn_id"];
                $data['payment_gross']   = $paypalInfo["mc_gross"];
                $data['currency_code']   = $paypalInfo["mc_currency"];
                $data['payer_email']     = $paypalInfo["payer_email"];
                $data['payment_status']  = $paypalInfo["payment_status"];

                $this->product->insertTransaction($data);

                //send notification

                $today = Date('Y-m-d H:i:s'); 
                $insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $data['user_id'],'message' => "gift send successfully",'user_send_from'=>$data['user_id'],'date'=>$today,'type'=>5));

                $data_user = $this->common->getData('user',array('id'=>$data['user_id']),array('single'));

                $sendmsg =  array('title'=>"gift send",'body'=>"gift send successfully");
                $notification = array('user_id' => $data['user_id'],'message' => "gift send successfully",'user_send_from'=>$data['user_id'],'date'=>$today,'type'=>5);

                if($data_user['ios_token'] != ""){
                    $isSend = $this->push_iOS($data_user['ios_token'],$notification,$sendmsg);
                }
                else if($data_user['android_token'] != "")
                {
                    $registatoin_id = array($user_data["android_token"]); 
                    $this->send_notification($registatoin_id, $sendmsg);
                }
                                
                            
            }
        }
    }
}