<?php

include_once('./models/Comments.php');
include_once('./helpers/CsrfHelpers.php');

use helpers\CsrfHelper;
use models\Comments;

if(is_file(__DIR__.'/config/db.php')) define('CONFIG', ['db'=> require_once (__DIR__.'/config/db.php')]);

session_start();
$csrf_token = CsrfHelper::getOrSetSessionToken();

$flashes = [];
if (isset($_SESSION['flash'])) {
    $flashes = $_SESSION['flash'];
    unset($_SESSION['flash']);
}


$comments = Comments::getAll();

/**Controller**/
$form = 'CommentsForm';
$comment = new Comments();

if (isset($_REQUEST[$form])) {
    if (isset($_REQUEST['csrf_token']) && $_REQUEST['csrf_token'] === $csrf_token) {
        if ($comment->setAttributes($form) && $comment->validate()) {
            $comment->save();
            header('location: http://'.$_SERVER['HTTP_HOST'].'/index.php');
        };
    }
}

$csrf_token = CsrfHelper::generateToken();

/**--Controller--**/

include_once('./views/view.php');
