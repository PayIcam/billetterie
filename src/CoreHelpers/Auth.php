<?php

namespace CoreHelpers;


class Auth{

    private $roles;

    /**
     * Il s'agit juste d'écrire les différents roles dès l'appel de la fonction
     */
    function __construct() {
        global $DB;
        $this->roles = array(
            array('id'=>0, "name"=>"Membre", 'slug'=> "member", 'level'=>0),
            array('id'=>1, "name"=>"Serveur et autre responsabilités", 'slug'=> "member++", 'level'=>1),
            array('id'=>2, "name"=>"Administrateur de Fondation", 'slug'=> "admin", 'level'=>2),
            array('id'=>3, "name"=>"Super Admin", 'slug'=> "super-admin", 'level'=>3)
        );
    }

    /**
     * La fonction va permettre de se connecter au Cas.
     * Elle va aussi mettre dans la session des petites choses intéressantes, comme le cokie payUtc, et les infos de l'Icam
     * @param  $ticket  C'ets le ticket de connection que l'on reçoit
     * @param  $service C'est l'url de la page à appeler qui va permettre de se connecter.
     */
    function loginUsingCas($ticket, $service) {
        global $DB, $payutcClient, $_CONFIG;

        try {
            $result = $payutcClient->loginCas(array("ticket" => $ticket, "service" => $service));

            $status = $payutcClient->getStatus();
            $userRank = $payutcClient->getUserLevel();

            $_SESSION['payutc_cookie'] = $payutcClient->cookie;
            $_SESSION['Auth'] = array(
                'email' => $status->user,
                'firstname' => $status->user_data->firstname,
                'lastname' => $status->user_data->lastname,
                'slug' => $this->roles[$userRank]['slug'],
                'roleName' => $this->roles[$userRank]['name'],
                'level' => $userRank
            );
            return true;
        } catch (Exception $e) {
            if (strpos($e, 'UserNotFound') !== false ) {
                header('Location: '. basename(basename($_CONFIG['payicam']['url'])).'/casper', true, 303);
                die();
            }
            // Functions::setFlash($e->getMessage(),'danger');
            return false;
        }
    }


    public function logOut() {
        global $payutcClient;
        $status = $payutcClient->getStatus();
        if($status->user) {
            $payutcClient->logout();
        }
        $service = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $casUrl = $payutcClient->getCasUrl()."logout?url=".urlencode($service);
        $_SESSION = array();
        session_destroy();

        return $casUrl;
    }

    /**
     * Autorise un rang à accéder à une page, redirige vers forbidden sinon
     * */
    function allow($rang) {
        $roles = $this->getLevels();
        if(!$this->getUserField('slug')) {
            $this->forbidden();
        } else {
            if($roles[$rang] > $this->getUserField('level')) {
                $this->forbidden();
            } else {
                return true;
            }
        }
    }

    /**
     * Permet de savoir si un utilisateur a un role ou non.
     * @param  $rang c'est le slug, il faut donc donner ('member', 'member++', 'admin' ou 'super-admin')
     * @return boolean renvoie true si l'utilisateur a le role, false sinon
     */
    function hasRole($rang) {
        $roles = $this->getLevels();
        if(!$this->getUserField('slug')) {
            return false;
        } else {
            if($roles[$rang] > $this->getUserField('level')) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Récupère un champ de la variable de session
     * @param  $field IN('id', 'name', 'role', 'slug' ou 'level')
     * @return ce qu'on demande si définit, false sinon
     */
    function getUserField($field) {
        if($field == 'role') $field = 'slug';
        if(isset($_SESSION['Auth'][$field])) {
            return $_SESSION['Auth'][$field];
        } else {
            return false;
        }
    }

    /**
     * Récupère toutes les infos de Auth dans la variable de session
     */
    function getUser() {
        return $_SESSION['Auth'];
    }

    /**
     * Redirige un utilisateur
     * */
    function forbidden() {
        Functions::setFlash('<strong>Identification requise</strong> Vous ne pouvez accéder à cette page.','danger');
        header('Location: connection.php'.((!empty($_GET['ticket']))?'?ticket='.$_GET['ticket']:''));exit;
    }


    // -------------------- isXXX functions -------------------- //
    /**
     * Renvoie true si la connection a l'air bonne, false sinon
     */
    function isLogged() {
        if ($this->getUserField('level') !== false && $this->getUserField('level') >= 0)
            return true;
        else
            return false;
    }
    /**
     * Renvoie true si on est admin ou super-admin
     * @return boolean [description]
     */
    function isAdmin() {
        if ($this->getUserField('role') == 'admin')
            return true;
        else
            return false;
    }

    // -------------------- Getters -------------------- //
    public function getLevels($key = 'slug') {
        global $DB;
        if ($key != 'slug' || $key != 'id')
            $key = 'slug';

        $roles = array();
        foreach($this->roles as $d) {
            $roles[$d[$key]] = $d['level'];
        }
        return $roles;
    }
    public function getRoles($key = 'id') {
        global $DB;
        if ($key != 'slug' || $key != 'id')
            $key = 'id';

        $roles = array();
        foreach($this->roles as $d) {
            $roles[$d[$key]] = $d['name'];
        }
        return $roles;
    }
    public function getRole($key) {
        if (isset($this->roles[$key])) {
            return $this->roles[$key];
        } else { // C'est surement son slug
            foreach($this->roles as $d) {
                if ($d['slug'] == $key) {
                    return $d;
                }
            }
            return null;
        }
    }
    public function getRoleName($id) {
        $role = $this->getRole($id);
        return $role['name'];
    }
}
