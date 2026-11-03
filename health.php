<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/config.php';
try {
 $pdo=db();
 $tables=$pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
 echo json_encode(['ok'=>true,'database'=>true,'tables'=>count($tables),'php'=>PHP_VERSION],JSON_UNESCAPED_SLASHES);
} catch(Throwable $e) {
 http_response_code(500);
 echo json_encode(['ok'=>false,'database'=>false,'error'=>$e->getMessage(),'php'=>PHP_VERSION],JSON_UNESCAPED_SLASHES);
}
