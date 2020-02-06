<?php
// Initialisation
$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

if (strlen($username) > 0 && strlen($password) > 0) {
    //$loggedUser = $userController->loginWithNickname($username, $password);
    $loggedUser = $userController->Login(['userNickname' => $username, 'userPwd' => $password]);


  if ($loggedUser !== null) {
    $_SESSION["loggedUser"] = $loggedUser;
    $_SESSION['loggedIn'] = true;

    echo json_encode([
      'ReturnCode' => 0,
      'Success' => "Login is correct"
    ]);
    exit();
  }

  echo json_encode([
    'ReturnCode' => 2,
    'Error' => "Username/Password invalid"
  ]);
}
