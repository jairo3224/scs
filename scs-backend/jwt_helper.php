<?php
// backend/jwt_helper.php
// simple JWT encode / decode (HMAC SHA256). Not a full library but fine for demo/college project.

// base64url encode
function b64url_encode($data){
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function b64url_decode($data){
  $remainder = strlen($data) % 4;
  if ($remainder) $data .= str_repeat('=', 4 - $remainder);
  return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_encode($payload, $secret, $exp_seconds = 86400) {
  $header = ['alg' => 'HS256', 'typ' => 'JWT'];
  $payload['iat'] = time();
  $payload['exp'] = time() + $exp_seconds;
  $header_b64 = b64url_encode(json_encode($header));
  $payload_b64 = b64url_encode(json_encode($payload));
  $sig = hash_hmac('sha256', "$header_b64.$payload_b64", $secret, true);
  $sig_b64 = b64url_encode($sig);
  return "$header_b64.$payload_b64.$sig_b64";
}

function jwt_decode($token, $secret) {
  $parts = explode('.', $token);
  if (count($parts) !== 3) return null;
  list($h64, $p64, $s64) = $parts;
  $payload_json = b64url_decode($p64);
  $sig_check = b64url_encode(hash_hmac('sha256', "$h64.$p64", $secret, true));
  if (!hash_equals($sig_check, $s64)) return null;
  $payload = json_decode($payload_json, true);
  if (!$payload) return null;
  if (isset($payload['exp']) && time() > $payload['exp']) return null;
  return $payload;
}

// helper to get JWT from Authorization header
function get_bearer_token() {
  $hdrs = null;
  if (function_exists('getallheaders')) $hdrs = getallheaders();
  if (!$hdrs) {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) $hdrs = ['Authorization' => $_SERVER['HTTP_AUTHORIZATION']];
  }
  if (!$hdrs) return null;
  $auth = $hdrs['Authorization'] ?? $hdrs['authorization'] ?? '';
  if (preg_match('/Bearer\s(\S+)/', $auth, $m)) return $m[1];
  return null;
}
