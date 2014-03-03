<?
// This was derived from https://gist.github.com/dzuelke/972386 in order to
// have an implementation that does not require PHP >= 5.5.
// With 5.5 password_hash() with algo CRYPT_BLOWFISH implements bcrypt()
// directly.

function bcrypt_hash($pass) {
    $salt = substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
    $hash = crypt($pass, '$2a$12$' . $salt);
    return $hash;
}

function bcrypt_check($pass,$hash) {
    return $hash == crypt($pass, $hash);
}

?>
