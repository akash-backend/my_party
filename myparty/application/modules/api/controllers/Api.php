<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set("Asia/Kolkata");

class Api extends Base_Controller {

	private $sandBox =  0; // 0-Sandbox / 1-Live
    private $pem_Dev ='/home3/ctinf0eg/public_html/CT06/sport/assets/CertificatesApns13July.pem';
    private $pem_Pro ='/home3/ctinf0eg/public_html/CT06/sport/assets/CertificatesApns13July.pem';
    private $passPhrase = '123456';


	public function __construct()
	{
		parent:: __construct();
		$this->checkAuth();		
		$this->load->helper('common');
		$this->load->library('email');
		$this->load->library('m_pdf');
	}	

	public function getKey()
	{
		$result = $this->common->getFieldKey($_POST['table']);
		echo json_encode($result);
	}


	// For IOS notification
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


	// public function featured_images()
	// {
	// 	$result = $this->common->getData('featured_images');
	// 	if(!empty($result))
 //        {
 //        	foreach($result as $value)
	// 		{
	// 			if(!empty($value['featured_images']))
	// 			{
	// 				$image = base_url('/assets/fetatured_business/'.$value['featured_images']);
	// 			}
	// 			else
	// 			{
	// 				$image = "";
	// 			}


	// 			$featured_list[]=array('featured_images_id'=>$value['id'],'featured_images'=>$image);	
	// 		}

	// 		$this->response(true,"Featured Images fetch Successfully.",array("featured_list" => $featured_list));		

 //        }
 //        else
 //        {
 //        	$this->response(false,"Featured Images not found",array("featured_list" => array()));
 //        }

	// }



	public function featured_images()
	{
		$query="SELECT *  FROM   user  where  user_type	=2 ORDER BY RAND() LIMIT 0, 10";
		$result = $this->common->query($query);

		if(!empty($result))
        {
        	foreach($result as $value)
			{
				if(!empty($value['banner_image']))
				{
					$image = base_url('/assets/userfile/banner/'.$value['banner_image']);
				}
				else
				{
					$image = "";
				}


				$featured_list[] =array('id'=>$value['id'],'banner_image'=>$image,'banner_title'=>$value['banner_title']);
			}
        	
			$this->response(true,"Featured Images fetch Successfully.",array("featured_list" => $featured_list));			
				
		}
		else
		{
			$this->response(false,"Featured Images not found",array("featured_list" => array()));
		}	
	}

	public function login()
	{				
		$_POST['password'] = md5($_POST['password']);
		$result = $this->common->getData('user',array('email = ' => $_POST['email'], 'password' => $_POST['password'],'singup_type' => 1),array('single'));
		
		if($result)
		{
			if($result['status'] == 0)
			{
				$this->response(false,'Your account are not activate by admin');
				die();
			}

			
			if(!empty($result['start_time']))
			{
				$result['start_time'] = date("h:i a", strtotime($result['start_time']));
			}


			if(!empty($result['end_time']))
			{
				$result['end_time'] = date("h:i a", strtotime($result['end_time']));
			}
			

			if(!empty($result['user_image']))
			{
				$result['user_image'] = base_url('/assets/userfile/profile/'.$result['user_image']);
			}
			else
			{
				$result['user_image'] = "";
			}

			if(!empty($result['banner_image']))
			{
				$result['banner_image'] = base_url('/assets/userfile/banner/'.$result['banner_image']);
			}
			else
			{
				$result['banner_image']='';
			}

			if(isset($_POST['android_token']))
			{
				$old_device = $this->common->getData('user',array('android_token' => $_POST['android_token']),array('single','field'=>'id'));	
			}		

			if (isset($_POST['ios_token'])) 
			{
				$old_device = $this->common->getData('user',array('ios_token' => $_POST['ios_token']),array('single','field'=>'id'));	
			}

			if($old_device)
			{
				$this->common->updateData('user',array('android_token' => "", "ios_token" => ""),array('id' => $old_device['id']));
			}
			
			$this->common->updateData('user',array('ios_token' =>$_POST['ios_token'], 'android_token' => $_POST['android_token']), array('id' => $result['id']));
			unset($result['password']);

			$count_follow_user = $this->common->getData('user_following',array('user_id'=>$result['id']),array('count'));
        	

			$result['count_follow_user'] = $count_follow_user;

			if(!empty($result['category']))
				{
					$category_info  = $this->common->getData('category_tbl',array('category_id' => $result['category']),array('single'));
					$result['category_name'] = $category_info['category_name'];
				}
				else
				{
					$result['category_name'] = "";
				}

				if(!empty($_REQUEST['provider_id']))
				{
					$result['provider_id'] = $_REQUEST['provider_id'];
				}
				else
				{
					$result['provider_id'] = "";
				}	

				$this->response(true,'Successfully Login',array("userinfo" => $result));	
		}
		else
		{
			$message = " wrong Email or Password";			
			$this->response(false,$message,array("userinfo" => ""));
		}
	}


	public function email_exist()
	{
		if(!empty($_REQUEST['email']))
		{

			$exist = $this->common->getData('user',array('email' => $_POST['email'],'singup_type' => 1),array('single'));

			if(empty($exist['status'] == 1))
			{
				$response = $this->response(true,"Email available");
				die();				
			}
			else
			{
				$response = $this->response(false,"Email is already exists");
				die();	
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}


	


	public function event_delete()
	{
		if(!empty($_REQUEST['event_id']))
		{
			$where="id	='" .$_REQUEST['event_id'] . "'";
			$value = $this->common->deleteData('event_tbl',$where);
			$this->response(true,"event deleted");
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}


	public function searchItem()
	{
		
		if(!empty($_REQUEST['user_latitude']) && !empty($_REQUEST['user_longitude']) && !empty($_REQUEST['name']) && !empty($_REQUEST['user_id']) && !empty($_REQUEST['type']))
		{
			$name = $_REQUEST['name'];
			
			if($_REQUEST['type'] == 1)
			{
				
				$user_latitude = $_REQUEST['user_latitude'];
				$user_longitude = $_REQUEST['user_longitude'];
				$user_id = $_REQUEST['user_id'];

				$where = "user_type = 2 AND status=1";
				$result = $this->common->get_user_by_lat($where,$user_latitude,$user_longitude);

				if(!empty($result))
				{     
	                 foreach ($result as $key => $value)
	                 {
	                 	if(!empty($value['user_image']))
	                 	{
	                 		$image = base_url('/assets/userfile/profile/'.$value['user_image']);
	                 	}
	                 	else
	                 	{
	                 		$image = '';
	                 	}


	                 	$where_follow="user_id ='" . $_REQUEST['user_id'] . "' AND following_id = '" . $value['id'] . "' ";
						$follow_status = $this->common->getData('user_following',$where_follow,array('single'));

						if($follow_status)
						{
							$follow_status = 1;
						}
						else
						{
							$follow_status = 2;
						}

						
						$array_data[]=array('id'=>$value['id'],'first_name'=>$value['first_name'],'last_name'=>$value['last_name'],'image'=>$image,'distance'=>$value['distance'],'follow_status'=>$follow_status,'business_name'=>$value['business_name']);

						
					 }

					
				}
				else
				{
					$array_data = array();
				}
	
			}
					
			
				
			if($_REQUEST['type'] == 1)
			{
				
				$filtered = $this->multi_array_search_with_condition($array_data, array('business_name'=>strtolower($name)));
			}
			

			if(!empty($filtered))
			{
				if($_REQUEST['type'] == 1)
				{
					$this->response(true,"Provider Found Successfully.",array("user_list" => $filtered));
				}
			}
			else
			{
				$this->response(false,"Item Not Found");
			}
    	}
		else
        {
        	$this->response(false,"Missing Parameter.");
        }
	}












	function multi_array_search_with_condition($array, $condition)

	{
		

	    $foundItems = array();



	    foreach($array as $item)

	    {

	        $find = TRUE;

	        foreach($condition as $key => $value)

	        {


	        	

			   if(isset($item[$key]) && strpos(strtolower($item[$key]),$value) !== false)

	            {

	            	if (strpos(strtolower($item[$key]), $value) == 0)

	            	{
	            		
	            		array_push($foundItems, $item);

	            	}

	            	else

	            	{

	            		$find = FALSE;

	            	}

	                

	            } else {

	                $find = FALSE;

	            }

	        }


	    }

	    return $foundItems;

	}


	public function category_list()
	{
		$result = $this->common->getData('category_tbl',array('status'=>1));
		
		if(!empty($result))
        {
			foreach($result as $value)
			{
				if(!empty($value['category_image']))
				{
					$image = base_url('/assets/category/'.$value['category_image']);
				}
				else
				{
					$image = "";
				}


				$category_info[]=array('category_id'=>$value['category_id'],'category_name'=>$value['category_name'],'category_image'=>$image);	
			}


			$this->response(true,"Category fetch Successfully.",array("category_list" => $category_info));			
		}
		else
		{
			$this->response(false,"Category not found",array("category_list" => array()));
		}
	}


	public function advertisement_provider()
	{
		$query="SELECT *  FROM   user  where  user_type	=2 ORDER BY RAND()";
		$result = $this->common->query($query);

		if(!empty($result))
        {

        	
					if(!empty($result[0]['banner_image']))
					{
						$image = base_url('/assets/userfile/banner/'.$result[0]['banner_image']);
					}
					else
					{
						$image = "";
					}


					$advertisement_info =array('id'=>$result[0]['id'],'banner_image'=>$image,'banner_title'=>$result[0]['banner_title'],'business_name'=>$result[0]['business_name']);

					$this->response(true,"Advertisement fetch successfully",array("advertisement_info" => $advertisement_info));			
				
		}
		else
		{
			$this->response(false,"Advertisement not found",array("advertisement_info" => array()));
		}	
	}


	public function add_event()
	{
		$_POST['event_created_at'] = date('Y-m-d H:i:s');
		$post = $this->common->getField('event_tbl',$_POST);
		$result = $this->common->insertData('event_tbl',$post);

		if($result)
		{
			$insert_id = $this->db->insert_id();

			$today = Date('Y-m-d H:i:s');
			$join['event_owner_status'] = 1;
			$join['user_id'] = $_REQUEST['user_id'];
			$join['event_id'] = $insert_id;
			$join['created_at'] = $today;
			$result = $this->common->insertData('event_join_user',$join);

			$event = $this->common->getData('event_tbl',array('id' => $insert_id),array('single'));
			$this->response(true,"Event create successfully",array("eventinfo" => $event));					
		}
		else
		{
			$this->response(false,"There is a problem, please try again.",array("eventinfo" => ""));
		}
	}


	public function signup()
	{	
		$exist = $this->common->getData('user',array('email' => $_POST['email'],'singup_type' => 1),array('single'));

		if(!empty($exist['status'] == 1))
		{
			$response = $this->response(false,"Email is already exists");
			die();				
		}
		else
		{
			$old_device = $old_ios = false;
			
			if(!empty($_FILES['user_image']['name']))
			{
				$image = $this->common->do_upload('user_image','./assets/userfile/profile/');
				if(isset($image['upload_data']))
				{
					$_POST['user_image'] = $image['upload_data']['file_name'];

				}
			}	

			if(!empty($_FILES['banner_image']['name']))
			{
				$banner_image = $this->common->do_upload('banner_image','./assets/userfile/banner/');
				if(isset($banner_image['upload_data']))
				{
					$_POST['banner_image'] = $banner_image['upload_data']['file_name'];
				}
			}		

			if(isset($_POST['ios_token']))
			{
				$old_ios =  $this->common->getData('user',array('ios_token' => $_POST['ios_token']),array('single','field'=>'id'));
			}

			if($old_device || $old_ios)
			{
				$this->common->updateData('user',array('android_token' => "", "ios_token" => ""),array('id' => $old_device['id']));
			}

			$_POST['password'] = md5($_POST['password']);	
			$_POST['singup_type'] = 1;
			$_POST['created_at'] = date('Y-m-d H:i:s');
			
			if($_POST['user_type']== 1)
			{
				$_POST['status'] = 1;
			}

			$_POST['status'] = 1;

			$post = $this->common->getField('user',$_POST);

			if(empty($exist))
			{
				$result = $this->common->insertData('user',$post);
			}
			else
			{
				$where="id	='" . $exist['id'] . "'";
				$result = $this->common->updateData('user',$post,$where); 
			}

			if($result)
			{
				$userid = $this->db->insert_id();					
				$user = $this->common->getData('user',array('id' => $userid),array('single'));

				if(!empty($user['user_image']))
				{
					$user['user_image'] = base_url('/assets/userfile/profile/'.$user['user_image']);
				}
				else
				{
					$user['user_image']='';
				}

				if(!empty($user['banner_image']))
				{
					$user['banner_image'] = base_url('/assets/userfile/banner/'.$user['banner_image']);
				}
				else
				{
					$user['banner_image']='';
				}
				
				$this->response(true,"Register user successfully",array("userinfo" => $user));
			}
			else
			{
				$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
			}
		}
	}


	function distance_filter()
	{
		if( !empty($_REQUEST['maximumDistance']) && !empty($_REQUEST['user_latitude']) && !empty($_REQUEST['user_longitude']) && !empty($_REQUEST['category']) && !empty($_REQUEST['user_id']))
		{
			$category = $_REQUEST['category'];
			$user_latitude = $_REQUEST['user_latitude'];
			$user_longitude = $_REQUEST['user_longitude'];
			$user_id = $_REQUEST['user_id'];
			$minimumDistance = $_REQUEST['minimumDistance'];
			$maximumDistance = $_REQUEST['maximumDistance'];

			$where = "user_type = 2 AND category ='".$category."' AND status=1";
			$result = $this->common->get_eventList_by_lat($where,$minimumDistance,$maximumDistance,$user_latitude,$user_longitude);

			;
			if(!empty($result))
			{     
                 foreach ($result as $key => $value)
                 {
                 	if(!empty($value['user_image']))
                 	{
                 		$image = base_url('/assets/userfile/profile/'.$value['user_image']);
                 	}
                 	else
                 	{
                 		$image = '';
                 	}


                 	$where_follow="user_id ='" . $_REQUEST['user_id'] . "' AND following_id = '" . $value['id'] . "' ";
					$follow_status = $this->common->getData('user_following',$where_follow,array('single'));

					if($follow_status)
					{
						$follow_status = 1;
					}
					else
					{
						$follow_status = 2;
					}

					
					$arr[]=array('id'=>$value['id'],'first_name'=>$value['first_name'],'last_name'=>$value['last_name'],'business_name'=>$value['business_name'],'image'=>$image,'distance'=>$value['distance'],'follow_status'=>$follow_status);

					
				 }

				 $this->response(true,"user fetch Successfully.",array("user_list" => $arr));	
			}
			else
			{
				$this->response(false,"User Not Found",array("user_list" => array()));
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}



	function user_list()
	{
		if(!empty($_REQUEST['user_latitude']) && !empty($_REQUEST['user_longitude']) && !empty($_REQUEST['category']) && !empty($_REQUEST['user_id']))
		{
			$category = $_REQUEST['category'];
			
			$user_latitude = $_REQUEST['user_latitude'];
			$user_longitude = $_REQUEST['user_longitude'];
			$user_id = $_REQUEST['user_id'];

			$where = "user_type = 2 AND category ='".$category."' AND status=1";
			$result = $this->common->get_user_by_lat($where,$user_latitude,$user_longitude);

			;
			if(!empty($result))
			{     
                 foreach ($result as $key => $value)
                 {
                 	if(!empty($value['user_image']))
                 	{
                 		$image = base_url('/assets/userfile/profile/'.$value['user_image']);
                 	}
                 	else
                 	{
                 		$image = '';
                 	}


                 	$where_follow="user_id ='" . $_REQUEST['user_id'] . "' AND following_id = '" . $value['id'] . "' ";
					$follow_status = $this->common->getData('user_following',$where_follow,array('single'));

					if($follow_status)
					{
						$follow_status = 1;
					}
					else
					{
						$follow_status = 2;
					}

					
					$arr[]=array('id'=>$value['id'],'first_name'=>$value['first_name'],'last_name'=>$value['last_name'],'business_name'=>$value['business_name'],'image'=>$image,'distance'=>$value['distance'],'follow_status'=>$follow_status);

					
				 }

				 $this->response(true,"user fetch Successfully.",array("user_list" => $arr));	
			}
			else
			{
				$this->response(false,"User Not Found",array("user_list" => array()));
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}


	public function updateProfile()
	{
		chmod('./assets/userfile/profile/',0777);
		$id = $_POST['id']; unset($_POST['id']);
		
		if(!empty($_FILES['user_image']))
		{
			$user_image = $this->common->do_upload('user_image','./assets/userfile/profile/');
			$_POST['user_image'] = $user_image['upload_data']['file_name'];
			$old_image = $this->common->getData('user',array('id'=>$id),array('single','field'=>'user_image'));
			
			if(!empty($old_image['user_image']))
			{
				if(file_exists('./assets/userfile/profile/'.$old_image['user_image']))
				{ 
					unlink('./assets/userfile/profile/'.$old_image['user_image']);
				}
			}

		}	

		if(!empty($_FILES['banner_image']))
		{
			$image = $this->common->do_upload('banner_image','./assets/userfile/banner/');
			$_POST['banner_image'] = $image['upload_data']['file_name'];
			$old_image = $this->common->getData('user',array('id'=>$id),array('single','field'=>'user_image'));

			if(!empty($old_image['banner_image']))
			{
				if(file_exists('./assets/userfile/banner/'.$old_image['banner_image']))
				{ 
					unlink('./assets/userfile/banner/'.$old_image['banner_image']);
				}

			}

		}	
		
		$post = $this->common->getField('user',$_POST);
		
		if(!empty($post))
		{		
			$result = $this->common->updateData('user',$post,array('id' => $id)); 
		}
		else
		{
			$result = "";
		}

		if($result)
		{
			$user = $this->common->getData('user',array('id' => $id),array('single'));
			if(!empty($user['user_image']))
			{
				$user['user_image'] = base_url('/assets/userfile/profile/'.$user['user_image']);
			}
			else
			{
				$user['user_image']='';
			}

			if(!empty($user['banner_image']))
			{
				$user['banner_image'] = base_url('/assets/userfile/banner/'.$user['banner_image']);
			}
			else
			{
				$user['banner_image']='';	
			}
			
			$this->response(true,"Profile Update Successfully.",array("userinfo" => $user));

		}
		else
		{
			$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));
		}
	}



	public function updateEvent()
	{
		
		$id = $_POST['id']; unset($_POST['id']);
		
		$post = $this->common->getField('event_tbl',$_POST);
		
		if(!empty($post))
		{		
			$result = $this->common->updateData('event_tbl',$post,array('id' => $id)); 
		}
		else
		{
			$result = "";
		}

		if($result)
		{
			$event = $this->common->getData('event_tbl',array('id' => $id),array('single'));
			
			
			$this->response(true,"Event Update Successfully.",array("eventinfo" => $event));

		}
		else
		{
			$this->response(false,"There is a problem, please try again.",array("eventinfo" => ""));
		}
	}



	public function change_password()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['old_password']) && !empty($_REQUEST['new_password']))	
		{
			$user_id = $_REQUEST['user_id'];
			$old_password = $_REQUEST['old_password'];
			$new_password = $_REQUEST['new_password'];
			$user_info = $this->common->getData('user',array('id' => $user_id),array('single'));
			$old_user_password = $user_info['password'];
			$old_password = md5($old_password);
			
			if ($old_password == $old_user_password)
			{
				$data['password'] = md5($new_password);
				$result = $this->common->updateData('user',$data,array('id' => $user_id));
				$this->response(true,'Password changed successfully');
			} 
			else 
			{
				$this->response(false,'Invalid old password');
				exit();
			}
		}
		else
		{
			$this->response(false,'Missing parameter');
		}
	}


	
	
	public function follow_user()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['follow_uid']))
		{
			 $user_id = $_REQUEST['user_id'];
			 $follow_uid = $_REQUEST['follow_uid'];
				$status = $_REQUEST['status'];
				if ($status == 1) {
					if (!empty($follow_uid)) {
					
						$where="user_id	='" . $user_id . "' AND following_id ='" . $follow_uid . "' ";
						$value = $this->common->getData('user_following',$where,array('single'));
						;
						
					if(empty($value))
						{	
							$insert = $this->common->insertData('user_following',array('user_id' => $user_id,'	following_id' => $follow_uid));

							 $user_data = $this->common->getData('user',array('id'=>$follow_uid),array('single'));
							 	$first_name = $user_data['first_name'];
							 	$last_name = $user_data['last_name'];
							 	$ios_token = $user_data['ios_token'];
							 	$android_token = $user_data['android_token'];
							 	$today = Date('Y-m-d H:i:s'); 

							 	// notification start

								$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $follow_uid,'message' => "Started Following You",'user_send_from'=>$user_id,'date'=>$today,'type'=>1));
							
							
							$notification = array('user_id' => $follow_uid,'message' => "Started Following You",'user_send_from'=>$user_id,'date'=>$today,'type'=>1);

						$follow_data_user = $this->common->getData('user',array('id'=>$follow_uid),array('single'));


						$data_user = $this->common->getData('user',array('id'=>$_REQUEST['user_id']),array('single'));

						$ios_token = $follow_data_user['ios_token'];
						$from_name_notification = $data_user['first_name'].' '.$data_user['last_name'];


						$sendmsg =  array('title'=>$from_name_notification,'body'=>"followed");


						$sendmsg =  array('title'=>$from_name_notification,'body'=>"followed");
							
							
						$android_token ="";
							
							if($ios_token != ""){
										 $isSend = $this->push_iOS($ios_token,$notification,$sendmsg);


								}
								else if($android_token != "")
								{
									$registatoin_id = array($user_data["android_token"]); 
									$this->send_notification($registatoin_id, $sendmsg);

								}

								$uid  = $this->db->insert_id();
						
							$this->response(true,"followed");
							
						}
						else
						{
							$this->response(false,"Follow already added");
						}

					}
				}

				else
                {
                    
                    if (!empty($follow_uid)) {
                    	$where="user_id	='" . $user_id . "' AND following_id ='" . $follow_uid . "' ";
                    	
                    	$value = $this->common->deleteData('user_following',$where);
                    	$this->response(true,"Unfollowed");
                    	$today = Date('Y-m-d H:i:s'); 
                    	$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $follow_uid,'message' =>"Unfollowed You",'user_send_from'=>$user_id,'date'=>$today,'type'=>"Unfollowed"));

                    	 $user_data = $this->common->getData('user',array('id'=>$follow_uid),array('single'));
							 	$first_name = $user_data['first_name'];
							 	$last_name = $user_data['last_name'];
							 	$ios_token = $user_data['ios_token'];
							 	$android_token = $user_data['android_token'];
							 	


                    	$notification = array('user_id' => $follow_uid,'message' =>"Unfollowed You",'user_send_from'=>$user_id,'date'=>$today,'type'=>"Unfollowed");
							

					    $user_data_from = $this->common->getData('user',array('id'=>$user_id),array('single'));

						$from_name_notification = $user_data_from['first_name'].' '.$user_data_from['last_name'];

						$sendmsg =  array('title'=>$from_name_notification,'body'=>"Unfollowed");

							
							// if($ios_token != ""){
							// 			 $isSend = $this->push_iOS($ios_token,$notification,$sendmsg);


							// 	}
							// 	else if($android_token != "")
							// 	{
							// 		$registatoin_id = array($user_data["android_token"]); 
							// 		$this->send_notification($registatoin_id, $sendmsg);

							// 	}

							
                    }
                }
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	
	}



	public function attending_event()
	{
		if(!empty($_REQUEST['event_id']))
		{
			$where = "event_join_user.event_id = '".$_REQUEST['event_id']."' AND event_join_user.event_owner_status = 0";
			$result = $this->common->get_record_join_two_table('event_join_user','user','user_id','id','event_join_user.id,event_join_user.user_id,event_join_user.status,event_join_user.created_at,user.first_name,user.last_name',$where);

			

			

			if(!empty($result))
			{

			$i = $j = $k = 0;

			foreach($result as $value)
			{
			


				$payment_detail = $this->common->getData('payments',array('event_id'=>$_REQUEST['event_id'],'user_id'=>$value['user_id']));
				

				$gift_price = 0;
				if(!empty($payment_detail))
				{
					foreach ($payment_detail as $key => $value_data)
					{
						if($value_data['payment_status'] == 'Completed')
						{
							$gift_price+=$value_data['payment_gross'];
						}
					}
				}
				else
				{
					$gift_price = 0;
				}
				
				

				if($value['status'] == 1)
				{

					if($_REQUEST['status'] == 2)
					{
						if($_REQUEST['user_id'] == $value['user_id'])
						{
							$attending_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['user_id'],"gift"=>$gift_price);
							$i++;
						}
						else
						{
							$attending_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['user_id'],"gift"=>"");
							$i++;	
						}
					}
					else
					{
						$attending_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['user_id'],"gift"=>$gift_price);
							$i++;
					}
				}
				else if($value['status'] == 2)
				{
						if($_REQUEST['status'] == 2)
						{
							if($_REQUEST['user_id'] == $value['user_id'])
							{
								$not_attending_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['user_id'],"gift"=>$gift_price);
								$j++;
							}
							else
							{
								$not_attending_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['user_id'],"gift"=>"");
								$j++;	
							}

							

						}
						else
						{
							$not_attending_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['user_id'],"gift"=>$gift_price);
								$j++;
						}
						
					}
					else
					{
						$total_user[] = $value['user_id'];
					}

					
				}


				
				
	        	
	        	
	       

	        if(empty($attending_user_list))
	        {
	        	$attending_user_list = array();
	        }
	       

	        if(empty($not_attending_user_list))
	        {
	        	$not_attending_user_list = array();
	        }
	        


	        if(empty($total_user))
	        {
	        	$not_confiremed_user_list = array();
	        }
	        else
	        {
	        	$user_id_string = implode("','", $total_user);
	        	$where_user = "`id`  IN ('".$user_id_string."') AND user_type = 1";
	        	$result_user = $this->common->getData('user',$where_user);
	        


				if($_REQUEST['status'] == 2)
				{

					foreach($result_user as $value)
					{
						$payment_detail = $this->common->getData('payments',array('event_id'=>$_REQUEST['event_id'],'user_id'=>$value['id']),array('single'));
				

						if($payment_detail['payment_status'] == 'Completed')
						{
							$gift_price = $payment_detail['payment_gross'];
						}
						else
						{
							$gift_price = "";
						}

						if($_REQUEST['user_id'] == $value['id'])
						{
							$not_confiremed_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['id'],"gift"=>$gift_price);
								$k++;
						}
						else
						{
							$not_confiremed_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['id'],"gift"=>"");
								$k++;
						}
					}
				}
				else
				{
					foreach($result_user as $value)
					{
						$payment_detail = $this->common->getData('payments',array('event_id'=>$_REQUEST['event_id'],'user_id'=>$value['id']),array('single'));
				

						if($payment_detail['payment_status'] == 'Completed')
						{
							$gift_price = $payment_detail['payment_gross'];
						}
						else
						{
							$gift_price = "";
						}
						
						$not_confiremed_user_list[] = array('name'=>$value['first_name'].' '.$value['last_name'],'user_id'=>$value['id'],"gift"=>$gift_price);
								$k++;
					
					}
				}

	        }

	        $this->response(true,"Attending user fetch Successfully.",array("attending" => $attending_user_list,"not_attending"=> $not_attending_user_list,"not_confiremed"=> $not_confiremed_user_list,"attending_count"=>$i,"not_attending_count"=>$j,"not_confiremed_count"=>$k));	
	    }
	    else
	    {
	    	$this->response(false,"you are not invited any one");
	    }

			
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}
	




	public function accept_reject_event()
	{

		if(!empty($_REQUEST['event_id']) && !empty($_REQUEST['user_id']) && !empty($_REQUEST['status']))
		{
			$event_id = $_REQUEST['event_id'];
			$user_id = $_REQUEST['user_id'];
			$status = $_REQUEST['status'];
			$today = Date('Y-m-d H:i:s');


			$where="event_id = '" . $event_id . "' AND user_id = '" . $user_id . "'";
			$result = $this->common->getData('event_join_user',$where);

			if(!empty($result))
			{
				$update_data['status'] = $status;	
				$update_data['created_at'] = $today;
				$update_cart = $this->common->updateData('event_join_user',$update_data,array('event_id' => $event_id,'user_id' => $user_id));


				if($status == 1)
				{
					

					$where_info_invent = "event_tbl.id = '".$event_id."'";
					$event_user_info = $this->common->get_record_join_two_table('event_tbl','user','user_id','id','user.id',$where_info_invent);
					$today = Date('Y-m-d H:i:s'); 

					//notification code start

					$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $event_user_info[0]['id'],'message' =>"Accept event successfully",'date'=>$today,'user_send_from'=>$user_id,'type'=>"3",'event_id'=>$_REQUEST['event_id']));


					$notification_seller = array('user_id' => $event_user_info[0]['id'],'message' =>"Accept event successfully",'date'=>$today,'user_send_from'=>$user_id,'type'=>"3",'event_id'=>$_REQUEST['event_id']);


					$where_seller_notification ="id	='" . $event_user_info[0]['id'] . "'";
					$result_seller_notification = $this->common->getData('user',$where_seller_notification,array('single'));


					$where_user_notification ="id	='" . $_REQUEST['user_id'] . "'";
					$result_user_notification = $this->common->getData('user',$where_user_notification,array('single'));

					

					$seller_ios = $result_seller_notification['ios_token'];
					
					$seller_send_username = $result_user_notification['first_name'].' '.$result_user_notification['last_name'];

					$msg_seller = $result_user_notification['first_name'].' '.$result_user_notification['last_name']." has been accepted your event";
					
					$seller_sendmsg =  array('title'=>$seller_send_username,'body'=>$msg_seller);


					
					$android_token ="";

					if($seller_ios != "")
					{
						
						$isSend = $this->push_iOS($seller_ios,$notification_seller,$seller_sendmsg);
					}
					else if($android_token != "")
					{
						$registatoin_id = array($user_data["android_token"]); 
						$this->send_notification($registatoin_id, $sendmsg);
					}

					$insert_notification = $this->common->insertData('notification_tbl',array('user_id' =>$user_id ,'message' =>"Congratulations you Accept event successfully",'date'=>$today,'user_send_from'=>$event_user_info[0]['id'],'type'=>"3",'event_id'=>$_REQUEST['event_id']));

					$notification_user = array('user_id' =>$user_id ,'message' =>"Congratulations you Accept event successfully",'date'=>$today,'user_send_from'=>$event_user_info[0]['id'],'type'=>"3",'event_id'=>$_REQUEST['event_id']);


					$user_ios = $result_user_notification['ios_token'];

					
					$user_send_username = $result_seller_notification['first_name'].' '.$result_seller_notification['last_name'];

					$msg_user = "Now you are participant";
					
					$user_sendmsg =  array('title'=>$user_send_username,'body'=>$msg_user);

					$android_token="";
					if($user_ios != "")
					{
						
						$isSend = $this->push_iOS($user_ios,$notification_user,$user_sendmsg);
					}
					else if($android_token != "")
					{
						$registatoin_id = array($user_data["android_token"]); 
						$this->send_notification($registatoin_id, $sendmsg);
					}



					$this->response(true,"Accept event Successfully.");	
				}
				else
				{
					
					$event_user_info = $this->common->get_record_join_two_table('event_tbl','user','user_id','id','user.id');
					$today = Date('Y-m-d H:i:s'); 
					$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $event_user_info[0]['id'],'message' =>"Reject event successfully",'date'=>$today,'user_send_from'=>$user_id,'type'=>"4",'event_id'=>$_REQUEST['event_id']));

					$notification_seller= array('user_id' => $event_user_info[0]['id'],'message' =>"Reject event successfully",'date'=>$today,'user_send_from'=>$user_id,'type'=>"4",'event_id'=>$_REQUEST['event_id']);


					$where_user_notification ="id	='" . $_REQUEST['user_id'] . "'";
					$result_user_notification = $this->common->getData('user',$where_user_notification,array('single'));


					$where_seller_notification ="id	='" . $event_user_info[0]['id'] . "'";
					$result_seller_notification = $this->common->getData('user',$where_seller_notification,array('single'));

					$seller_ios = $result_seller_notification['ios_token'];
					
					$seller_send_username = $result_user_notification['first_name'].' '.$result_user_notification['last_name'];

					$msg_seller = $result_user_notification['first_name'].' '.$result_user_notification['last_name']." has been reject your event";

					$seller_sendmsg =  array('title'=>$seller_send_username,'body'=>$msg_seller);



					$android_token ="";

					if($seller_ios != "")
					{
						$isSend = $this->push_iOS($seller_ios,$notification_seller,$seller_sendmsg);
					}
					else if($android_token != "")
					{
						$registatoin_id = array($user_data["android_token"]); 
						$this->send_notification($registatoin_id, $sendmsg);
					}


					$this->response(true,"Reject event Successfully.");	
				}
			}
			else
			{
				$this->response(true,"event are not join yet");	

			}


			
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}



	
	public function add_notes()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_name']) && !empty($_REQUEST['event_start_date']) && !empty($_REQUEST['event_end_date']) && !empty($_REQUEST['venue']) &&!empty($_REQUEST['description']))
		{
			
			$data['user_id'] = $_REQUEST['user_id'];
			$data['event_name'] = $_REQUEST['event_name'];
			$data['venue'] = $_REQUEST['venue'];
			$data['venue_lat'] = $_REQUEST['venue_lat'];
			$data['venue_lng'] = $_REQUEST['venue_lng'];
			$data['description'] = $_REQUEST['description'];
			$data['event_start_date'] = $_REQUEST['event_start_date'];
			$data['event_end_date'] = $_REQUEST['event_end_date'];
			
			$iname = '';
			if(isset($_FILES['event_image']))
			{
				$image = $this->common->do_upload('event_image','./assets/event/');
				if(isset($image['upload_data']))
				{
					$iname = $image['upload_data']['file_name'];
				}
			}		
			
			$data['event_image'] = $iname;	
			$result = $this->common->insertData('note_tbl',$data);
			$insert_id = $this->db->insert_id();
			
			if($result)
			{

				$where_follow="following_id	='" . $_REQUEST['user_id'] . "'";
				$result_follow_user = $this->common->getData('user_following',$where_follow);


				if(!empty($result_follow_user))
				{
						foreach ($result_follow_user as $key => $value) 
						{
							// notification start
							$today = Date('Y-m-d H:i:s');
							$insert_notification = $this->common->insertData('notification_tbl',array('user_id' => $value['user_id'],'message' =>"create a new note",'user_send_from'=>$_REQUEST['user_id'],'date'=>$today,'type'=>"2",'note_id'=>"$insert_id"));

							$notification = array('user_id' => $value['user_id'],'message' =>"create a new note",'user_send_from'=>$_REQUEST['user_id'],'date'=>$today,'type'=>"2",'note_id'=>"$insert_id");

							$where_user_notification ="id	='" . $value['user_id'] . "'";
							$result_user_notification = $this->common->getData('user',$where_user_notification,array('single'));

							$where_seller_notification ="id	='" . $_REQUEST['user_id'] . "'";
							$result_seller_notification = $this->common->getData('user',$where_seller_notification,array('single'));

							$msg_user = "New post by ".$result_seller_notification['first_name'].' '.$result_seller_notification['last_name'];
							$user_send_username = $result_seller_notification['first_name'].' '.$result_seller_notification['last_name'];
							$sendmsg =  array('title'=>$user_send_username,'body'=>$msg_user);

							$ios_token = $result_user_notification['ios_token'];
							$android_token ="";

							if($ios_token != "")
							{
								$isSend = $this->push_iOS($ios_token,$notification,$sendmsg);
							}
							else if($android_token != "")
							{
								$registatoin_id = array($user_data["android_token"]); 
								$this->send_notification($registatoin_id, $sendmsg);
							}

							
						}
				}

				



				$note_array = $this->common->getData('note_tbl',array('id'=>$insert_id),array('single'));
				if(!empty($note_array['event_image']))
				{
					$note_array['event_image']= base_url('/assets/event/'.$note_array['event_image']);

				}
				else
				{
					$note_array['event_image']="";
				}
				
				$this->response(true,"Notes Create Successfully.",array("noteinfo" => $note_array));
			}
			else
			{
				$this->response(false,"There is a problem, please try again.",array());
			}
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	
	}




	public function event_list()
	{
		if(!empty($_REQUEST['user_id']))
		{
			$today = Date('Y-m-d H:i:s');
			// $where="event_start_date >='".$today."'";

			// $this->common->get_record_join_two_table('event_tbl','event_join_user','id');
			// $event_list = $this->common->getData('event_tbl',$where,array('sort_by'=>'	event_start_date','sort_direction' => 'asc'));

			$where="(E.event_live_status = '1' OR E.event_start_date >='".$today."') AND JU.user_id = '".$_REQUEST['user_id']."' AND E.status = '1'";
			$event_list = $this->common->event_list_data($where);

		

		
			if(!empty($event_list))
			{	
				foreach ($event_list as $key => $value) 
				{
					if($value['event_live_status'] == 1)
					{
						$result_check = $this->checkEventExpiration($value['event_start_date']);
					}
					else
					{
						$result_check = array('expire_status'=>1,'show_status'=>1);
					}

					if($result_check['show_status'] == 1)
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
						

						

						
						
						$event_arry[] = array('id'=>$value['id'],'user_id'=>$value['user_id'],'event_name' => $value['event_name'],'event_latitude'=>$value['event_latitude'],'event_longitude' => $value['event_longitude'],'event_venue'=>$value['event_venue'],'event_description' => $value['event_description'],'event_duration'=>$event_duration,'start_date'=>$start_date,'accept_event_count'=>$accept_event_count,'reject_event_count'=>$reject_event_count,"pending_user_count"=>$pending_user_count,"event_created_at"=>$value['event_created_at'],"event_start_date"=>$value['event_start_date'],"event_end_date"=>$value['event_end_date'],'paypal_status'=>$paypal_status,'stripe_status'=>$stripe_status,'accept_reject_status'=>$accept_reject_status,"visibility_status"=>$value['visibility_status'],'event_owner_status'=>$value['event_owner_status'],'expire_status'=>$result_check['expire_status']);
					}

				}	
				
				if(!empty($event_arry))
				{
					$this->response(true,"Event list fetch Successfully.",array("event_list" => $event_arry));
				}
				else
				{
					$this->response(true,"No Notes Found",array("event_list" =>array()));
				}

			}
			else
			{
				$this->response(true,"No Events Found",array("event_list" =>array()));
			}
		}
		else
		{
			$this->response(false,"Missing parameter");
		}
	}

	public function checkEventExpiration($event_start_date)
	{
		$start_date = strtotime($event_start_date);
		$end_date = strtotime("+7 day", $start_date);
		$end_date = date('Y-m-d', $end_date);
		$today = Date('Y-m-d H:i:s');

		if($today < $event_start_date)
		{
			$expire_status = 1;
			return array('expire_status' => $expire_status,'show_status'=>1);
		}
		else
		{
			$expire_status = 2;
			$now = date('Y-m-d');
			if($now < $end_date)
			{
				$show_status = 1;
			}
			else
			{
				$show_status = 2;
			}

			return array('expire_status' => $expire_status,'show_status'=>$show_status);
		}
	}



	public function event_detail()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['event_id']))
		{
			// event join manage
			$join_detail = $this->common->getData('event_join_user',array('event_id'=>$_REQUEST['event_id'],'user_id'=>$_REQUEST['user_id']),array('single'));

			if(empty($join_detail))
			{
				$today = Date('Y-m-d H:i:s');
				$join['user_id'] = $_REQUEST['user_id'];
				$join['event_id'] = $_REQUEST['event_id'];
				$join['created_at'] = $today;
				$result = $this->common->insertData('event_join_user',$join);
			}


			$payment_detail = $this->common->getData('payments',array('event_id'=>$_REQUEST['event_id'],'user_id'=>$_REQUEST['user_id']));
				

			$gift_price = 0;
			if(!empty($payment_detail))
			{
				foreach ($payment_detail as $key => $value_data)
				{
					if($value_data['payment_status'] == 'Completed')
					{
						$gift_price+=$value_data['payment_gross'];
					}
				}
			}
			else
			{
				$gift_price = 0;
			}

			
			
			
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


					$user_accept_reject_result = $this->common->getData('event_join_user',array('user_id'=>$_REQUEST['user_id'],'event_id'=>$result['id'] ),array('single'));
					if(!empty($user_accept_reject_result))
					{
						$accept_reject_status = $user_accept_reject_result['status'];
					}
					else
					{
						$accept_reject_status = 0;
					}

					


					
					
					$event_arry = array('id'=>$result['id'],'user_id'=>$result['user_id'],'event_name' => $result['event_name'],'event_latitude'=>$result['event_latitude'],'event_longitude' => $result['event_longitude'],'event_venue'=>$result['event_venue'],'event_description' => $result['event_description'],'event_duration'=>$event_duration,'start_date'=>$start_date,'accept_event_count'=>$accept_event_count,'reject_event_count'=>$reject_event_count,"pending_user_count"=>$pending_user_count,"event_created_at"=>$result['event_created_at'],"event_start_date"=>$result['event_start_date'],"event_end_date"=>$result['event_end_date'],'paypal_status'=>$paypal_status,'stripe_status'=>$stripe_status,'accept_reject_status'=>$accept_reject_status,"visibility_status"=>$result['visibility_status'],'gift_price'=>$gift_price);
			
				
				
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




	public function notification_list()
	{
		if(!empty($_REQUEST['user_id']))
        {
        	$user_id = $_REQUEST['user_id'];
        
        	$where = "NT.user_id = '".$user_id."'";

        	$user_info = $this->common->get_notification_user($where);


        	// echo"<pre>";
        	// print_r($user_info);

        	foreach ($user_info as $key => $value)
        	{

        		

				if(!empty($value['user_image']))
                 	{
                 		$image = base_url('/assets/userfile/profile/'.$value['user_image']);
                 	}
                 	else
                 	{
                 		$image = '';
                 	}


				$arr[]=array('user_id'=>$value['id'],'first_name'=>$value['first_name'],'last_name'=>$value['last_name'],'email'=>$value['email'],'image'=>$image,'message'=>$value['message'],'date'=>$value['date'],'type'=>$value['type'],'Notification_id'=>$value['Notification_id'],'note_id'=>$value['note_id'],'event_id'=>$value['event_id']);

				}

        	

        
        	$this->response(true,"Notification list.",array("notification_info" =>$arr));
        }
        else
        {
        	$this->response(false,"Missing Parameter.");
        }
	}




	public function note_list()
	{
		if(!empty($_REQUEST['user_id']))
		{
			$event_list = $this->common->getData('note_tbl',array('user_id' => $_POST['user_id']),array('sort_by'=>'id','sort_direction' => 'desc'));
			
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
						
						$note_arry[] = array('id'=>$value['id'],'user_id'=>$value['user_id'],'image' => $image,'event_name'=>$value['event_name'],'venue'=>$value['venue'],'venue_lat'=>$value['venue_lat'],'venue_lng'=>$value['venue_lng'],'description'=>$value['description'],'status'=>$value['status'],"first_name"=>$userdinfo['first_name'],"last_name"=>$userdinfo['last_name'],'date_note'=>$date_note,'time_note'=>$time_note,'user_image'=>$user_image);

					}
				}	
				
				if(!empty($note_arry))
				{
					$this->response(true,"Notes list fetch Successfully.",array("note_list" => $note_arry));
				}
				else
				{
					$this->response(true,"No Notesss Found",array("note_list" =>array()));
				}

			}
			else
			{
				$this->response(true,"No Notes Found",array("note_list" =>array()));
			}
		}
		else
		{
			$this->response(false,"Missing parameter");
		}	

	}



	public function add_feedback()

	{

		if(!empty($_REQUEST['description']) && !empty($_REQUEST['user_id']) )

		{

				$description = $_REQUEST['description'];

				$user_id = $_REQUEST['user_id'];

				

				



				$data['description'] = $description;

				$data['user_id'] = $user_id;

				





				





				if(!empty($_FILES['image']['name']))

				{

					$image = $this->common->do_upload('image','./assets/feedback/');

					if(isset($image['upload_data']))

					{

						$image = $image['upload_data']['file_name'];

					}

					else

					{

						$image = "";

					}

				}

				else

				{

					$image = "";

				}		



				$data['image'] = $image;	

				

				$result = $this->common->insertData('feedback_tbl',$data);



				$this->response(true,"Feedback added Successfully.");	

		}

		else

		{

			$this->response(false,"Missing Parameter.");

		}

	}









	public function add_report()

	{

		if(!empty($_REQUEST['description']) && !empty($_REQUEST['user_id']) )

		{

				$description = $_REQUEST['description'];

				$user_id = $_REQUEST['user_id'];

				$report_type = $_REQUEST['report_type'];

				

				



				$data['description'] = $description;

				$data['user_id'] = $user_id;

				$data['report_type'] = $report_type;

				

				if(!empty($_FILES['image']['name']))

				{

					$image = $this->common->do_upload('image','./assets/report/');

					if(isset($image['upload_data']))

					{

						$image = $image['upload_data']['file_name'];

					}

					else

					{

						$image = "";

					}

				}

				else

				{

					$image = "";

				}		



				$data['image'] = $image;	

				

				$result = $this->common->insertData('report_tbl',$data);



				$this->response(true,"Report added Successfully.");	

		}

		else

		{

			$this->response(false,"Missing Parameter.");

		}

	}











	public function member_list()

	{

		if(!empty($_REQUEST['room_id']))

		{

			$room_id = $_REQUEST['room_id'];

			$where="room_id	='" . $room_id . "'";

			$result = $this->common->getData('room_member_tbl',$where);





			if(!empty($result)){

					 foreach ($result as $key => $value) 

					 {

					 		$id = $value['user_id']; 

					 		$where_user="id	='" . $id . "'";

					 		

					 		$result_user = $this->common->getData('user',$where_user,array('single'));

					 		if(!empty($result_user['user_image']))

					 		{

					 			$user_image = $image = base_url('/assets/userfile/profile/'.$result_user['user_image']);

					 		}

					 		else

						 	{

						 		$user_image = "";

						 	}





						 	$where_country="id	='" . $result_user['user_country'] . "'";

						 	$result_country = $this->common->getData('country',$where_country,array('single'));



						 	if(!empty($result_country))

						 	{

						 		$country_name = $result_country['nicename'];

						 		$country_image = $image = base_url('/assets/country/india.png');

						 	}

						 	else

						 	{

						 		$country_name = "";

						 		$country_image ="";

						 	}



					 	



					 	$user_list[] = array('id'=>$result_user['id'],'username'=>$result_user['username'],'user_id'=>$result_user['user_id'],'user_country'=>$country_name,'gender'=>$result_user['gender'],'image'=>$user_image,'country_image'=>$country_image);



					 	

					 }



					 $this->response(true,"Room member fetch Successfully.",array("member_list" => $user_list));	

				}

				else

				{

					$this->response(true,"Room member Not Found",array("member_list" => array()));			

				}

		}

		else

		{

			$this->response(false,"Missing Parameter.");

		}



	}





	public function group_chat()
	{
		if(!empty($_REQUEST['user_from']) && !empty($_REQUEST['type']) && !empty($_REQUEST['group_id']))
		{
			$post['user_from'] = $_REQUEST['user_from'];
			$post['group_id'] = $_REQUEST['group_id'];
			$post['type'] = $_REQUEST['type'];
			$group_id = $_REQUEST['group_id'];
			$type = $_REQUEST['type'];


			 $user_info = $this->common->getData('user',array('id' => $_REQUEST['user_from']),array('single'));

			if($type == 1)
			{
				$message_user = $_REQUEST['message'];
				$message_user = $message_user;
				$post['message']  =  $message_user;
				$msg = $_REQUEST['message'];
				$message_send_notification = $msg;
				$message_type_notification = 1;

			}


			if($type == 2)
			{
				$image = $this->common->do_upload_file('message','./assets/chat/');
				if(isset($image['upload_data']))
				{
					$msg_image = $image['upload_data']['file_name'];
					$msg = base_url('/assets/chat/'.$msg_image);
					$post['message']=$image['upload_data']['file_name'];
					$message_send_notification = base_url('/assets/chat/'.$msg_image);
					$message_type_notification = 2;
				}
				else
				{
					$this->response(false,'Missing parameter');
					exit();
				}
			}


			if($type == 3)
			{
				$post['comment'] = $_REQUEST['comment'];
				$image = $this->common->do_upload_file('message','./assets/chat/');
				if(isset($image['upload_data']))
				{
					$msg_image = $image['upload_data']['file_name'];
					$msg = base_url('/assets/chat/'.$msg_image);
					$post['message']=$image['upload_data']['file_name'];
					$message_send_notification = base_url('/assets/chat/'.$msg_image);
					$message_type_notification = 3;
				}
				else
				{
					
					$this->response(false,'Missing parameter');
					exit();
				}
			}




		


					if(!empty($user_info['user_image']))
                 	{
                 		$user_image = base_url('/assets/userfile/profile/'.$user_info['user_image']);
                 	}
                 	else
                 	{
                 		$user_image = '';
                 	}
			
			$post['created_at'] = date('Y-m-d');
			$result = $this->common->insertData('group_chat',$post);

			$insert_id = $this->db->insert_id();

			if($result)
			{
				$message = "message sent successfully";

				if($_REQUEST['type'] == 1 || $_REQUEST['type'] == 2)
				{
					$last_msg =  array("id" => $insert_id,
					"user_from" => $_POST['user_from'],
					"group_id" => $group_id,
					"message"=> $msg,
					"message_staus"=> $message_type_notification,
					"created_at" => $post['created_at'],
					'first_name'=>$user_info['first_name'],
					'last_name'=>$user_info['last_name'],
					'user_image'=>$user_image);

				}


				if($_REQUEST['type'] == 3)
				{
					$last_msg =  array("id" => $insert_id,
					"user_from" => $_POST['user_from'],
					"group_id" => $group_id,
					"message"=> $msg,
					"comment"=> $_POST['comment'],
					"message_staus"=> $message_type_notification,
					"created_at" => $post['created_at'],
					'first_name'=>$user_info['first_name'],
					'last_name'=>$user_info['last_name'],
					'user_image'=>$user_image);
				}


				


				// notification start

				// $user_data_to = $this->common->getData('user',array('id'=>$user_to),array('single'));

				// $user_data_from = $this->common->getData('user',array('id'=>$_REQUEST['user_from']),array('single'));

				// $ios_token = $user_data_to['ios_token'];
				// $android_token = $user_data_to['android_token'];
				// $user_data_from_name = $user_data_from['username']; 
				// $message_push = $user_data_from_name." Sent You a Message";
				// $title = "chat";
				// $type = "chat";

					
				

				// if($ios_token != ""){

				// 	$messages_push = array("alert" => $title, "msg" => $message_push,"sound"=>"default","type" => $type,"message_send"=>$message_send_notification,"message_staus"=>$message_type_notification);	
					
				// 	$this->push_iOS($ios_token,$messages_push);

					
				// }
				// else if($android_token != "")
				// {
					
				// 	$messages_push = array("title" => $title, "message" => $message_push, "type" => $type,"message_send"=>$message_send_notification,"last_msg"=>$last_msg);	

				// 	$registatoin_id = array($android_token); 
				// 	$this->send_notification($registatoin_id, $messages_push);

				// }

				// notification end
		}
		else
		{
			$message = false;
		}

		if($message){

			$this->response(true,$message,array("last_msg" => $last_msg));		

		}else{

			$this->response(false,$message,array("last_msg" => $last_msg));		

		}		 	

		}

		else

		{

			$this->response(false,'Missing Parameter');	

		}

	}












	







	function replaceKey($subject, $newKey, $oldKey) {



 		if (!is_array($subject)) return $subject;



    $newArray = array(); // empty array to hold copy of subject

    foreach ($subject as $key => $value) {



        // replace the key with the new key only if it is the old key

        $key = ($key === $oldKey) ? $newKey : $key;



        // add the value with the recursive call

        $newArray[$key] = replaceKey($value, $newKey, $oldKey);

    }

    return $newArray;

}



	public function room_list_others()

	{

		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['type']))

		{

			if($_REQUEST['type'] == 1)

			{

				$result = $this->common->getData('room_follow',array('following_id'=>$_REQUEST['user_id']));

			}



			if($_REQUEST['type'] == 2)

			{

				$result = $this->common->getData('room_member_tbl',array('user_id'=>$_REQUEST['user_id']));

			}



				

			if(!empty($result))

			{

				foreach ($result as $key => $value) 

				{

					if($_REQUEST['type'] == 1)

					{

						$id = $value['room_id'];

					}



					if($_REQUEST['type'] == 2)

					{

						$id = $value['room_id'];

					}



					$where="R.id ='" . $id . "' R.status = 1";

					$result_room = $this->common->room_detail($where,array('single'));



					if(!empty($result_room['image']))

					 	{

					 		$room_image = $image = base_url('/assets/room/'.$result_room['image']);

					 	}

					 	else

					 	{

					 		$room_image = "";

					 	}



				

						

						$where_country="id	='" . $result_room['user_country'] . "'";

					 	$result_country = $this->common->getData('country',$where_country,array('single'));



					 	



					 	if(!empty($result_country))

					 	{

					 		$country_name = $result_country['nicename'];

					 		$country_image = $image = base_url('/assets/country/india.png');

					 	}

					 	else

					 	{

					 		$country_name = "";

					 		$country_image ="";

					 	}

					$array_room[] = array('id'=>$result_room['id'],'user_id'=>$result_room['user_id'],'room_name'=>$result_room['room_name'],'announcement'=>$result_room['announcement'],'user_country'=>$country_name,'tags'=>$result_room['tag_name'],'image'=>$room_image,'country_image'=>$country_image);



					

				}



				$this->response(true,"Room found successfully",array("room_list" => $array_room));

			}

			else

			{

				$this->response(true,"Room Not Found",array("room_list" => array()));

			}

		}

		else

		{

			$this->response(false,"Missing Parameter.");

	 		die();

		}



	}





	public function gift_sent()

	{

		if(!empty($_REQUEST['type']))

		{

			if($_REQUEST['type'] == 1)

			{

				$today = date('Y-m-d');

				$query="SELECT gift_from, sum(price)  as total_price FROM   gift_send_tbl  where  `created_at` = '".$today."' GROUP BY gift_from order by total_price desc";

				$result = $this->common->query($query);

			}





			if($_REQUEST['type'] == 2)

			{

		

				$date_week = date('Y-m-d',time()-(7*86400)); // 7 days ago

				$query="SELECT gift_from, sum(price)  as total_price FROM   gift_send_tbl  where  `created_at`>= '".$date_week."' GROUP BY gift_from order by total_price desc";

				$result = $this->common->query($query);



			}



			if($_REQUEST['type'] == 3)

			{

				$date_month=Date('Y-m-d', strtotime("-30 days"));

				$query="SELECT gift_from, sum(price)  as total_price FROM   gift_send_tbl  where  `created_at`>= '".$date_month."' GROUP BY gift_from order by total_price desc";

				$result = $this->common->query($query);

				

			}



			





			if(!empty($result))

			{

			 	foreach ($result as $key => $value) 

				{

					

					$id = $value['gift_from']; 

				



					$where_user="id	='" . $id . "'";

					$result_user = $this->common->getData('user',$where_user,array('single'));



					if(!empty($result_user['user_image']))

					{

					 	$user_image = $image = base_url('/assets/userfile/profile/'.$result_user['user_image']);

					}

					else

					{

					 	$user_image = "";

					}



					$where_country="id	='" . $result_user['user_country'] . "'";

					 	$result_country = $this->common->getData('country',$where_country,array('single'));



					if(!empty($result_country))

					{

					 	$country_name = $result_country['nicename'];

					 	$country_image = $image = base_url('/assets/country/india.png');

					}

					else

					{

					 	$country_name = "";

					 	$country_image ="";

					}



					$user_list[] = array('id'=>$result_user['id'],'username'=>$result_user['username'],'user_id'=>$result_user['user_id'],'user_country'=>$country_name,'gender'=>$result_user['gender'],'image'=>$user_image,'country_image'=>$country_image,'total_price'=>$value['total_price']);



				}



				$this->response(true,"User Found",array("user_list" => $user_list));

			}

			else

			{

				$this->response(true,"User Not Found",array("user_list" => array()));			

			}



		}

		else

		{

			$this->response(false,"Missing Parameter.");

		}

	}







	public function gift_received()

	{

		if(!empty($_REQUEST['type']))

		{

			if($_REQUEST['type'] == 1)

			{

				$today = date('Y-m-d');

				$query="SELECT gift_to, sum(price)  as total_price FROM   gift_send_tbl  where  `created_at` = '".$today."' GROUP BY gift_to order by total_price desc";

				$result = $this->common->query($query);

			}





			if($_REQUEST['type'] == 2)

			{

		

				$date_week = date('Y-m-d',time()-(7*86400)); // 7 days ago

				$query="SELECT gift_to, sum(price)  as total_price FROM   gift_send_tbl  where  `created_at`>= '".$date_week."' GROUP BY gift_to order by total_price desc";

				$result = $this->common->query($query);



			}



			if($_REQUEST['type'] == 3)

			{

				$date_month=Date('Y-m-d', strtotime("-30 days"));

				$query="SELECT gift_to, sum(price)  as total_price FROM   gift_send_tbl  where  `created_at`>= '".$date_month."' GROUP BY gift_to order by total_price desc";

				$result = $this->common->query($query);

				

			}



			





			if(!empty($result))

			{

			 	foreach ($result as $key => $value) 

				{

					

					$id = $value['gift_to']; 

				



					$where_user="id	='" . $id . "'";

					$result_user = $this->common->getData('user',$where_user,array('single'));



					if(!empty($result_user['user_image']))

					{

					 	$user_image = $image = base_url('/assets/userfile/profile/'.$result_user['user_image']);

					}

					else

					{

					 	$user_image = "";

					}



					$where_country="id	='" . $result_user['user_country'] . "'";

					 	$result_country = $this->common->getData('country',$where_country,array('single'));



					if(!empty($result_country))

					{

					 	$country_name = $result_country['nicename'];

					 	$country_image = $image = base_url('/assets/country/india.png');

					}

					else

					{

					 	$country_name = "";

					 	$country_image ="";

					}



					$user_list[] = array('id'=>$result_user['id'],'username'=>$result_user['username'],'user_id'=>$result_user['user_id'],'user_country'=>$country_name,'gender'=>$result_user['gender'],'image'=>$user_image,'country_image'=>$country_image,'total_price'=>$value['total_price']);



				}



				$this->response(true,"User Found",array("user_list" => $user_list));

			}

			else

			{

				$this->response(true,"User Not Found",array("user_list" => array()));			

			}



		}

		else

		{

			$this->response(false,"Missing Parameter.");

		}

	}







	







	public function room_list()

	{

		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['type']))

		{



			if($_REQUEST['type'] == 1)

			{

				$user_id = $_REQUEST['user_id'];

				$where="R.user_id	!='" . $user_id . "' AND R.status =1";

				$result = $this->common->room_detail($where);

				

			}





			if($_REQUEST['type'] == 2)

			{

				$user_id = $_REQUEST['user_id'];

				$today = date('Y-m-d');

				$where="R.user_id	!='" . $user_id . "' AND R.room_date ='" . $today . "' AND R.status =1";

				$result = $this->common->room_detail($where);

				

			}





			if($_REQUEST['type'] == 3)

			{

				if(!empty($_REQUEST['country']) ||!empty($_REQUEST['tag']))

 				{

 					if(!empty($_REQUEST['tag']) && empty($_REQUEST['country']) )

 					{

	 					$user_id = $_REQUEST['user_id'];

						$tag = $_REQUEST['tag'];

						$where="R.user_id	!='" . $user_id . "' AND  R.tags ='" . $tag . "' AND R.status =1";

						$result = $this->common->room_detail($where);

 					}





					

					if(!empty($_REQUEST['country']) && empty($_REQUEST['tag']) )

 					{

	 					$user_id = $_REQUEST['user_id'];

						$country = $_REQUEST['country'];

						$where="user.user_country	='" . $country . "' AND  room_tbl.user_id !='" . $user_id . "' AND room_tbl.status =1";

						$result = $this->common->get_join_three_table_where('room_tbl','user','tag_tbl','id','user_id','tag_id','tags','room_tbl.id,room_tbl.user_id,room_tbl.room_name,room_tbl.announcement,room_tbl.membership_fee,room_tbl.room_date,room_tbl.room_time,room_tbl.image,user.user_country,tag_tbl.tag_name',$where,'room_tbl.id');



					}



				}

 				else

 				{

 					$this->response(false,"Missing Parameter.");

 					die();

 				}

			}









				if(!empty($result))

				{

						$pin_result = $this->common->getData('add_pin_top_tbl');

						

						if(!empty($pin_result))

						{

							foreach ($pin_result as $key => $value) 

							{

								$end_time = $value['end_time'];

								$start_time = date('Y-m-d H:i:s');



								if($start_time<$end_time)

								{

									$arr_pin[] = $this->myfunction($result,'user_id',$value['user_id']);

								}

							}

							// user id available in those index of result array list show 

							

							if(!empty($arr_pin))

							{

								foreach ($arr_pin as $arr_pin_key => $arr_pin_value) 

								{

									foreach ($arr_pin_value as $key => $value) 

									{

											$array_key_index[] = $value;

									}



									// merge index are single array 

								}

								

								if(!empty($array_key_index))

								{

									$array_count = count($result);// array size

									$array_count = $array_count-1;

									

									for ($x = $array_count; $x >= 0; $x--)

									{

										if (in_array($x, $array_key_index))

										  {

										  		

										  }

										else

										  {

										  	$array_key_index[] = $x;

										  }

									}

									// give overall index



									foreach ($array_key_index as $key => $value)

									{

											$array_result[] = $result[$value];

									} 

								 	// result array value add by index formate

								}

								else

								{

									$array_result = $result;

								}

							}

							else

							{

								$array_result = $result;

							}

						}

						else

						{

							$array_result = $result;

						}

					 

					 foreach ($array_result as $key => $value) 

					 {

					 	

					 	if(!empty($value['image']))

					 	{

					 		$room_image = $image = base_url('/assets/room/'.$value['image']);

					 	}

					 	else

					 	{

					 		$room_image = "";

					 	}



				

						

						$where_country="id	='" . $value['user_country'] . "'";

					 	$result_country = $this->common->getData('country',$where_country,array('single'));



					 	



					 	if(!empty($result_country))

					 	{

					 		$country_name = $result_country['nicename'];

					 		$country_image = $image = base_url('/assets/country/india.png');

					 	}

					 	else

					 	{

					 		$country_name = "";

					 		$country_image ="";

					 	}



					 	$user_list[] = array('id'=>$value['id'],'user_id'=>$value['user_id'],'room_name'=>$value['room_name'],'announcement'=>$value['announcement'],'user_country'=>$country_name,'tags'=>$value['tag_name'],'image'=>$room_image,'country_image'=>$country_image);



					 	

					 }



					 $this->response(true,"user fetch Successfully.",array("room_list" => $user_list));	

				}

				else

				{

					$this->response(true,"User Not Found",array("room_list" => array()));			

				}

					



		}

		else

		{

			$this->response(false,"Missing Parameter.");

		}

	}







			public function general_room_list()

			{

				if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['type']))

				{



					if($_REQUEST['type'] == 1)

					{

						$user_id = $_REQUEST['user_id'];

						$where="R.user_id ='" . $user_id . "' AND R.status=1";

						$result = $this->common->room_detail($where);

					}

					else

					{

						$user_id = $_REQUEST['user_id'];

						$where="R.user_id	!='" . $user_id . "' AND R.status=1";

						$result = $this->common->room_detail($where);

					}

						



						

						if(!empty($result))

						{

							foreach ($result as $key => $value) 

							{

							 	$where_country="id	='" . $value['user_country'] . "'";

					 			$result_country = $this->common->getData('country',$where_country,array('single'));



					 			if(!empty($value['image']))

							 	{

							 		$room_image = $image = base_url('/assets/room/'.$value['image']);

							 	}

							 	else

							 	{

							 		$room_image = "";

							 	}





								if(!empty($result_country))

					 			{

					 				$country_name = $result_country['nicename'];

					 				$country_image = $image = base_url('/assets/country/india.png');

					 			}

					 			else

					 			{

					 				$country_name = "";

					 				$country_image ="";

					 			}



					 			$user_list[] = array('id'=>$value['id'],'user_id'=>$value['user_id'],'room_name'=>$value['room_name'],'announcement'=>$value['announcement'],'user_country'=>$country_name,'tags'=>$value['tag_name'],'image'=>$room_image,'country_image'=>$country_image);

							 	

							 }



							 $this->response(true,"Room fetch Successfully.",array("room_list" => $user_list));	

						}

						else

						{

							$this->response(true,"Room Not Found",array("room_list" => array()));			

						}

							



				}

				else

				{

					$this->response(false,"Missing Parameter.");

				}

			}





	function room_check_in()

	{

		if(!empty($_REQUEST['room_id'] && !empty($_REQUEST['password'])))

		{

			$room_id = $_REQUEST['room_id'];

			$password = $_REQUEST['password'];

			

			$password = md5($_POST['password']);

			

			$result = $this->common->getData('room_tbl',array('id' => $room_id, '	room_lock_password' => $password),array('single'));

			if(!empty($result))

			{

				$this->response(true,"Enter Room Successfully.");

			}

			else

			{

				$this->response(true,"Password is wrong");

			}



		}

	}







	public function set_room_password()

	{

		if(!empty($_REQUEST['room_id']) && !empty($_REQUEST['password']))

		{

			$room_id = $_REQUEST['room_id'];

			$password = $_REQUEST['password'];

			$password = md5($_POST['password']);



			$data['room_lock_password'] = $password;

			$result = $this->common->updateData('room_tbl',$data,array('id' => $room_id));

			$this->response(true,'Password Set scuccessfully');

		}

		else

		{

			$this->response(false,'Missing parameter');

		}



	}



	public function change_room_password()

	{

		if(!empty($_REQUEST['room_id']) && !empty($_REQUEST['old_password']) && !empty($_REQUEST['new_password']))

    		{

    			$room_id = $_REQUEST['room_id'];

    			$old_password = $_REQUEST['old_password'];

    			$new_password = $_REQUEST['new_password'];



    			$room_info = $this->common->getData('room_tbl',array('id' => $room_id),array('single'));

    			$old_room_password = $room_info['room_lock_password'];

    			$old_password = md5($old_password);

    			if ($old_password == $old_room_password) 

				{

    				$data['room_lock_password'] = md5($new_password);

    				$result = $this->common->updateData('room_tbl',$data,array('id' => $room_id));

    				$this->response(true,'Password changed successfully');

    			} 

    			else 

    			{

					$this->response(false,'Invalid old password');

					exit();

				}

    		}

			else

			{

				$this->response(false,'Missing parameter');

			}

	}





	



	function room_detail()

	{

		if(!empty($_REQUEST['room_id'] && !empty($_REQUEST['user_id'])))

		{

			$where_room="R.id	='" . $_REQUEST['room_id'] . "'";

			$result = $this->common->room_detail($where_room,array('single'));



			if(!empty($result))

			{



				$where_country="id	='" . $result['user_country'] . "'";

				$result_country = $this->common->getData('country',$where_country,array('single'));

				

				if(!empty($result_country))

				{

					$result['country_name'] = $result_country['nicename'];

					$result['country_image'] = $image = base_url('/assets/country/india.png');

				}

				else

				{

					$result['country_name'] = "";

					$result['country_image'] ="";

				}





				if(!empty($result['image']))

				{

					$result['image'] = $image = base_url('/assets/room/'.$result['image']);

				}

				else

				{

					$result['image'] = "";

				}



				$where_room_lock_check ="user_id = '". $result['user_id'] ."'";

				$result_lock_check = $this->common->getData('add_room_lock_tbl',$where_room_lock_check,array('single'));



				if(!empty($result_lock_check))

				{



					$end_date = $result_lock_check['end_date'];

					$start_date = date('Y-m-d');



					if($start_date<$end_date)

					{

						$result['room_lock_staus'];

					}

					else

					{

						$result['room_lock_staus'] = "1";

					}



				}

				else

				{

					$result['room_lock_staus'] = "1";

				}







				$where_follow ="room_id = '". $_REQUEST['room_id'] ."' AND following_id = '". $_REQUEST['user_id'] ."'";

				$result_follow = $this->common->getData('room_follow',$where_follow,array('single'));



				if(!empty($result_follow))

				{

					$follow_status = 1;

				}

				else

				{

					$follow_status = 2;

				}



				$result['follow_status'] = $follow_status;



				$this->response(true,"Room fetch Successfully.",array("room_detail" => $result));

			}

			else

			{

				$this->response(false,"Room not found",array("room_detail" =>""));

			}

		}

		else

		{

			$this->response(false,'Missing parameter');

		}

	}









	function myfunction($products, $field, $value)

	{

	   foreach($products as $key => $product)

	   {

	      if ( $product[$field] == $value )

	       {	

	       		 $key_arr[] = $key;

	       }

	   }





	   if(!empty($key_arr))

	   {

	   		return $key_arr;

	   }

	   else

	   {

	   		$key_arr =array();

	   		return $key_arr;

	   }

	 }




	 public function user_list_data()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['type']))
		{
			
			if($_REQUEST['type'] == 1)
			{
				$user_id = $_REQUEST['user_id'];
				$where="following_id ='" . $user_id . "'";
				$result = $this->common->getData('user_following',$where);
			}
			
				if(!empty($result)){
					 foreach ($result as $key => $value) 
					 {
					 	if($_REQUEST['type'] == 1)
						{
					 		$id = $value['user_id']; 
					 	}
					 	 
						$where_user="id	='" . $id . "'";
					 	$result_user = $this->common->getData('user',$where_user,array('single'));
					 	if(!empty($result_user['user_image']))
					 	{
					 		$image = base_url('/assets/userfile/profile/'.$result_user['user_image']);
					 	}
					 	else
					 	{
					 		$image = "";
					 	}
					 	
					 	
					 	$user_list[]=array('id'=>$result_user['id'],'first_name'=>$result_user['first_name'],'last_name'=>$result_user['last_name'],'business_name'=>$result_user['business_name'],'image'=>$image);
					 	
					 }
					 $this->response(true,"user fetch Successfully.",array("user_list" => $user_list));	
				}
				else
				{
					$this->response(true,"User Not Found",array("user_list" => array()));			
				}
					
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}





	public function general_user_list()
	{
		if(!empty($_REQUEST['id']))
		{
				$user_id = $_REQUEST['id'];

				$where="id	!='" . $user_id . "'AND status=1";
				$result = $this->common->getData('user',$where);
				
				if(!empty($result))
				{
					 foreach ($result as $key => $value) 
					 {
					 	if(!empty($value['user_image']))
					 	{
					 		$user_image = $image = base_url('/assets/userfile/profile/'.$value['user_image']);
					 	}
					 	else
					 	{
					 		$user_image = "";
					 	}
					 	$where_country="id	='" . $value['user_country'] . "'";
					 	$result_country = $this->common->getData('country',$where_country,array('single'));
					 	if(!empty($result_country))
					 	{
					 		$country_name = $result_country['nicename'];
					 		$country_image = $image = base_url('/assets/country/india.png');
					 	}
					 	else
					 	{
					 		$country_name = "";
					 		$country_image ="";
					 	}
					 	
					 	$user_list[] = array('id'=>$value['id'],'username'=>$value['username'],'user_id'=>$value['user_id'],'gender'=>$value['gender'],'image'=>$user_image,'user_country'=>$country_name,'country_image'=>$country_image);
					 	
					 }
					 $this->response(true,"user fetch Successfully.",array("user_list" => $user_list));	
				}
				else
				{
					$this->response(true,"User Not Found",array("user_list" => array()));			
				}
					
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}

	}





















	public function wholesaler_list()



	{



		if(!empty($_REQUEST['category_id']))



		{



			$category_id = $_REQUEST['category_id'];



			$where = "FIND_IN_SET('".$category_id ."',category) and (user_type = 2 or user_type=3)";



            $result = $this->common->getData('user',$where);



     	



     		if(!empty($result))



         	{    



	            foreach($result as $value)



	            {



	            	if(!empty($value['image']))



					{



						$image = base_url('/assets/userfile/profile/'.$value['image']);







					}



					else



					{



						$image = "";



					}







					$rating = $this->rating_count($value['id']);



	            	$wholesaler_array[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'mobile'=>$value['mobile'],'user_address'=>$value['user_address'],'image'=>$image,'user_type'=>$value['user_type'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'id'=>$value['id'],'rating'=> $rating);



            	}



            



             



    				$this->response(true,"user fetch Successfully.",array("wholesaler_list" => $wholesaler_array));



    			}



    			else



    			{



					$this->response(true,"Wholesaler Not Found",array("wholesaler_list" => array()));



				}



		}



		else



		{



			$this->response(false,"Missing Parameter.");



		}







	}























	public function user_by_type()



	{



		if(!empty($_REQUEST['user_type']))



		{



			$user_type = $_REQUEST['user_type'];



			$where = "user_type != '".$user_type ."'";



            $result = $this->common->getData('user',$where);



     	



     		if(!empty($result))



         	{    



	            foreach($result as $value)



	            {



	            	if(!empty($value['image']))



					{



						$image = base_url('/assets/userfile/profile/'.$value['image']);







					}



					else



					{



						$image = "";



					}







					



	            	$user_array[] = array('id'=>$value['id'],'name'=>$value['name'],'email'=>$value['email'],'image'=>$image,'user_type'=>$value['user_type']



	            );



            	}



            



             



    				$this->response(true,"user fetch Successfully.",array("user_list" => $user_array));



    			}



    			else



    			{



					$this->response(true,"Wholesaler Not Found",array("user_list" => array()));



				}



		}



		else



		{



			$this->response(false,"Missing Parameter.");



		}







	}



















	public function wholesaler_add_rating()



	{



		if(!empty($_REQUEST['wholesaler_id'])&& !empty($_REQUEST['user_id']))



		{



			$wholesaler_id = $_REQUEST['wholesaler_id'];



			$user_id = $_REQUEST['user_id'];



			



			$where = "wholesaler_id ='".$wholesaler_id ."' AND user_id ='".$user_id ."' ";



            $result = $this->common->getData('wholesaler_rating',$where);



            



            if(empty($result))



            	{     



            		







					$post = $this->common->getField('wholesaler_rating',$_POST);







					$result_insert = $this->common->insertData('wholesaler_rating',$post);



					$avg_rating = $this->rating_count($wholesaler_id);







					



					$this->response(true,"Rating add Successfully.",array("rating" =>$avg_rating));



    			}



    			else



    			{







    				unset($_POST['wholesaler_id']);



    				unset($_POST['user_id']);



    				$post = $this->common->getField('wholesaler_rating',$_POST);



    				$where_update = "wholesaler_id ='".$wholesaler_id ."' AND user_id ='".$user_id ."' ";



    				



					$result = $this->common->updateData('wholesaler_rating',$post,$where_update); 



					$avg_rating = $this->rating_count($wholesaler_id);



					



					$this->response(true,"Rating Edited Successfully.",array("rating" =>$avg_rating));







				}



		}



		else



		{



			$this->response(false,"Missing Parameter.");



		}







	}











	public function product_add_rating()



	{



		if(!empty($_REQUEST['product_id']) && !empty($_REQUEST['user_id']))



		{



			$product_id = $_REQUEST['product_id'];



			$user_id = $_REQUEST['user_id'];



			







			$where = "product_id ='".$product_id ."' AND user_id ='".$user_id ."' ";



            $result = $this->common->getData('product_rating',$where);



            



            if(empty($result))



            	{     







            		$post = $this->common->getField('product_rating',$_POST);







					$result_insert = $this->common->insertData('product_rating',$post);



					$avg_rating = $this->rating_count_product($product_id);







					



					$this->response(true,"Rating add Successfully.",array("rating" =>$avg_rating));



    			}



    			else



    			{







    				unset($_POST['product_id']);



    				unset($_POST['user_id']);



    				$post = $this->common->getField('product_rating',$_POST);











    				$where_update = "product_id ='".$product_id ."' AND user_id ='".$user_id ."' ";



    		







					$result = $this->common->updateData('product_rating',$post,$where_update); 



					$avg_rating = $this->rating_count_product($product_id);



					



					$this->response(true,"Rating Edited Successfully.",array("rating" =>$avg_rating));







				}



		}



		else



		{



			$this->response(false,"Missing Parameter.");



		}







	}







	public function logout()
	{
		if(!empty($_REQUEST['user_id']))
		{	
			$user_id = $_REQUEST['user_id'];

			$this->common->updateData('user',array('android_token' => "", "ios_token" => ""),array('id' => $user_id));	
			$this->response(true,"Logout successfully");
		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}	
	}




















	function rating_count($wholesaler_id)



	{



		



		$count_user = $this->common->getData('wholesaler_rating',array('wholesaler_id'=>$wholesaler_id),array('count'));







		if($count_user)



		{



			$query="SELECT SUM(`rating`) AS rating_count FROM wholesaler_rating  WHERE wholesaler_id='".$wholesaler_id."'";



			$total_wholesale_rating = $this->common->query($query);



			$total_rating_user = $total_wholesale_rating[0]->rating_count;



			$avg=$total_rating_user/$count_user;



		}



		else



		{



			$avg = 0;



		}



		return $avg;



	}







	function rating_count_product($product_id)



	{



		



		$count_user = $this->common->getData('product_rating',array('product_id'=>$product_id),array('count'));







		if($count_user)



		{



			$query="SELECT SUM(`rating`) AS rating_count FROM product_rating  WHERE product_id='".$product_id."'";



			$total_product_rating = $this->common->query($query);



			$total_rating_user = $total_product_rating[0]->rating_count;



			$avg=$total_rating_user/$count_user;



		}



		else



		{



			$avg = 0;



		}



		return $avg;



	}























	public function user_detail()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['login_id']))
		{

			// user following array start
			

			$where_follow_array="user_following.user_id ='".$_REQUEST['user_id']."'";
			$result_follow_array = $this->common->get_join_three_table_where('user','user_following','category_tbl','following_id','id','category_id','category','user.first_name,user.last_name,user.id,category_tbl.category_name',$where_follow_array);


			// user following array end

			$where_follow="user_id ='" . $_REQUEST['login_id'] . "' AND following_id = '" . $_REQUEST['user_id'] . "' ";
					$follow_status = $this->common->getData('user_following',$where_follow,array('single'));

					if($follow_status)
					{
						$follow_status = 1;
					}
					else
					{
						$follow_status = 2;
					}

			$user_id = $_REQUEST['user_id'];
			$result = $this->common->getData('user',array('id' => $user_id),array('single'));




			if(!empty($result))
			{
				$count = $this->common->getData('user_following',array('following_id'=>$_REQUEST['user_id']),array('count'));
				

				if(!empty($result['category']))
				{
					$category_info  = $this->common->getData('category_tbl',array('category_id' => $result['category']),array('single'));
					$result['category_name'] = $category_info['category_name'];
				}
				else
				{
					$result['category_name'] = "";
				}				
				
				if(!empty($result['user_image']))
				{
					$result['user_image'] = $image = base_url('/assets/userfile/profile/'.$result['user_image']);
				}
				else
				{
					$result['user_image'] ="";
				}
				
				if(!empty($result['banner_image']))
				{
					$result['banner_image'] = base_url('/assets/userfile/banner/'.$result['banner_image']);
				}
				else
				{
					$result['banner_image']="";
				}

				if(!empty($result['start_time']))
				{
					$result['start_time'] = date("h:i a", strtotime($result['start_time']));
				}


				if(!empty($result['end_time']))
				{
					$result['end_time'] = date("h:i a", strtotime($result['end_time']));
				}

				$result['follow_count'] = $count;
				$result['follow_status'] = $follow_status;
				$result['follow_user'] = $result_follow_array;	
				unset($result['password']);
				$this->response(true,"user fetch Successfully.",array("userinfo" => $result));	
			}
			else
			{
				$this->response(false,"User Not Found",array("userinfo" => ""));
			}

		}
		else
		{
			$this->response(false,"Missing Parameter.");
		}
	}











	





function timeAgoNew($time_ago)

{

    $time_ago_date=$time_ago;

    $time_ago = strtotime($time_ago);

    $cur_time   = time();

    $time_elapsed   = $cur_time - $time_ago;

    $seconds    = $time_elapsed ;

    $minutes    = round($time_elapsed / 60 );

    $hours      = round($time_elapsed / 3600);

    $days       = round($time_elapsed / 86400 );

    $weeks      = round($time_elapsed / 604800);

    $months     = round($time_elapsed / 2600640 );

    $years      = round($time_elapsed / 31207680 );

    // Seconds

    

    if($seconds <= 60 || $minutes <=60 ||  $hours <=24 || $days <= 7 ){

    	$result_empty ="";

      return $result_empty;

    }

    //Weeks

    else if($weeks <= 4.3){

        if($weeks==1){

            return "1 week ago";

        }else{

            return "$weeks week ago";

        }

    }

   

   //Months

   else if($months <=12){

       if($months==1){

           return "1 month ago";

       }else{

           return "$months month ago";

       }

   }

   //Years

   else{

       if($years==1){

           return "1 year ago";

       }else{

           return "$years year ago";

       }

   }

    

    

}





function timeAgo($time_ago)

{

    $time_ago_date=$time_ago;

    $time_ago = strtotime($time_ago);

    $cur_time   = time();

    $time_elapsed   = $cur_time - $time_ago;

    $seconds    = $time_elapsed ;

    $minutes    = round($time_elapsed / 60 );

    $hours      = round($time_elapsed / 3600);

    $days       = round($time_elapsed / 86400 );

    $weeks      = round($time_elapsed / 604800);

    $months     = round($time_elapsed / 2600640 );

    $years      = round($time_elapsed / 31207680 );

    // Seconds

    if($seconds <= 60){

        return "just now";

    }

    //Minutes

    else if($minutes <=60){

        if($minutes==1){

            return "one minute ago";

        }

        else{

            return "$minutes minutes ago";

        }

    }

    //Hours

    else if($hours <=24){

        if($hours==1){

            return "an hour ago";

        }else{

            return "$hours hrs ago";

        }

    }

    //Days

    else if($days <= 7){

        if($days==1){

            return "yesterday";

        }else{

            return "$days days ago";

        }

    }

    //Weeks

    else if($weeks <= 4.3){

        if($weeks==1){

            return "a week ago";

        }else{

            return "$weeks weeks ago";

        }

    }

    

     else if($months <=12 || $years>=1){

       return $time_ago_date;

    }

    

    

//    //Months

//    else if($months <=12){

//        if($months==1){

//            return "a month ago";

//        }else{

//            return "$months months ago";

//        }

//    }

//    //Years

//    else{

//        if($years==1){

//            return "one year ago";

//        }else{

//            return "$years years ago";

//        }

//    }

    

    

}









	public function verification()



	{		



		if(!empty($_REQUEST['phone']) && !empty($_REQUEST['otp']) && !empty($_REQUEST['password']))



		{







			$userinfo = $this->common->getData('user',array('phone'=>$_POST['phone']),array('single'));



			if($_POST['otp'] != $userinfo['otp']){



				$this->response(false,"Wrong OTP entered. please try again.",array("userinfo" => $userinfo)); exit();



			}







			$password = md5($_POST['password']);



			$this->common->updateData('user',array('verified'=> '1','password'=> $password,'otp' => null),array('phone'=> $_POST['phone']));



		



			$message = "OTP verified successfully.";



			$this->response(true,$message,array("userinfo" => $userinfo));



		}



		else



		{



			$this->response(false,'Missing parameter');



		}



	}















	public function social_login()

	{		

		$user = $this->common->getData('user',array('social_id' => $_POST['social_id'],'singup_type'=>2),array('single'));

		$url = $this->input->post('user_image');

		$uimg = "";



		if($url != "")

		{

			$uimg = rand().time().'.png';

			file_put_contents('assets/userfile/profile/'.$uimg, file_get_contents($url));

		}



		if($user)

		{

			if($user['status'] == 0)

			{

				$this->response(false,'Your account are blocked by admin');

				die();

			}



			$old_device = $this->common->getData('user',array('ios_token' => $_POST['ios_token'],'android_token' => $_POST['android_token']),array('single','field'=>'id'));

			

			if($old_device)

			{

				$this->common->updateData('user',array('android_token' => "", "ios_token" => ""),array('id' => $old_device['id']));

			}

			

			$update = $this->common->updateData('user',array('user_image' => $uimg, 'ios_token' =>$_POST['ios_token'], 'android_token' => $_POST['android_token'],'user_latitude' => $_POST['user_latitude'],'user_longitude' => $_POST['user_longitude'],'address' => $_POST['address']),array('id' => $user['id']));

			

			if($update)

			{				

				if($user['user_image'] != "" && file_exists('assets/userfile/profile/'.$user['user_image']))

				{

					unlink('assets/userfile/profile/'.$user['user_image']);

				}

				

				$user['user_image'] = $uimg;

				if(!empty($user['user_image']))

				{

					$user['user_image'] = $image = base_url('/assets/userfile/profile/'.$user['user_image']);

				}

				else

				{

					$user['user_image']='';

				}

				if(!empty($_REQUEST['provider_id']))
				{
					$user['provider_id'] = $_REQUEST['provider_id'];
				}
				else
				{
					$user['provider_id'] = "";
				}	

				$this->response(true,"Login Successfully.",array("userinfo" => $user));

			}

			else

			{

				$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));

			}

		}

		else

		{	

			

			$insert = $this->common->insertData('user',array('social_id' => $_POST['social_id'],'user_image' => $uimg,'first_name' => $_POST['first_name'],'last_name'=>$_POST['last_name'],'android_token' => $_POST['android_token'],'ios_token' => $_POST['ios_token'],'user_latitude' => $_POST['user_latitude'],'user_longitude' => $_POST['user_longitude'],'address' => $_POST['address'],'email' => $_POST['email'],'singup_type' => 2,'status' => 1,'created_at' => Date('Y-m-d H:i:s')));

			

			$uid  = $this->db->insert_id();



			if($insert)

			{

				$user = $this->common->getData('user',array('id'=> $uid),array('single'));
				if(!empty($user['user_image']))
				{
					$user['user_image'] = base_url('/assets/userfile/profile/'.$user['user_image']);
				}
				else
				{
					$user['user_image'] = "";
				}


				$this->response(true,"Your Registration Successfully Completed.",array("userinfo" => $user));

			}

			else 

			{

				$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));

			}

		}

	}











	











	


















	public function getData1()



	{



		echo "hello" ;



	}







	public function getProfile()



	{



		if(!empty($_REQUEST['id']))



		{



			$id = $_REQUEST['id'];



			$result = $this->common->getData('user',array('id' => $id),array('single'));



			



			if($result['user_type'] == 2 || $result['user_type'] == 3)



			{



				if(!empty($result['image']))



				{



					$result['image'] = base_url('/assets/userfile/profile/'.$result['image']);



				}



				else



				{



					$result['image'] = "";



				}



				$rating = $this->rating_count($id);



				$category = $result['category'];



				if($category != "")



				{



					$category  = explode(",",$category);







					foreach($category as $cat)



					{



						$cat_info = $this->common->getData('category_tbl',array('category_id' => $cat),array('single')); 



						$category_data[] = array('category_id'=>$cat_info['category_id'],'category_name'=>$cat_info['category_name'],'category_image'=>$cat_info['category_image']);



					}







				



				}



				else



				{



					$category_data ="";



				}







				$count_user_no = $this->common->getData('wholesaler_rating',array('	wholesaler_id'=>$id),array('count'));



        	 		if($count_user_no)



        	 		{



        	 			$result['rating_user_no'] = $count_user_no;



        	 		}



        	 		else



        	 		{



        	 			$result['rating_user_no'] = 0;



        	 		}



        	 		



				$result['rating']= $rating;



				$result['category_info'] = $category_data;



				$other_info = $this->common->getData('other_user_info',array('user_id' => $result['id']),array('single'));



			}







			if(!empty($other_info))



			{



						$result['user_video'] =  $other_info['user_video'];







						if(!empty($other_info['user_video']))



						{



							$result['user_video'] = base_url('/assets/userfile/profile/'.$other_info['user_video']);



						}



						else



						{



							$result['user_video'] = "";



						}



						$result['get_percent'] =  $other_info['get_percent'];



						$result['min_price'] =  $other_info['min_price'];



						$result['max_price'] =  $other_info['max_price'];



						$result['expected_delivery_date'] =  $other_info['expected_delivery_date'];



						$result['dropship'] =  $other_info['dropship'];



			}



			



			if($result)



			{







				$this->response(true,'User found Successfully',array("userinfo" => $result));					



			}else{		



				$this->response(false,'User Not Found',array("userinfo" => ""));



			}



		}



		else



		{



			$this->response(false,'Missing parameter');



					



		}



	}







	















// 	function get_mysqli() { 



// $db = (array)get_instance()->db;



// return mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);}











	public function product_list_pdf()



	{



		if(!empty($_POST['wholesaler_id']))



		{



			$wholesaler_id = $_REQUEST['wholesaler_id'];



			







				$where="product_tbl.user_id = '" . $wholesaler_id . "'";



				$data['product_list'] = $this->common->get_record_join_two_table('product_tbl','category_tbl','	category','category_id','',$where,'product_tbl.product_id');



				$template = $this->load->view('template/product-list',$data,true);











				$apikey = 'd44f85bd-b9ae-4f91-94b6-ab5c1bc4d812';



				$value = $template;



				$result = file_get_contents("http://api.html2pdfrocket.com/pdf?apikey=" . urlencode($apikey) . "&value=" . urlencode($value));



 



			// Output headers so that the file is downloaded rather than displayed



			// Remember that header() must be called before any actual output is sent



			header('Content-Description: File Transfer');



			header('Content-Type: application/pdf');



			header('Expires: 0');



			header('Cache-Control: must-revalidate');



			header('Pragma: public');



			header('Content-Length: ' . strlen($result));



			 



			// Make the file a downloadable attachment - comment this out to show it directly inside the 



			// web browser.  Note that you can give the file any name you want, e.g. alias-name.pdf below:



			//  header('Content-Disposition: attachment; filename=' . 'alias-nametype.pdf' );



 



			// Stream PDF to user



			echo $result;



		}



		else



		{



			$this->response(false,'Missing parameter');



		}



	}







	public function share_room_passwrd()

	{

		if(!empty($_REQUEST['room_id']) && !empty($_REQUEST['user_id'])  && !empty($_REQUEST['share_user_id']))

		{

			$share_user_array = explode(",",$_REQUEST['share_user_id']);

			echo $string = "'".implode("','", $share_user_array)."'";

			

		}	

		else

		{

			$this->response(false,'Missing Parameter');	

		}

	}















	public function chat()

	{

		if(!empty($_REQUEST['user_from']) && !empty($_REQUEST['user_to']) && !empty($_REQUEST['type']))

		{

			$post['user_from'] = $_REQUEST['user_from'];

			$post['user_to'] = $_REQUEST['user_to'];

			$user_to = $_REQUEST['user_to'];

			$type = $_REQUEST['type'];



			if($type == 2)

			{

				$image = $this->common->do_upload_file('message','./assets/chat/');

				if(isset($image['upload_data']))

				{

					$msg_image = $image['upload_data']['file_name'];

					$msg = base_url('/assets/chat/'.$msg_image);

					$post['message']=$image['upload_data']['file_name'];

					$message_send_notification = base_url('/assets/chat/'.$msg_image);

					$message_type_notification = 1;

				}

				else

				{

					$this->response(false,'Missing parameter');

					exit();

				}

			}


			if($type == 3)

			{

				$post['comment'] = $_REQUEST['comment'];
				$post['created_at'] = date('Y-m-d H:i:s');
				$image = $this->common->do_upload_file('message','./assets/chat/');

				if(isset($image['upload_data']))

				{

					$msg_image = $image['upload_data']['file_name'];

					$msg = base_url('/assets/chat/'.$msg_image);

					$post['message']=$image['upload_data']['file_name'];

					$message_send_notification = base_url('/assets/chat/'.$msg_image);

					$message_type_notification = 1;

				}

				else

				{

					$this->response(false,'Missing parameter');

					exit();

				}

			}

			
			if($type == 1)
			{

				$message_user = $_REQUEST['message'];

				$message_user = $message_user;

				$post['message']  =  $message_user;

				$msg = $_REQUEST['message'];

				$message_send_notification = $msg;

				$message_type_notification = 2;

			}

			

			$post['created_at'] = date('Y-m-d H:i:s');

			$result = $this->common->insertData('chat',$post);

			$insert_id = $this->db->insert_id();



			if($result)

			{

				$message = "message sent successfully";

				$last_msg =  array("id" => $insert_id,

				"user_from" => $_POST['user_from'],

				"user_to" => $user_to,

				"message"=> $msg,

				"message_staus"=> $message_type_notification,

				"created_at" => $post['created_at']);





				// notification start



				$user_data_to = $this->common->getData('user',array('id'=>$user_to),array('single'));



				$user_data_from = $this->common->getData('user',array('id'=>$_REQUEST['user_from']),array('single'));



				$ios_token = $user_data_to['ios_token'];

				$android_token = $user_data_to['android_token'];

				$user_data_from_name = $user_data_from['username']; 

				$message_push = $user_data_from_name." Sent You a Message";

				$title = "chat";

				$type = "chat";



					
				$android_token ="";
				



				if($ios_token != ""){



					$messages_push = array("alert" => $title, "msg" => $message_push,"sound"=>"default","type" => $type,"message_send"=>$message_send_notification,"message_staus"=>$message_type_notification);	

					

					$this->push_iOS($ios_token,$messages_push);



					

				}

				else if($android_token != "")

				{

					

					$messages_push = array("title" => $title, "message" => $message_push, "type" => $type,"message_send"=>$message_send_notification,"last_msg"=>$last_msg);	



					$registatoin_id = array($android_token); 

					$this->send_notification($registatoin_id, $messages_push);



				}



				// notification end

		}

		else

		{

			$message = false;

		}



		if($message){



			$this->response(true,$message,array("last_msg" => $last_msg));		



		}else{



			$this->response(false,$message,array("last_msg" => $last_msg));		



		}		 	



		}



		else



		{



			$this->response(false,'Missing Parameter');	



		}



	}



	 











	public function get_event()



	{



		if(!empty($_REQUEST['get_type']) && !empty($_REQUEST['user_latitude']) && !empty($_REQUEST['user_longitude']))



        {







        	$user_latitude = $_REQUEST['user_latitude'];



        	$user_longitude = $_REQUEST['user_longitude'];



        	if($_REQUEST['get_type'] == 1)



        	{



        	







        		$where = 'SE.event_user_type = 1 AND SE.status = 0 AND SG.status = 0';



				$result = $this->common->get_eventList_by_lat($where,$user_latitude,$user_longitude);







        	}



        	else



        	{



        		$where = 'SE.status = 0 AND SG.status = 0';



        		$result = $this->common->get_eventList_by_lat($where,$user_latitude,$user_longitude);



        	}







        	



		$arr=array();



		$i=0;



		foreach ($result as $key => $value) {



			if(!empty($value['game_image']))



			{



				$game_image = base_url('/assets/Game/gamelogo/'.$value['game_image']);



			}



			else



			{



				$game_image = "";



			}











			if(!empty($value['event_image']))



			{



				$event_image = base_url('/assets/event/image/'.$value['event_image']);



			}



			else



			{



				$event_image = "";



			}



			



			



		$arr[$i]=array('id'=>$value['id'],'title'=>$value['title'],'game_id'=>$value['game_id'],'event_user_type'=>$value['event_user_type'],'event_time'=>$value['event_time'],'event_duration'=>$value['event_duration'],'event_participant_no'=>$value['event_participant_no'],'price'=>$value['price'],'event_description'=>$value['event_description'],'status'=>$value['status'],'game_name'=>$value['game_name'],'game_image'=>$game_image,'event_image'=>$event_image,'latitude'=>$value['latitude'],'longitude'=>$value['longitude'],'event_address'=>$value['event_address'],'distance'=>$value['distance']);







			if($value['event_user_type']==2)



				{



					$arr[$i]['user_id']=$value['user_id'];



					$userinfo = $this->common->getData('user',array('id'=>$value['user_id']),array('single'));















					$arr[$i]['user_name'] = $userinfo['name'];



					$arr[$i]['user_email'] = $userinfo['email'];



				



				}



				$i++;



		}







	



		if($result){



			$this->response(true,"Event fetch Successfully.",array("eventinfo" => $arr));			



		}else{



			$this->response(false,"There is a problem, please try again.",array("userinfo" => ""));



		}



		}



		else



		{



			$this->response(false,"Missing parameter");



		}			



		



		







	}



	





	public function group_chatlist()
	{
		$where = "user_from = '".$_POST['user_id']."'";
		$result = $this->common->getData('group_chat',$where,array('sort_by'=>'created_at','sort_direction' => 'desc'));
		$group = array();
		
		if(!empty($result))
		{			
			foreach ($result as $key => $value) 
			{					
				if (!in_array($value['group_id'], $group))
				{
				  	$group[] = $value['group_id'];
				}		
			}
		}



		if(!empty($group))
		{
			foreach ($group as $key => $value) 
			{
				$userinfo = $this->common->getData('event_tbl',array('id'=> $value),array('single'));
				
				$where_group = "group_id='".$value."'";
				$result_group = $this->common->getData('group_chat',$where_group,array('single','field' => 'message,comment,created_at,id,type,group_id,user_from','sort_by' =>'id' , 'sort_direction' => 'desc'));


				
				if($result_group['type'] == 1)
	        	{
	        		$message_status = $result_group['type'];
	        	}
				
				if($result_group['type'] == 2)
	        	{
	        		$result_group['message']=base_url('/assets/chat/'.$result_group['message']);	
	                $message_status = $result_group['type'];
	        	}

	        	if($result_group['type'] == 3)
	        	{
	        		$result_group['message']=base_url('/assets/chat/'.$result_group['message']);	
	                $message_status = $result_group['type'];
	        	}

				
				$group_chat_list[] = array('message'=>$result_group['message'],'created_at'=>$result_group['created_at'] ,'message_status' => $message_status,'id'=>$result_group['id'],'user_from' =>$result_group['user_from'],'group_id' =>$result_group['group_id'],'comment' =>$result_group['comment']);
				
			}


			
			$data_array_new = $this->array_sort($group_chat_list,'id', SORT_DESC);
			// create array in ascending order



			$user_group = $this->common->getData('accept_reject_tbl',array('user_id' => $_POST['user_id'],'status'=>1),array('','field' => 'event_id'));
			// all group id fetch 

			
			foreach ($user_group as $arr_user_group => $arr_group_value) 
			{
				foreach ($arr_group_value as $key => $value) 
				{
					$array_key_index[] = $value;
				}
				// merge index are single array 
			}


			


			foreach ($array_key_index as $key => $value) 
			{
				
				if(in_array($value, array_column($data_array_new, 'group_id'))) 
				{ 
	    			
				}
				else
				{
					$data_array_new[] = array('message'=>"",'created_at'=>"" ,'message_status' =>"",'id'=>"",'user_from' =>"",'group_id' =>$value,'comment' =>"");
				}
			}

			


			// check multidimensonal array and add event 
			
			$this->response(true,$data_array_new);
			
		}
		else
		{
			$this->response(true,array());	
		}
	}





	public function group_chatHistory()
	{
		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['group_id']))
		{
			$where="group_id = '". $_POST['group_id'] ."'";
			$result = $this->common->getData('group_chat',$where,array('sort_by'=>'created_at','sort_direction' => 'asc'));

			$where_check="event_id = '". $_POST['group_id'] ."' AND user_id = '". $_POST['user_id'] ."' AND status = 1";
			$result_check = $this->common->getData('accept_reject_tbl',$where_check,array('single'));

			// if(empty($result_check))
			// {
			// 	$this->response(false,'Please firstly accept event');
			// 	die();	
			// }
			
			

			if(!empty($result))
			{
				foreach($result as $value)
        		{
        			
        			if(!empty($value['message']))
					{
	        			if($value['type'] == 1)
	        			{
	        				
	                         $value['message_staus'] = $value['type'];
	        			}
						else if($value['type'] == 2)
	        			{
	        				 $value['message']=base_url('/assets/chat/'.$value['message']);	
	                         $value['message_staus'] = $value['type'];
	        			}
	        			else if($value['type'] == 3)
	        			{
	        				 $value['message']=base_url('/assets/chat/'.$value['message']);	
	                         $value['message_staus'] = $value['type'];
	        			}
	        			else
	        			{
	        				$value['message']="";	
	                        $value['message_staus'] ="";
	        			}
        			}
                    else
                    {
                        $value['message']="";	
                        $value['message_staus'] ="";
                    }

                    $user_info = $this->common->getData('user',array('id' => $value['user_from']),array('single'));

                    if($value['user_from'] == $_REQUEST['user_id'])
                    {
                    	$user_type = 1;
                    }
                    else
                    {
                    	$user_type = 2;
                    }

                    if(!empty($user_info['user_image']))
                 	{
                 		$user_image = base_url('/assets/userfile/profile/'.$user_info['user_image']);
                 	}
                 	else
                 	{
                 		$user_image = '';
                 	}

                   	$array_info=array('id'=>$value['id'],'user_from'=>$value['user_from'],'group_id'=>$value['group_id'],'message'=>$value['message'],'message_staus' => $value['message_staus'],'created_at'=>$value['created_at'],'first_name'=>$user_info['first_name'],'last_name'=>$user_info['last_name'],'user_image'=>$user_image,'user_type'=>$user_type,'comment'=>$value['comment']);
                   

                    $arr_chat[] = $array_info;
                }

                $this->response(true,'chat history fetch Successfully',array("chat_history" => $arr_chat));
        	}
        	else
        	{
        		$this->response(true,'chat not found',array("chat_history" => array()));
        	}
		}
		else
		{
			$this->response(false,'Missing Parameter');	
		}


	}






	function array_sort($array, $on, $order=SORT_ASC){



    $new_array = array();

    $sortable_array = array();



    if (count($array) > 0) {

        foreach ($array as $k => $v) {

            if (is_array($v)) {

                foreach ($v as $k2 => $v2) {

                    if ($k2 == $on) {

                        $sortable_array[$k] = $v2;

                    }

                }

            } else {

                $sortable_array[$k] = $v;

            }

        }



        switch ($order) {

            case SORT_ASC:

                asort($sortable_array);

                break;

            case SORT_DESC:

                arsort($sortable_array);

                break;

        }



        foreach ($sortable_array as $k => $v) {

            $new_array[$k] = $array[$k];

        }

    }



    return $new_array;

}
	public function chatHistory()

	{

		$where = '(user_from = '.$_POST['id'].' AND user_to = '.$_POST['uid'].') OR (user_from = '.$_POST['uid'].' AND user_to = '.$_POST['id'].')';



		$result = $this->common->getData('chat',$where,array('sort_by'=>'created_at','sort_direction' => 'asc'));

		

		if(!empty($result))

		{

			foreach($result as $value)

			{

				if(!empty($value['message']))

				{

					$msg = $value['message'];

					preg_match('/\.[^\.]+$/i',$msg,$ext);



					if(!empty($ext))

					{

						$ext = $ext[0];

					}

					else

					{

						$ext = "";

					}



					$type=Array(1 => '.jpg', 2 => '.jpeg', 3 => '.png', 4 => '.gif',5 => '.3gp',6 => '.mp4',7 => '.avi',8 =>'.wmv');



                    if(!(in_array($ext,$type)))

                    {

						$value['message']=$msg;	

						$value['message_staus'] = 2;

					}

					else 

					{

						$value['message']=base_url('/assets/chat/'.$msg);	

						$value['message_staus'] = 1;

					}

				}

				else

				{

					$value['message']="";	

					$value['message_staus'] ="";

				}



				$arr_chat[]=array('id'=>$value['id'],'user_from'=>$value['user_from'],'user_to'=>$value['user_to'],'message'=>$value['message'],'message_staus' => $value['message_staus'],'created_at'=>$value['created_at']);

			}

		

			$user = $this->common->getData('user',array('id' => $_POST['uid']),array('single','field' => 'username,user_image'));



			if($user)

			{

				$arr_chat = $arr_chat ? $arr_chat : array();

				if(!empty($user['user_image']))

				{

					$image = base_url('/assets/userfile/profile/'.$user['user_image']);



	            }

				else

				{

					$image = '';



	            }

				

				$this->response(true,$arr_chat,array("username" => $user['username'],"image" => $image));		



			}

			else

			{

				$this->response(false,array());		

			}	



		}

		else

		{

			$this->response(false,array());	

		}		 

	}









	public function block_user()
	{

		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['block_uid']))

		{

			 $user_id = $_REQUEST['user_id'];

			 $block_uid = $_REQUEST['block_uid'];

			 $status = $_REQUEST['status'];

				if ($status == 1) {

					if (!empty($block_uid)) {

					

						$where="user_id	='" . $user_id . "' AND block_uid ='" . $block_uid . "' ";

						$value = $this->common->getData('user_block',$where,array('single'));

						;

						

					if(empty($value))

						{	

							$insert = $this->common->insertData('user_block',array('user_id' => $user_id,'	block_uid' => $block_uid));



						

							$this->response(true,"Blocked");

							

						}

						else

						{

							$this->response(false,"block already added");

						}



					}

				}



				else

                {

                    

                    if (!empty($block_uid)) {

                    	$where="user_id	='" . $user_id . "' AND block_uid ='" . $block_uid . "' ";

                    	

                    	$value = $this->common->deleteData('user_block',$where);

                    	$this->response(true,"Unblocked");

                    					

                    }

                }

		}

		else

		{

			$this->response(false,"Missing Parameter.");

		}

	}













	



	public function contactUs()



	{



		$message = '<h4>'.$_POST['name'].'</h4><p>'.$_POST['message'].'</p>';



		$mail = $this->common->sendMail('devendra@mailinator.com','Contact Us',$message,array('fromEmail'=>$_POST['email']));



		$mail_msg = $mail ? 'Email send successfully' : 'Email not send. Please send again';



		$this->response($mail,$mail_msg);	



	}



	



	public function report()



	{		



		$_POST['created_at'] = date('Y-m-d H:i:s');



		$post = $this->common->getData('post',array('id' => $_POST['post_id']),array('single'));



		$user = $this->common->getData('user',array('id'=> $post['uid']),array('single','field'=>'email,name'));



		$post1 = $this->common->getField('report',$_POST);



		$report = $this->common->insertData('report',$post1);



		$mail = false;



		if($report){



			//$this->checkMail();



			$message = "Hello Administrator <br> One post <a href='".base_url('api/postDetail/'.$post['id'])."'>".$post['title']."</a> is reported. We will delete your post if found inappropriate. <br>".$_POST['comment'];







			$mail = $this->common->sendMail("info@positivenetwork.com.au",'Report on your post',$message);



		}



		$response = $this->response($mail,"Reported Successfully");		



	}



	



}