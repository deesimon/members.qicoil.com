<?php
// session_start();
include('array.php');
include('constants.php');
//print_r($_POST);
if (!empty($_POST)) {
	if ($_POST['login'] == 1) {
		$email = $_POST['email'];
		$fname = $_POST['fname'];
		$password = $_POST['password'];
		$header = array('Content-Type: application/x-www-form-urlencoded');
		$url=LOGIN_URL;
		$post_data = http_build_query(array('email' => $email, 'password' => $password));
		$res = curl_post($url, $post_data, $header);
		// echo '<br><br><b>URL:</b> ' . $url . "<br>\n\n<br/><b>DATA:</b> " . $post_data . "<br>\n\n<br/><b>RES:</b><br/>";
		// print_r($res['res']);
		// die;

		$response = json_decode(($res['res']));
		// print_r($response);
		// die;
		//   $flag=$response->user[0]->fetch_flag;
		$id = $response->user[0]->id;
		$name = $response->user[0]->name;
		$email = $response->user[0]->email;
		$payStatus = $response->user[0]->is_subscribe;
		$is_verified = $response->user[0]->is_verified;
		$category_ids = explode(",", $response->user[0]->category_ids);
		$subcategory_ids = explode(",", $response->user[0]->subcategory_ids);
		$album_ids = explode(",", $response->user[0]->album_ids);
		//$password=$response->user[0]->password;

		// echo'</br> </br>';
		// echo $flag.'</br>'.$id.'</br>'.$name.'</br>'.$email;

		// echo'</br> </br>';
		if ($response->user[0]->fetch_flag == 1) {
			$_SESSION['id'] = $id;
			$_SESSION['email'] = $email;
			$_SESSION['name'] = $name;
			$_SESSION['token'] = $response->user[0]->token;
			$_SESSION['payStatus'] = $payStatus;
			$_SESSION['category_ids'] = $category_ids;
			$_SESSION['subcategory_ids'] = $subcategory_ids;
			$_SESSION['album_ids'] = $album_ids;
			$_SESSION['verified'] = $is_verified;
			if (isset($_COOKIE['backurl'])) {
				header("Location:" . $_COOKIE['backurl']);
			} else {
				header("Location:starter-frequencies.php");
			}

			//echo $email.'</br>';

			//echo "Login Success";

		} else {
			// print_r($response);
			$_SESSION['err'] = $response->user[0]->rsp_msg;
			// print_r($_SESSION);die;
			header("Location:index.php");
		}
		exit;
	}

	if ($_POST['register'] == 1) {

		$email = $_POST['email'];
		$name = $_POST['name'];
		$password = $_POST['password'];
		$confirm_password = $_POST['password'];
		$dateofbirth = $_POST['dateofbirth'];
		$gender = $_POST['gender'];
		//  print_r($_POST);die;
		$header = array('Content-Type: application/x-www-form-urlencoded');
		$url=REGISTER_URL;
		$post_data = http_build_query(array('name' => $name, 'email' => $email, 'password' => $password, 'confirm_password' => $confirm_password, 'dateofbirth' => $dateofbirth, 'gender' => $gender));
		$res = curl_post($url, $post_data, $header);
		//echo $url.'?'.$post_data;
		// print_r($res);exit;
		$response = json_decode(($res['res']));

		//   $flag=$response->user[0]->fetch_flag;
		$id = $response->user[0]->id;
		$name = $response->user[0]->name;
		$email = $response->user[0]->email;
		$gender = $response->user[0]->gender;
		$dateofbirth = $response->user[0]->radio;
		$confirm_password = $response->user[0]->confirm_password;
		if ($response->user[0]->fetch_flag == 1) {
			$_SESSION['id'] = $id;
			$_SESSION['email'] = $email;
			$_SESSION['password'] = $password;
			$_SESSION['name'] = $name;
			$_SESSION['token'] = $response->user[0]->token;
			$_SESSION['verified'] = 0;
			//header("Location:starter-frequencies.php");
			header("Location: thankyou_signup.php");
		} else {
			$_SESSION['err'] = "Email Already Registered"; //print_r($_SESSION);exit;
			header("Location:register.php");
			exit;
		}
	}

	if ($_POST['forgot_pw'] == 1) {
		// print_r($_POST);
		// die;
		if (!empty($_POST['email'])) {
			$email = $_POST['email'];
			$header = array('Content-Type: application/x-www-form-urlencoded');
			$url=FORGOT_PW_URL;
			$post_data = http_build_query(array('email' => $email));
			$res = curl_post($url, $post_data, $header);
			// print_r($res);
			// exit;
			$response = json_decode(($res['res']));
			//  print_r($response);
			//  exit;
			$flag = $response->user[0]->fetch_flag;
			$msg = $response->user[0]->rsp_msg;
			if ($response->user[0]->fetch_flag == 1) {
				$_SESSION['success'] = $msg;
			} else {
				$_SESSION['err'] = $msg;
			}
			header("Location:forgot.php");
			die;
		}
		header("Location:index.php");
		die;
	}
	if ($_POST['change_pw'] == 1) {
		// print_r($_POST);
		// die;
		if (!empty($_POST['id']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {
			$id = $_POST['id'];
			$password = $_POST['password'];
			$confirm_password = $_POST['confirm_password'];
			$header = array('Content-Type: application/x-www-form-urlencoded');
			$url=CHANGE_PW_URL;
			$post_data = http_build_query(array('id' => $id, 'password' => $password, 'confirm_password' => $confirm_password));
			$res = curl_post($url, $post_data, $header);
			// print_r($res);
			// exit;
			$response = json_decode(($res['res']));
			// print_r($response);
			// exit;
			$flag = $response->user[0]->fetch_flag;
			$msg = $response->user[0]->rsp_msg;
			if ($response->user[0]->fetch_flag == 1) {
				$_SESSION['success'] = $msg;
			} else {
				$_SESSION['err'] = $msg;
			}
			header("Location:index.php");
			die;
		}
		header("Location:index.php");
		die;
	}
	if ($_POST['update_pw'] == 1) {
		// print_r($_POST);
		// die;
		$password = $_POST['password'];
		$newpassword = $_POST['newpassword'];
		$reapetpassword = $_POST['reapetpassword'];

		$url=PROFILE_UPDATEPASSWORD_URL;
		$post_data = http_build_query(array('password_old' => $password, 'password' => $newpassword, 'password_confirmation' => $reapetpassword));
		$header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
		$res = curl_post($url, $post_data, $header);

		$response = json_decode(($res['res']));
		// print_r($response);//die;

		$flag = $response->user[0]->fetch_flag;
		$msg = $response->user[0]->rsp_msg;
		if ($flag == 1) {
			$_SESSION['success'] = $msg;
		} else {
			$_SESSION['err'] = $msg;
		}
		header("Location:changepassword.php");
		die;
	}

	if ($_POST['playlist'] == 1) {
		// print_r($_POST);
		// die;
		if ($_POST['method'] == 'add') {
			$url=SAVEPLAYLIST_URL;
			$post_data = http_build_query(array('name' => $_POST['playlist_name']));
			$header = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['token']);
			$res = curl_post($url, $post_data, $header);
			$response = json_decode($res['res']);
			// print_r($res);//die;
			if ($response->playlist[0]->fetch_flag == 1) {
				$return = array('success' => true);
			} else {
				$return = array('success' => false);
			}
		} elseif ($_POST['method'] == 'remove_playlist') {
			$url = REMOVE_PLAYLIST_URL;
			$post_data = http_build_query(array('playlist_id' => $_POST['playlist_id']));
			$header = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['token']);
			$res = curl_post($url, $post_data, $header);
			$response = json_decode($res['res']);
			// print_r($res);die;
			if ($response->playlist[0]->fetch_flag == 1) {
				$return = array('success' => true, 'msg' => $response->playlist[0]->rsp_msg);
			} else {
				$return = array('success' => false, 'msg' => $response->playlist[0]->rsp_msg);
			}
		} elseif ($_POST['method'] == 'add_frequency') {
			$url=ADD_FREQUENCY_TO_PLAYLIST_URL;
			$post_data = http_build_query(array('playlist_id' => $_POST['playlist_id'], 'frequency_id' => $_POST['frequency_id']));
			$header = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['token']);
			$res = curl_post($url, $post_data, $header);
			$response = json_decode($res['res']);
			// print_r($res);die;
			if ($response->playlist[0]->fetch_flag == 1) {
				$return = array('success' => true, 'msg' => $response->playlist[0]->rsp_msg);
			} else {
				$return = array('success' => false, 'msg' => $response->playlist[0]->rsp_msg);
			}
		} elseif ($_POST['method'] == 'remove_frequency') {
			$url=REMOVE_FREQUENCY_TO_PLAYLIST_URL;
			$post_data = http_build_query(array('playlist_id' => $_POST['playlist_id'], 'frequency_id' => $_POST['frequency_id']));
			$header = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['token']);
			$res = curl_post($url, $post_data, $header);
			$response = json_decode($res['res']);
			// print_r($res);die;
			if ($response->playlist[0]->fetch_flag == 1) {
				$return = array('success' => true, 'msg' => $response->playlist[0]->rsp_msg);
			} else {
				$return = array('success' => false, 'msg' => $response->playlist[0]->rsp_msg);
			}
		} else {
			$return = array('success' => false);
		}
		echo json_encode($return);
		die;
	}

	if ($_POST['favorite'] == 1) {
		$header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
		$data = http_build_query(array('is_favorite' => $_POST['is_favorite'], 'frequency_id' => $_POST['albumid']));
		$url=FAVORITE_SAVE_URL;
		$res = curl_post($url, $data, $header);
		// print_r($res['res']);die;
		$response = json_decode($res['res']);
		if ($response->favorite[0]->fetch_flag) {
			$return = array('success' => true);
		} else {
			$return = array('success' => false);
		}
		echo json_encode($return);
		die;
	}
	if ($_POST['update_name'] == 1) {
		// print_r($_POST);
		// die;
		$name = $_POST['name'];
		$url = "https://apiadmin.qienergy.ai/api/user/profile/updatename";
		$post_data = http_build_query(array('name' => $name));
		$header = array('Authorization: Bearer ' . $_SESSION['token'], 'Content-Type: application/x-www-form-urlencoded');
		$res = curl_post($url, $post_data, $header);

		$response = json_decode(($res['res']));
		// print_r($response);//die;
		$flag = $response->user[0]->fetch_flag;
		$msg = $response->user[0]->rsp_msg;
		if ($flag == 1) {
			$_SESSION['success'] = $msg;
			$_SESSION['name'] = $name;
		} else {
			$_SESSION['err'] = $msg;
		}
		header("Location:profile.php");
		die;
	}

} 
else {
	header("Location:index.php");
	die;
}
