<div class="widget">
		
                 <h2>Hello, <?php echo $user_data['first_name']; ?>!</h2>
                <div class="inner">
                
                <div class="profile">
		<?php 
		
		if (isset($_FILES['profile']))
		{
		    if (empty($_FILES['profile']['name'])===true)
		    {
		        echo 'Please choose a file!!';
		    }
		    else {
		        $allowed=array('jpg','jpeg','gif','png');
		        $file_name=$_FILES['profile']['name'];
		        $file_extn=explode('.', $file_name);
		        $file_extn=strtolower(end($file_extn));
		        $file_temp=$_FILES['profile']['tmp_name'];
		        if (in_array($file_extn, $allowed))
		        {
		            change_profile_image($session_user_id, $file_temp, $file_extn);
		            header('Location:'.$current_file);
		            exit();
		        }
		        else 
		        {
		            echo 'Incorrect file type!!<br> Allowed :';
		            echo implode(', ', $allowed);
		        }
		    }
		}
                if(empty($user_data['profile'])===false)
                {
                    ?>
                    <img alt="<?php echo $user_data['first_name']?>'s Profile Image" src="<?php echo $user_data['profile']?>">
                <?php 
                }
			?>
			<form action="" method="post" enctype="multipart/form-data">
			<input type="file" name="profile"><br>
			<input type="submit" value="Submit">
		</form>
		</div>
               <ul>
                <li><a href="<?php echo $user_data['username']; ?>">Profile</a></li>
               
               <li><a href="changepassword.php">Change Password</a></li>
               <li><a href="setting.php">Settings</a></li>
              <li><a href="logout.php">Logout</a></li>
                </ul>  
				</div>
</div>