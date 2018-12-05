<?php

function admin_protect()
{
    global $user_data;
    if (has_access($user_data['user_id'], 1)===false)
    {
        header('Location:index.php');
    }
}

function email($to, $subject,$body)
{
    mail($to, $subject, $body, 'From:hello@gmail.com');    
}

function logged_in_redirect()
{
    if(logged_in()===true)
    {
        header('Location:index.php');
        exit();
    }
}

function protect_page()
{
    if(logged_in()===false)
    {
        header('Location:protected.php');
        exit();
    }
}

function array_sanitize(&$item)
{
       global $connection;
       $item=htmlentities(strip_tags(mysqli_real_escape_string($connection, $item)));
}

function  sanitize($data)
{
    global $connection;
    return htmlentities(strip_tags(mysqli_real_escape_string($connection, $data)));
}

function output_errors($errors)
{
    return '<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
}

?>