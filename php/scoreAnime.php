<?php
require_once '../inc/dbConnect.php';
require_once dirname(__DIR__) . '/inc/function.php';

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$score = filter_var($_POST['score'], FILTER_SANITIZE_NUMBER_INT);
$idAnime = filter_var($_POST['idAnime'], FILTER_SANITIZE_NUMBER_INT);
$idUser = $_SESSION['loggedUser']['idUser'];

$query = <<<EOT
INSERT INTO  t_library (note, idUser, idAnime) VALUES (:note, :idUser,:idAnime);
EOT;

if (AlredyScored($idAnime, $idUser)) {
  $query = <<<EOT
UPDATE t_library SET note = :note WHERE idUser = :idUser AND idAnime = :idAnime;
EOT;
}

try {
  $updateAnimeScore = EDatabase::getDb()->prepare($query);

  $updateAnimeScore->bindParam(':note', $score, PDO::PARAM_INT);
  $updateAnimeScore->bindParam(':idUser', $idUser, PDO::PARAM_INT);
  $updateAnimeScore->bindParam(':idAnime', $idAnime, PDO::PARAM_INT);
  $updateAnimeScore->execute();
  
  echo json_encode([
    'ReturnCode' => 0,
    'Success' => "Score updated correctly"
  ]);
  exit();
} catch (Exception $e) {
  throw $e;
}
