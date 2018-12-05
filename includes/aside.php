<aside id="aside_ID">

            <?php 
            if(logged_in()===true)
            {
                include 'includes/widget/loggedin.php';
            }
            else
            include 'includes/widget/login.php';
            include 'includes/widget/user_count.php';
            ?> 
 </aside> 