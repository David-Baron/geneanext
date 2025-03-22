<?php 

use Symfony\Component\HttpFoundation\Session\Session;

class UserPermission // extends AppComponent
{
    private array $levels = [
        'I' => ['code' => 'I', 'libele' => 'Invité', 'level' => 1],
        'P' => ['code' => 'P', 'libele' => 'Privilégié', 'level' => 3],
        'C' => ['code' => 'C', 'libele' => 'Contributeur', 'level' => 5],
        'G' => ['code' => 'G', 'libele' => 'Gestionnaire', 'level' => 9],
    ];
    
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function isAuthenticated()
    {
        return $this->session->has('user');
    }
    
    public function isGranted(string $code)
    {
        return $this->isAuthenticated() && $this->session->get('user')['niveau'] >= $this->levels[$code]['level'];
    }
}