<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$company = $this->session->userdata('admin');

		$this->id = $company['id'];
		$admin = $this->session->userdata('admin');
		if(empty($admin)){ 
			redirect(base_url('admin-login'));
		}
	}

	public function dashboard()
	{	
		$data['all_user'] = $this->common->getData('user',array('user_type'=>1),array('count'));
		$data['all_provider'] = $this->common->getData('user',array('user_type'=>2),array('count'));

		$today = Date('Y-m-d H:i:s');
		$where="E.event_start_date >='".$today."'";
		$data['all_event'] = $this->common->event_list_data($where,array('count'));
		
		
		$this->adminHtml('Dashboard','admin/dashboard',$data);;
	}


	public function change_status() {
		$where_name = $this->uri->segment(3);
		$where_value = $this->uri->segment(4);
		$table = $this->uri->segment(5);
		$table_field = $this->uri->segment(6);
		$field_value = $this->uri->segment(7);
		$function = $this->uri->segment(8);

        //----------------Start Change Status--------------------//

		$where = array($where_name => $where_value);
		$data = array($table_field => $field_value);
		$this->common->updateData($table, $data, $where);

        //----------------End Change Status--------------------//

		if ($table == "recharge" && $function == "recharge_list") {
			if ($field_value == 0) {
				$message = 'recharge blocked successfully';
			} else if($field_value == 1) {
				$message = 'recharge unblocked successfully';
			}
		}

		if ($table == "tag_tbl" && $function == "tag_list") {
			if ($field_value == 0) {
				$message = 'Tags blocked successfully';
			} else if($field_value == 1) {
				$message = 'Tags unblocked successfully';
			}
		}

		if ($table == "pin_top_tbl" && $function == "pin_top_list") {
			if ($field_value == 0) {
				$message = 'Pin Top blocked successfully';
			} else if($field_value == 1) {
				$message = 'Pin Top unblocked successfully';
			}
		}

		if ($table == "unique_id_tbl" && $function == "unique_id_list") {
			if ($field_value == 0) {
				$message = 'Uniquie Id blocked successfully';
			} else if($field_value == 1) {
				$message = 'Uniquie Id unblocked successfully';
			}
		}

		if ($table == "user" && $function == "userList") {
			if ($field_value == 0) {
				$message = 'User blocked successfully';
			} else if($field_value == 1) {
				$message = 'User unblocked successfully';
			}
		}


		if ($table == "user" && $function == "providerList") {
			if ($field_value == 0) {
				$message = 'Provider blocked successfully';
			} else if($field_value == 1) {
				$message = 'Provider unblocked successfully';
			}
		}

		if ($table == "category_tbl" && $function == "category_list") {
			if ($field_value == 0) {
				$message = 'Category blocked successfully';
			} else if($field_value == 1) {
				$message = 'Category unblocked successfully';
			}
		}


		if ($table == "event_tbl" && $function == "event_list") {
			if ($field_value == 0) {
				$message = 'Event blocked successfully';
			} else if($field_value == 1) {
				$message = 'Event unblocked successfully';
			}
		}


		$this->flashMsg('success',$message);
		$path = 'admin/' . $function;
		redirect($path);
	}


	public function category_list()
	{
		
		$data['category'] = $this->common->getData('category_tbl','',array('sort_by'=>'category_id','sort_direction' => 'desc'));
		$this->adminHtml('Category List','category/category-list',$data);
	}


	
	public function payment_list()
	{
		$data['payment'] = $this->common->payment_detail();
		$this->adminHtml('Payment History','payment/payment-list',$data);
	}

	public function event_list()
	{
		$data['event'] = $this->event_list_function();
		$data['link'] = 'admin/eventDetail/';
		$this->adminHtml('Event List','event/event-list',$data);
	}

	


	public function note_list()
	{
		$data['note'] = $this->note_list_function();

		

		$data['link'] = 'admin/noteDetail/';
		$this->adminHtml('Note List','note/note-list',$data);
	}


	public function event_list_function()
	{
		
			$today = Date('Y-m-d H:i:s');
			// $where="event_start_date >='".$today."'";

			// $this->common->get_record_join_two_table('event_tbl','event_join_user','id');
			// $event_list = $this->common->getData('event_tbl',$where,array('sort_by'=>'	event_start_date','sort_direction' => 'asc'));

			$where="E.event_start_date >='".$today."'";
			$event_list = $this->common->event_list_data($where);

		

		
			if(!empty($event_list))
			{	
				foreach ($event_list as $key => $value) 
				{
					

						$start = strtotime($value['event_start_date']);
						$end = strtotime($value['event_end_date']);
						$diff = ($end - $start);
						$miliseconds = $diff*1000;
						$hours = floor($diff / (60 * 60));
						$minutes = round(((($diff % 604800) % 86400) % 3600) / 60);
						$sec = round((((($diff % 604800) % 86400) % 3600) % 60));


						if($minutes!= 0)
						{
							$event_duration = $hours.'h'.' '.$minutes.'m';
						}
						else
						{
							$event_duration = $hours.'h';
						}
						

						$start_date_match = date("Y-m-d", strtotime($value['event_start_date']));

						$today = Date('Y-m-d'); 


						
						$start_date = date("D F d", strtotime($value['event_start_date']));

						if($start_date_match == $today)
						{
							$start_date = 'Today'.' - '.$start_date;
						}
						
						$accept_event_count = $this->common->getData('event_join_user',array('event_id'=>$value['id'],'status'=>1,'event_owner_status'=>0),array('count'));

						$reject_event_count = $this->common->getData('event_join_user',array('event_id'=>$value['id'],'status'=>2,'event_owner_status'=>0),array('count'));


						$pending_user_count = $this->common->getData('event_join_user',array('event_id'=>$value['id'],'status'=>0,'event_owner_status'=>0),array('count'));




						$user_info = $this->common->getData('user',array('id'=>$value['user_id']),array('single'));

						if(!empty($user_info))
						{
							if($value['payment_status'] == 1)
							{
								if(!empty($user_info['paypal_mail_id']))
								{
									$paypal_status = 1;
								}
								else
								{
									$paypal_status = 2;
								}

								if(!empty($user_info['stripe_mail_id']))
								{
									$stripe_status = 1;
								}
								else
								{
									$stripe_status = 2;
								}
							}
							else
							{
								$paypal_status = 2;
								$stripe_status = 2;
							}
						}
						else
						{
							$paypal_status = 2;
							$stripe_status = 2;
						}


						
						$accept_reject_status = $value['status'];
						

						

						
						
						$event_array[] = array('id'=>$value['id'],'user_id'=>$value['user_id'],'event_name' => $value['event_name'],'event_latitude'=>$value['event_latitude'],'event_longitude' => $value['event_longitude'],'event_venue'=>$value['event_venue'],'event_description' => $value['event_description'],'event_duration'=>$event_duration,'start_date'=>$start_date,'accept_event_count'=>$accept_event_count,'reject_event_count'=>$reject_event_count,"pending_user_count"=>$pending_user_count,"event_created_at"=>$value['event_created_at'],"event_start_date"=>$value['event_start_date'],"event_end_date"=>$value['event_end_date'],'paypal_status'=>$paypal_status,'stripe_status'=>$stripe_status,'accept_reject_status'=>$accept_reject_status,"visibility_status"=>$value['visibility_status'],'event_owner_status'=>$value['event_owner_status'],'event_status'=>$value['event_status']);
					}

				}	
				
				if(!empty($event_array))
				{
					return $event_array;
				}
				else
				{
					$event_array = array();
					return $event_array;
				}
		}
		



	


	public function note_list_function()
	{
		
			$event_list = $this->common->getData('note_tbl','',array('sort_by'=>'id','sort_direction' => 'desc'));

			if(!empty($event_list))
			{
				$today = Date('Y-m-d H:i:s'); 
				foreach ($event_list as $key => $value) 
				{
					if($value['event_start_date'] > $today)
					{
						if(!empty($value['event_image']))
						{
							$image = base_url('/assets/event/'.$value['event_image']);
						}
						else
						{
							$image ="";
						}
						
						$date_note = date("F m", strtotime($value['event_start_date']));
						$event_start_date = date("g:i a", strtotime($value['event_start_date']));

						$event_end_date = date("g:i a", strtotime($value['event_end_date']));

						$time_note = $event_start_date.' - '.$event_end_date;

						$userdinfo = $this->common->getData('user',array('id'=> $value['user_id']),array('single'));


						if(!empty($userdinfo['user_image']))
						{
							$user_image = base_url('/assets/userfile/profile/'.$userdinfo['user_image']);
						}
						else
						{
							$user_image='';
						}
						
						$note_array[] = array('id'=>$value['id'],'user_id'=>$value['user_id'],'image' => $image,'event_name'=>$value['event_name'],'venue'=>$value['venue'],'venue_lat'=>$value['venue_lat'],'venue_lng'=>$value['venue_lng'],'description'=>$value['description'],'status'=>$value['status'],"first_name"=>$userdinfo['first_name'],"last_name"=>$userdinfo['last_name'],'date_note'=>$date_note,'time_note'=>$time_note,'user_image'=>$user_image);

					}
				}	
				
				if(!empty($note_array))
				{
					return $note_array;
				}
				else
				{
					$note_array = array();
					return $note_array;
				}

			}
			else
			{
				$note_array = array();
				return $note_array;
			}

	}


	public function event_detail()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
		{
			$event_id = $_REQUEST['event_id'];
			$where="id ='".$event_id."'";
			$result = $this->common->getData('event_tbl',$where,array('single'));
			
			if(!empty($result))
			{	

				$start = strtotime($result['event_start_date']);
				$end = strtotime($result['event_end_date']);
				$diff = ($end - $start);
				$miliseconds = $diff*1000;
				$hours = floor($diff / (60 * 60));
				$minutes = round(((($diff % 604800) % 86400) % 3600) / 60);
				$sec = round((((($diff % 604800) % 86400) % 3600) % 60));

				if($minutes!= 0)
				{
					$event_duration = $hours.'h'.' '.$minutes.'m';
				}
				else
				{
					$event_duration = $hours.'h';
				}


				$start_date_match = date("Y-m-d", strtotime($result['event_start_date']));
				$today = Date('Y-m-d'); 


				$start_date = date("D F d", strtotime($result['event_start_date']));

				if($start_date_match == $today)
				{
					$start_date = 'Today'.' - '.$start_date;
				}

				$accept_event_count = $this->common->getData('accept_reject_tbl',array('event_id'=>$result['id'],'status'=>1),array('count'));

				$reject_event_count = $this->common->getData('accept_reject_tbl',array('event_id'=>$result['id'],'status'=>2),array('count'));


				$total_event_count = $this->common->getData('accept_reject_tbl',array('event_id'=>$result['id']),array('count'));


				$user_info = $this->common->getData('user',array('id'=>$result['user_id']),array('single'));

				if(!empty($user_info))
				{
					if($result['payment_status'] == 1)
					{
						if(!empty($user_info['paypal_mail_id']))
						{
							$paypal_status = 1;
						}
						else
						{
							$paypal_status = 2;
						}

						if(!empty($user_info['stripe_mail_id']))
						{
							$stripe_status = 1;
						}
						else
						{
							$stripe_status = 2;
						}
					}
					else
					{
						$paypal_status = 2;
						$stripe_status = 2;
					}
				}
				else
				{
					$paypal_status = 2;
					$stripe_status = 2;
				}


				$user_accept_reject_result = $this->common->getData('accept_reject_tbl',array('user_id'=>$_REQUEST['user_id'],'event_id'=>$result['id'] ),array('single'));
				if(!empty($user_accept_reject_result))
				{
					$accept_reject_status = $user_accept_reject_result['status'];
				}
				else
				{
					$accept_reject_status = 0;
				}

				$where_pending ="id	!='" . $result['user_id'] . "' AND user_type =1 ";
				$total_user_count = $this->common->getData('user',$where_pending,array('count'));


				$pending_user_count = $total_user_count - $total_event_count;

				$event_arry = array('id'=>$result['id'],'user_id'=>$result['user_id'],'event_name' => $result['event_name'],'event_latitude'=>$result['event_latitude'],'event_longitude' => $result['event_longitude'],'event_venue'=>$result['event_venue'],'event_description' => $result['event_description'],'event_duration'=>$event_duration,'start_date'=>$start_date,'accept_event_count'=>$accept_event_count,'reject_event_count'=>$reject_event_count,"pending_user_count"=>$pending_user_count,"event_created_at"=>$result['event_created_at'],"event_start_date"=>$result['event_start_date'],"event_end_date"=>$result['event_end_date'],'paypal_status'=>$paypal_status,'stripe_status'=>$stripe_status,'accept_reject_status'=>$accept_reject_status,"visibility_status"=>$result['visibility_status']);

				
				
				$this->response(true,"Event list fetch Successfully.",array("event_info" => $event_arry));
				

			}
			else
			{
				$this->response(true,"No Events Found",array("event_info" =>array()));
			}
		}
		else
		{
			$this->response(false,"Missing parameter");
		}
	}
	
	public function addCategory()
	{
		$this->form_validation->set_rules('category_name','Category','required');
		
		if($this->form_validation->run() == false)
		{
			$this->adminHtml('Add Category','add-category');
		}
		else
		{	
			$image = $this->common->do_upload('image','./assets/category');		
			
			if (isset($image['upload_data'])) 
			{
				$image = $image['upload_data']['file_name'];
				$data['category_image']=$image;
			}
			else
			{
				$this->flashMsg('danger','File formate are Not Supported');
				redirect(base_url('admin/category_list'));
			}
			
			
			$data['category_name'] = $this->input->post('category_name');
			$result = $this->common->insertData('category_tbl',$data);

			
			if($result){
				$this->flashMsg('success','Category added successfully');
				redirect(base_url('admin/category_list'));
			}else{
				$this->flashMsg('danger','Some error occured. Please try again');
				redirect(base_url('admin/category_list'));
			}
		}
	}


	
	public function editCategory()
	{
		$id = $this->uri->segment(3);
		$this->form_validation->set_rules('category_name','Category','required');
		if($this->form_validation->run() == false){			
			$data['category'] = $this->common->getData('category_tbl',array('category_id' => $id), array('single'));
			$this->adminHtml('Update Category','add-category',$data);
		}else{
			if(!empty($_FILES['image']['name']))
			{


				$image = $this->common->do_upload('image','./assets/category');

				if (isset($image['upload_data'])) {
					$image = $image['upload_data']['file_name'];
					$data['category_image']=$image;
				}
				else
				{
					$this->flashMsg('danger','File formate are Not Supported');
					redirect(base_url('admin/category_list'));
				}

			}
			else
			{
				


				$id = $this->input->post('id');
				$category_detail = $this->common->getData('category_tbl',array('category_id' => $id), array('single'));

				$data['category_image']= $category_detail['category_image'];

			}
			
			$data['category_name'] = $this->input->post('category_name');
			$id = $this->input->post('id');

			$result = $this->common->updateData('category_tbl',$data,array('category_id'=>$id));
			
			if($result){
				$this->flashMsg('success','Category update successfully');
			}else{
				$this->flashMsg('danger','Some Error occured.');
			} 			
			redirect(base_url('admin/category_list'),'refresh');
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		$this->session->set_flashdata('msg','Logged out successfully');
		redirect(base_url('admin-login'));
	}


	public function userDetail($id)
	{
		$data['user'] = $this->common->getData('user',array('id' => $id), array('single'));
		$this->adminHtml('User Detail','user/user-detail',$data);
	}

	public function providerDetail($id)
	{
		$data['user'] = $this->common->getData('user',array('id' => $id), array('single'));
		$this->adminHtml('Provider Detail','provider/provider-detail',$data);
	}

	public function eventDetail($id)
	{
		$data['event_detail'] = $this->event_detail_function($id);
		
		$this->adminHtml('Event Detail','event/event-detail',$data);
	}


	public function event_detail_function($event_id)
	{
		
			// event join manage





		$where="id ='".$event_id."'";
		$result = $this->common->getData('event_tbl',$where,array('single'));

		if(!empty($result))
		{	
			
			$start = strtotime($result['event_start_date']);
			$end = strtotime($result['event_end_date']);
			$diff = ($end - $start);
			$miliseconds = $diff*1000;
			$hours = floor($diff / (60 * 60));
			$minutes = round(((($diff % 604800) % 86400) % 3600) / 60);
			$sec = round((((($diff % 604800) % 86400) % 3600) % 60));
			if($minutes!= 0)
			{
				$event_duration = $hours.'h'.' '.$minutes.'m';
			}
			else
			{
				$event_duration = $hours.'h';
			}

			$start_date_match = date("Y-m-d", strtotime($result['event_start_date']));
			$today = Date('Y-m-d'); 

			$start_date = date("D F d", strtotime($result['event_start_date']));
			if($start_date_match == $today)
			{
				$start_date = 'Today'.' - '.$start_date;
			}

			$accept_event_count = $this->common->getData('event_join_user',array('event_id'=>$result['id'],'status'=>1,'event_owner_status'=>0),array('count'));
			$reject_event_count = $this->common->getData('event_join_user',array('event_id'=>$result['id'],'status'=>2,'event_owner_status'=>0),array('count'));
			$pending_user_count = $this->common->getData('event_join_user',array('event_id'=>$result['id'],'status'=>0,'event_owner_status'=>0),array('count'));
			$user_info = $this->common->getData('user',array('id'=>$result['user_id']),array('single'));
			if(!empty($user_info))
			{
				if($result['payment_status'] == 1)
				{
					if(!empty($user_info['paypal_mail_id']))
					{
						$paypal_status = 1;
					}
					else
					{
						$paypal_status = 2;
					}
					if(!empty($user_info['stripe_mail_id']))
					{
						$stripe_status = 1;
					}
					else
					{
						$stripe_status = 2;
					}
				}
				else
				{
					$paypal_status = 2;
					$stripe_status = 2;
				}
			}
			else
			{
				$paypal_status = 2;
				$stripe_status = 2;
			}




			$event_arry = array('id'=>$result['id'],'user_id'=>$result['user_id'],'event_name' => $result['event_name'],'event_latitude'=>$result['event_latitude'],'event_longitude' => $result['event_longitude'],'event_venue'=>$result['event_venue'],'event_description' => $result['event_description'],'event_duration'=>$event_duration,'start_date'=>$start_date,'accept_event_count'=>$accept_event_count,'reject_event_count'=>$reject_event_count,"pending_user_count"=>$pending_user_count,"event_created_at"=>$result['event_created_at'],"event_start_date"=>$result['event_start_date'],"event_end_date"=>$result['event_end_date'],'paypal_status'=>$paypal_status,'stripe_status'=>$stripe_status,"visibility_status"=>$result['visibility_status']);
			


			return $event_arry;

		}
		else
		{
			$event_info = array();
			return $event_arry;

		}
		
	}

	public function UserList()
	{
		$data['user'] = $this->common->getData('user',array('user_type'=>'1'),array('sort_by'=>'id','sort_direction' => 'desc'));
		$data['link'] = 'admin/userDetail/';
		$this->adminHtml('User List','user/user-list',$data);
	}

	public function providerList()
	{
		$data['user'] = $this->common->getData('user',array('user_type'=>'2'),array('sort_by'=>'id','sort_direction' => 'desc'));
		$data['link'] = 'admin/providerDetail/';
		$this->adminHtml('Provider List','provider/provider-list',$data);
	}


	
}
