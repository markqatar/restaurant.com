<?php
use Luracast\Restler\iAuthenticate;

class AuthApi implements iAuthenticate {
    public function __isAllowed() {
        global $pdo;

        $headers = getallheaders();

        if (isset($headers['Authorization']) && strpos($headers['Authorization'], 'Basic') === 0) {
            $decoded = base64_decode(substr($headers['Authorization'], 6));
            list($email, $password) = explode(':', $decoded, 2);

            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = :email AND status = 1 LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['api_user_id'] = $user['id'];
                return true;
            }
        }

        return false;
    }

    public function __getWWWAuthenticateString() {
        return 'Basic realm="API Explorer"';
    }
}