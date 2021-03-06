<?php
/*
*     Author              :  Fujise Thomas, Hoarau Nicolas.
*     Project             :  AList.
*     Page                :  Function.php.
*     Brief               :  Function page for the web application.
*     Starting Date       :  05.02.2020.
*/
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/dbConnect.php';
//require_once __DIR__ . '/tMailer.php';

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ LOGGED FUNCTION ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
/**
 * @author Hoarau Nicolas
 * 
 * Check if the user is logged
 * 
 * @return bool
 */
function IsLogged()
{
  $isLogged = false;

  if (array_key_exists('loggedIn', $_SESSION)) {
    if ($_SESSION['loggedIn'] == true) {
      $isLogged = true;
    }
  }

  return  $isLogged;
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ LOGIN FUNCTIONS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
/**
 * @author Hoarau Nicolas
 * 
 * @brief function for user login in database
 *
 * @param string $mail
 * @param string $password
 * 
 * @return array || null
 */
function Login($mail, $password)
{
  $query = "SELECT idUser, email, username FROM t_user WHERE email = :email  AND password = :userPwd";

  $password = sha1($mail . $password);

  try {
    $requestLogin = EDatabase::getDb()->prepare($query);
    $requestLogin->bindParam(':email', $mail, PDO::PARAM_STR);
    $requestLogin->bindParam(':userPwd', $password, PDO::PARAM_STR);
    $requestLogin->execute();

    $result = $requestLogin->fetch(PDO::FETCH_ASSOC);

    return $result != false ? $result : false;
  } catch (PDOException $e) {
    $e->getMessage('Error while login', $e::getMessage());

    return null;
  }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ REGISTER FUNCTIONS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
/**
 * @author Thomas Fujise
 * 
 * @brief function for user registration in database
 *
 * @param string $nickname
 * @param string $mail
 * @param string $pwd
 */
function registerUser($nickname, $email, $pwd, $logo = "logo.png", $activated = 1, $role = 1)
{
  //$sql = "INSERT INTO t_user(NICKNAME, EMAIL, ACTIVATION, STATE, PASSWORD,ROLE, EMAIL_TOKEN) VALUES(:nickname,:email,:activation,:state,:password,:role,:emailToken)";
  $sql = "INSERT INTO t_user(username, password, email, logo, email_token, activated, idRole) VALUES(:username, :password, :email, :logo, :emailToken, :activated, :role)";

  $req = EDatabase::getDb()->prepare($sql);
  $token = sha1($email . microtime());
  $req->bindParam(':username', $nickname, \PDO::PARAM_STR);
  $req->bindParam(':password', $pwd, \PDO::PARAM_STR);
  $req->bindParam(':email', $email, \PDO::PARAM_STR);
  $req->bindParam(':logo', $logo, \PDO::PARAM_STR);
  $req->bindParam(':emailToken', $token, \PDO::PARAM_STR);
  $req->bindParam(':activated', $activated, \PDO::PARAM_INT);
  $req->bindParam(':role', $role, \PDO::PARAM_INT);
  $req->execute();
  //$send = TMailer::sendMail(array($email),$nickname, $token);
}

/**
 * @author Thomas Fujise
 * 
 * @brief function for email token verification
 *
 * @param string $token user's token for activation
 * @return int id user else false
 */
function verifyToken($token)
{
  $sql = "SELECT idUser FROM t_user WHERE email_token = :token";
  $req = EDatabase::getDb()->prepare($sql);
  $req->bindParam(':token', $token, \PDO::PARAM_STR);
  $req->execute();

  if ($req->rowCount() == 1) {
    $idUser = $req->fetch();
    return $idUser;
  } else {
    return false;
  }
}

/**
 * @author Thomas Fujise
 * 
 * @brief function for account activation
 *
 * @param integer $id user id
 */
function activateAccount($id)
{
  $sql = "UPDATE t_user SET ACTIVATED = 1 WHERE idUser = :idUser";
  $req = EDatabase::getDb()->prepare($sql);
  $req->bindParam('idUser', $id[0], \PDO::PARAM_INT);
  $req->execute();
}

/**
 * @author Hoarau Nicolas
 * 
 * @brief Check if the mail is already use
 *
 * @param string $mail
 * @return boolean
 */
function MailAlreadyUsed(string $mail): bool
{
  $query = <<<EOT
SELECT email FROM t_user WHERE email = :email;
EOT;

  try {
    $checkMail = EDatabase::getDb()->prepare($query);

    $checkMail->bindParam(':email', $mail, PDO::PARAM_STR);
    $checkMail->execute();

    $result = $checkMail->fetch(PDO::FETCH_ASSOC);

    return $result == false ? $result : true;
  } catch (Exception $e) {
    throw $e->getMessage();
  }
}
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ HOME DISPLAY FUNCTIONS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
/**
 * @author Thomas Fujise
 * 
 * @brief function for get all anime in the database
 *
 * @return string html to display in index page
 */
function ShowAllAnime()
{
  $sql = <<<EX
SELECT idAnime, name, avgNote, addDate, cover, description 
FROM t_anime
EX;
  try {
    $req = EDatabase::getDb()->prepare($sql);
    $req->execute();
    $animes = $req->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $e->getMessage('Error while login', $e->getMessage());
    return null;
  }

  $message = <<<EOT
<div class="row mx-auto" style="width: 80%;" >
EOT;
  for ($i = 0; $i < count($animes); $i++) {
    // filtrage des données de la bdd
    $animes[$i]['idAnime'] = filter_var($animes[$i]['idAnime'], FILTER_SANITIZE_NUMBER_INT);
    $animes[$i]['name'] = filter_var($animes[$i]['name'], FILTER_SANITIZE_STRING);
    $animes[$i]['avgNote'] = filter_var($animes[$i]['avgNote'], FILTER_SANITIZE_NUMBER_INT);
    $animes[$i]['description'] = filter_var($animes[$i]['description'], FILTER_SANITIZE_STRING);

    $cover = GetCoverAnime($animes[$i]['idAnime']);
    $message .= <<<EOT
<div class="col-md-4">
<h2>{$animes[$i]['name']}</h2>
<p><img src="data:image/bmp;base64,{$cover}"/></p><p>{$animes[$i]['description']}</p>
<p><a class="btn btn-secondary" href="./anime.php?idAnime={$animes[$i]['idAnime']}" role="button">View details &raquo;</a></p>
</div>
EOT;
    if (($i + 1) % 3 == 0) {
      $message .= <<<EOT
</div>
<div class="row mx-auto" style="width: 80%;">
EOT;
    }
  }
  $message .= <<<EOT
</div>
EOT;
  return $message;
}
/**
 * @author Thomas Fujise
 * 
 * @brief function to get Cover from the anime
 *
 * @param int id anime
 * @return string html to display in index page
 */
function GetCoverAnime($idAnime)
{
  $sql = <<<EOT
SELECT cover FROM t_anime WHERE idAnime = :idAnime
EOT;
  try {
    $req = EDatabase::getDB()->prepare($sql);
    $req->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
    $req->execute();
    $result = $req->fetchAll();
    return  base64_encode($result[0]['cover']);
  } catch (PDOException $e) {
    $e->getMessage('Error while login', $e->getMessage());
    return null;
  }
}

/**
 * @author Hoarau Nicolas
 * 
 * @brief Get anime data from database with the id of the anime and is an user id is gave get users score too
 *
 * @param integer $idAnime
 * @param integer $idUser
 * @return array
 */
function GetAnimeData(int $idAnime, int $idUser = null): array
{
  if ($idUser != null) {
    $query = "SELECT a.name, a.avgNote, a.cover, a.description, 
        (SELECT note FROM t_library WHERE idUser = :idUser AND idAnime = :idAnime) AS userScore
      FROM t_anime AS a 
      WHERE a.idAnime = :idAnime;";

    try {
      $requestGetAnime = EDatabase::getDb()->prepare($query);
      $requestGetAnime->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
      $requestGetAnime->bindParam(':idUser', $idUser, PDO::PARAM_INT);
      $requestGetAnime->execute();

      $result = $requestGetAnime->fetch(PDO::FETCH_ASSOC);

      // filtrage des données de la bdd
      $result['name'] = filter_var($result['name'], FILTER_SANITIZE_STRING);
      $result['avgNote'] = filter_var($result['avgNote'], FILTER_SANITIZE_NUMBER_INT);
      $result['description'] = filter_var($result['description'], FILTER_SANITIZE_STRING);
      $result['cover'] = base64_encode($result['cover']);

      return $result;
    } catch (PDOException $e) {
      throw $e->getMessage();
    }
  } else {
    $query = "SELECT a.name, a.avgNote, a.cover, a.description FROM t_anime AS a WHERE a.idAnime = :idAnime";

    try {
      $requestGetAnime = EDatabase::getDb()->prepare($query);
      $requestGetAnime->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
      $requestGetAnime->execute();

      $result = $requestGetAnime->fetch(PDO::FETCH_ASSOC);

      $result['cover'] = base64_encode($result['cover']);
      return $result;
    } catch (PDOException $e) {
      throw $e->getMessage();
    }
  }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ SCORE ANIME FUNCTIONS ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function AlredyScored(int $idAnime, int $idUser)
{
  try {
    $query = <<<EOT
SELECT note FROM t_library WHERE idAnime = :idAnime AND idUser = :idUser;
EOT;

    $getAnimeScore = EDatabase::getDb()->prepare($query);
    $getAnimeScore->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
    $getAnimeScore->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    $getAnimeScore->execute();

    $result = $getAnimeScore->fetch(PDO::FETCH_ASSOC);

    return $result != false ? true : false;
  } catch (Exception $e) {
    throw $e;
  }
}

function UpdateAnimeAvgNote(int $idAnime, int $avgNote) : bool
{
  $sql = "UPDATE t_anime SET avgNote = :avgNote WHERE idAnime = :idAnime";

  try {
    $req = EDatabase::getDb()->prepare($sql);
    $req->bindParam(':avgNote', $avgNote);
    $req->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
    $req->execute();

    return true;
  } catch (PDOException $e) {
    throw $e->getMessage();
  }
}

/**
 * @author Hoarau Nicolas
 *
 * Get all score of an anime
 * 
 * @param integer $idAnime
 * @return array
 */
function GetAllScoreAnime(int $idAnime): array
{
  $query = "SELECT note FROM t_library WHERE idAnime = :idAnime;";

  try {
    $getAnimeScores = EDatabase::getDb()->prepare($query);
    $getAnimeScores->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
    $getAnimeScores->execute();

    $result = $getAnimeScores->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  } catch (Exception $e) {
    throw $e->getMessage();
  }
}

/**
 * @author Hoarau Nicolas
 * @brief Get the anime name with his id
 *
 * @param integer $idAnime
 * @return string
 */
function GetAnimeNameById(int $idAnime): string
{
  $query = <<<EOT
SELECT name FROM t_anime WHERE idAnime = :idAnime;
EOT;

  try {
    $getAnimeName = EDatabase::getDb()->prepare($query);
    $getAnimeName->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
    $getAnimeName->execute();

    $result = $getAnimeName->fetch(PDO::FETCH_ASSOC);
    $animeName = filter_var($result['name'], FILTER_SANITIZE_STRING);

    return $animeName;
  } catch (Exception $e) {
    throw $e->getMessage();
  }
}
