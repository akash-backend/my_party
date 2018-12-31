<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller{
    
    function  __construct(){
        parent::__construct();
        
        // Load paypal library & product model
        $this->load->library('paypal_lib');
        $this->load->model('product');
    }
    
    function index(){
        $data = array();
        
        // Get products data from the database
        $data['products'] = $this->product->getRows();
        
        // Pass products data to the view
        $this->load->view('products/index', $data);
    }
    
    function buy(){

        if(!empty($_REQUEST['price']) && !empty($_REQUEST['event_id']) && !empty($_REQUEST['user_id']))
        {
            // Set variables for paypal form
            $returnURL = base_url().'paypal/success';
            $cancelURL = base_url().'paypal/cancel';
            $notifyURL = base_url().'paypal/ipn';
            
            // Get product data from the database
            

            $user_info = $this->common->getData('user',array('id' => $_REQUEST['user_id']),array('single'));

            $where = "event_tbl.id = '".$_REQUEST['event_id']."'";
            $event_detail = $this->common->get_record_join_two_table('event_tbl','user','   user_id','id','user.id as provider_id,user.paypal_mail_id,event_tbl.event_name',$where,'',array('single'));

            
            $business_email = $event_detail['paypal_mail_id'];
            $business_email = preg_replace('/\s+/', '', $business_email);
            
            $event_name = $event_detail['event_name'];
            $provider_id = $event_detail['provider_id'];
            
            // changes code
            
            // Add fields to paypal form
            $this->paypal_lib->add_field('return', $returnURL);
            $this->paypal_lib->add_field('cancel_return', $cancelURL);
            $this->paypal_lib->add_field('notify_url', $notifyURL);
            $this->paypal_lib->add_field('item_name', $event_name);
            $this->paypal_lib->add_field('custom', $_REQUEST['user_id']);
            $this->paypal_lib->add_field('item_number', $_REQUEST['event_id']);
            $this->paypal_lib->add_field('amount',  $_REQUEST['price']);
            $this->paypal_lib->add_field('business',$business_email);
            
            // Render paypal form
            $this->paypal_lib->paypal_auto_form();
        }
        else
        {
            echo"hello";
        }
    }
}