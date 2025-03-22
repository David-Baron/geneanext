<?php

use Symfony\Component\HttpFoundation\Session\Session;

require(__DIR__ . '/../Model/UtilisateurModel.php');

class AppUserAuthenticator // extends AppComponent
{
    private Session $session;
    private UtilisateurModel $utilisateurModel;
    private int $antiflood = 5;

    public function __construct(Session $session)
    {
        // parent::__construct();
        $this->session = $session;
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function authenticate(string $identifier, string $plainTextPassword): bool
    {
        if ($this->session->has('antiflood') && $this->session->get('antiflood') >= $this->antiflood) {
            /** @todo Set ip and login on untrusted for 24 h and Set user flash for flooding here or redirect to forgot_password*/
            return false;
        }

        $user = $this->utilisateurModel->findOneByCriteria(['codeUtil' => $identifier]);
        // $oldhashpass = hash('sha256', $salt1 . $NomU . $salt2 . $motPasse . $salt3);
        
        if ($user) { 
            $oldhashpass = hash('sha256', ';$€°d' . $user['codeUtil'] . '#\'_^' . $plainTextPassword . '@")[&ù'); // pass hashed with old sha256
            if ($oldhashpass === $user['motPasseUtil']) {
                # code...
            }
            // OK set in session
            $this->session->set('user', $user);
            // $passwordWithnewEncoding = password_hash($plainTextPassword, PASSWORD_DEFAULT); TODO: database need to be modified (char 64 actualy, varchar 255 needed)
            return true;
        }/*  elseif ($user && password_verify($plainTextPassword, $user['motPasseUtil'])) { // pass hashed with password_hash
            // OK set in session
            return true;
        } */

        $this->session->set('antiflood', $this->session->get('antiflood', 0) + 1);
        return false;
    }
}
