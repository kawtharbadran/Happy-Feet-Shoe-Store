<?php

//resume/start session
session_start();

// destroy everything in this session
session_unset();
session_destroy();

//redirect to index.php
header('Location: ../index.php');
exit;
?>