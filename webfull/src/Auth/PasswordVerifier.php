<?php
declare(strict_types=1);

namespace App\Auth;

final class PasswordVerifier
{
    public function verify(string $plain, string $dbHash): bool
    {
        $dbHash = (string)$dbHash;
        $h = strtolower($dbHash);

        // 1) password_hash/bcrypt (or any password_hash algo)
        $info = password_get_info($dbHash);
        if (!empty($info['algo'])) {
            return password_verify($plain, $dbHash);
        }

        // 2) SHA1 (40 hex) + sha1(md5(pass)) + sha1(lower(pass))
        if (strlen($h) === 40 && ctype_xdigit($h)) {
            return (sha1($plain) === $h)
                || (sha1(md5($plain)) === $h)
                || (sha1(strtolower($plain)) === $h);
        }

        // 3) MD5 (32 hex) + md5(lower(pass))
        if (strlen($h) === 32 && ctype_xdigit($h)) {
            return (md5($plain) === $h) || (md5(strtolower($plain)) === $h);
        }

        // 4) plaintext
        return hash_equals($dbHash, $plain);
    }
}
