<?php
include 'core/init.php';
logged_in_redirect();
include 'includes/overall/header.php';
if (isset($_GET['success']) && empty($_GET['success']))
{
    ?>
    <h2>Thanks, we have activated your account!!</h2>
    <p>You're free to login!!</p>
    <?php 
}
else 
{
    if (isset($_GET['email']) && $_GET['email_code'])
    {
        $email=trim($_GET['email']);
        $email_code=trim($_GET['email_code']);
        if (email_exists($email)===false)
        {
            $errors[]='Oops, something went wrong and we can\'t find that email address!!';
        }
        else if (activate($email, $email_code)===false)
        {
            $errors[]='We had problems activating your acoount!!';
        }
        
        if (empty($errors)===false)
        {
            ?>
            <h2>Oops ...</h2><br>
            <?php 
            echo output_errors($errors);
        }
        else
        {
            header('Location:activate.php?success');
            exit();
        }
        
        
    }
}
include 'includes/overall/footer.php';
?>