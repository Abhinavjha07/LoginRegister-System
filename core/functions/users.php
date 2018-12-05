<?php

function change_profile_image($user_id,$file_tmp,$file_extn)
{
    global $connection;
    $file_name=substr(md5(time()), 0,10).'.'.$file_extn;
    $file_path='images/profile/'.$file_name;
    move_uploaded_file($file_tmp, $file_path);
    mysqli_query($connection, "Update `users` set `profile`='".$file_path."' where `user_id`=".(int)$user_id);
}

function mail_users($subject,$body)
{
    global $connection;
    $query=mysqli_query($connection, "Select `email`,`first_name` from `users` where `allow_email`=1");
    while (($row=mysqli_fetch_assoc($query))!==false)
    {
        email($row['email'], $subject, "Hello ".$row['first_name']." ,\n\n".$body);
    }
}

function has_access($user_id,$type)
{
    global $connection;
    $type=(int)$type;
    $user_id=(int)$user_id;
    $query=mysqli_query($connection, "Select count(`user_id`) from `users` where `user_id`=$user_id and `type`=$type");
    $result=mysqli_fetch_row($query);
    return ($result[0]==1)?true:false;
}

function recover($mode,$email)
{
    $mode=sanitize($mode);
    $email=sanitize($email);
    $user_data=user_data(user_id_from_email($email),'user_id','first_name','username');
    if($mode='username')
    {
        email($email, 'Your username', "Hello ".$user_data['first_name']." ,\n\n Your username is <strong>".$user_data['username']."</strong>\n\nmyWebsite.org");
    }
    else if ($mode=='password') {
        $generated_password=substr(md5(rand(9999,999999)), 0,8);
        change_password($user_data['user_id'], $generated_password);
        update_user($user_data['user_id'], array('password_recover'=>'1'));
        //email($email, 'Your password recovery', "Hello ".$user_data['first_name']." ,\n\n Your new password is <strong>".$generated_password."</strong>\n\nPlease login using this password to change your password.\n\nmyWebsite.org");
    }
}

function update_user($user_id,$update_data)
{
    global $connection;
    $update=array();
    array_walk($update_data, 'array_sanitize');
    foreach ($update_data as $field=>$data)
    {
        $update[]='`'.$field.'`=\''.$data.'\'';
    }
    
    mysqli_query($connection, "Update `users` set ".implode(', ', $update)." where `user_id`=".$user_id);
}

function activate($email,$email_code)
{
    global $connection;
    $email=mysqli_real_escape_string($connection, $email);
    $email_code=mysqli_real_escape_string($connection,$email_code);
    if($query=mysqli_query($connection, "Select count(`user_id`) from `users` where `email`='$email' and `email_code`='$email_code' and `active`=0")) 
    {
        $result=mysqli_fetch_row($query);
        if($result[0]==1)
        {
            mysqli_query($connection, "Update `users` set `active`=1 where `email`='$email'");
            return true;
        }
        else return false;
    }
}

function change_password($user_id,$password)
{
    global $connection;
    $user_id=(int)$user_id;
    $password=md5($password);
    mysqli_query($connection, "Update `users` set `password`='$password', `password_recover`=0 where `user_id`=$user_id");
}

function user_count()
{
    global $connection;
    $result=mysqli_fetch_row(mysqli_query($connection, "Select count(`user_id`) from `users` where `active`=1"));
    return $result[0];
}

function  user_data($user_id)
{
    global $connection;
    $data=array();
    $user_id=(int)$user_id;
    $func_num_args=func_num_args();
    $func_get_args=func_get_args();
    if($func_num_args>1)
    {
        unset($func_get_args[0]);
        $fields='`'.implode('`,`', $func_get_args).'`';
        $data=mysqli_fetch_assoc(mysqli_query($connection, "Select $fields from `users` where `user_id`=$user_id"));
        return $data;
    }
}

function logged_in()
{
    return (isset($_SESSION['user_id']))?true:false;
}

function  user_exists($username)
{
    global $connection;
   
    $username=sanitize($username);
    $query=mysqli_query($connection , "Select count(`user_id`) from `users` where `username`='$username'");
    $result=mysqli_fetch_row($query); 
    $result=$result[0];
    return ($result==1)?true:false;
}

function user_active($username)
{
    global $connection;
    $username=sanitize($username);
    $query=mysqli_query($connection, "Select count(`user_id`) from `users` where `username`='$username' and `active`=1");
    $result=mysqli_fetch_row($query);
    $result=$result[0];
    
    return ($result==1)?true:false;
}


function user_id_from_username($username)
{
    global $connection;
    $username=sanitize($username);
    $query=mysqli_query($connection, "Select `user_id` from `users` where `username`='$username'");
    $result=mysqli_fetch_row($query);
    
    return $result[0];
}


function user_id_from_email($email)
{
    global $connection;
    $email=sanitize($email);
    $query=mysqli_query($connection, "Select `user_id` from `users` where `email`='$email'");
    $result=mysqli_fetch_row($query);
    
    return $result[0];
}

function login($username, $password)
{
    global $connection;
    $user_id=user_id_from_username($username);
    $username=sanitize($username);
    $password=md5($password);
    $query=mysqli_query($connection, "Select count(`user_id`) from `users` where `username`='$username' and `password`='$password'");
    $result=mysqli_fetch_row($query);
    return ($result[0]==1)?$user_id:false;
}

function  email_exists($email)
{
    global $connection;
    $email=sanitize($email);
    $query=mysqli_query($connection , "Select count(`user_id`) from `users` where `email`='$email'");
    $result=mysqli_fetch_row($query);
    $result=$result[0];
    return ($result==1)?true:false;
}

function register_user($register_data)
{
    global $connection;
    array_walk($register_data, 'array_sanitize');
    $register_data['password']=md5($register_data['password']);
    
    $fields='`'.implode('`,`', array_keys($register_data)).'`';
    $data='\''.implode('\',\'', $register_data).'\' ';
    
    mysqli_query($connection, "Insert into `users` ($fields) values ($data)");
    //email($register_data['email'], 'Activate your account.', "Hello".$register_data['first_name'].",You need to activate your account, so use the link below:\n\nhttp://localhost/PHP_Practice/LoginRegister/activate.php?email=".$register_data['email']."&email_code=".$register_data['email_code']."\n\n-myWebsite.org");
}

?>
 