<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();

        if($this->loggedUser === false ) {
            $this->redirect('/login');
        }
    }

    public function index($atts = []) {
        $page = intVal(filter_input(INPUT_GET, 'page'));

        //detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        } 
        
        //pegando informações do usuário
        $user = UserHandler::getUser($id, true);

        if(!$user) {
            $this->redirect('/'); 
        }

        $dateFrom = new \dateTime($user->birthdate);
        $dateTo = new \dateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //pegando o feed do usuário
        $feed = PostHandler::getUserFeed($id, $page, $this->loggedUser->id);

        //verificar se EU sigo o usuário
        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
    }

    public function follow($atts) {
        $to = intVal($atts['id']);

        if(UserHandler::idExists($to)) {
            if(UserHandler::isFollowing($this->loggedUser->id, $to)) {
                UserHandler::unfollow($this->loggedUser->id, $to);
            } else {
                UserHandler::follow($this->loggedUser->id, $to);
            }
        }

        $this->redirect('/perfil/'.$to);
    }

    public function friends($atts = []) {
        //detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        } 
        
        //pegando informações do usuário
        $user = UserHandler::getUser($id, true);

        if(!$user) {
            $this->redirect('/'); 
        }

        $dateFrom = new \dateTime($user->birthdate);
        $dateTo = new \dateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile_friends', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }

    public function photos($atts = []) {
        //detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        } 
        
        //pegando informações do usuário
        $user = UserHandler::getUser($id, true);

        if(!$user) {
            $this->redirect('/'); 
        }

        $dateFrom = new \dateTime($user->birthdate);
        $dateTo = new \dateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile_photos', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }
}