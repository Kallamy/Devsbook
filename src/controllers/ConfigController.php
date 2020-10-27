<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ConfigController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checkLogin();

        if($this->loggedUser === false ) {
            $this->redirect('/login');
        }
        $this->loggedUser = UserHandler::getUser( $this->loggedUser->id );
    }
    
    public function index() {

        
        
        $dateFrom = new \dateTime($this->loggedUser->birthdate);
        $dateTo = new \dateTime('today');
        $this->loggedUser->ageYears = $dateFrom->diff($dateTo)->y;

        $flash = '';
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';

        }
        
        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'flash' => $flash
        ]);
    }

    public function save() {
        $name = filter_input(INPUT_POST, 'name');
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $email = filter_input(INPUT_POST, 'email');
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');
        $password = filter_input(INPUT_POST, 'password');
        $passwordConfirm = filter_input(INPUT_POST, 'password_confirm');

        if($name && $email) {
            $updateFields = [];

            $user = UserHandler::getUser($this->loggedUser->id);

            // E-MAIL
            if($user->email != $email) {
                if(!UserHandler::emailExists($email)) {
                    $updateFields['email'] = email;
                } else {
                    $_SESSION['flash'] = 'Esse e-mail já está cadastrado, tente outro!';
                    $this->redirect('/config');
                }
            }

            // BIRTHDATE
            $birthdate = explode('/', $birthdate);
            if(count($birthdate) != 3) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/config');
            }
            $updateFields['birthdate'] = $birthdate;

            // PASSWORD
            if(!empty($pasword)) {
                echo "senhas digitadas: ".$password.', '.$passwordConfirm;
                exit;
                if($password === $passwordConfirm) {
                    $updateFields['password'] = $password;
                } else {
                    $_SESSION['flash'] = 'As senhas não batem.';
                    $this->redirect('/config');
                }
            }

            // CAMPOS NORMAIS
            $updateFields['name'] = $name;
            $updateFields['city'] = $city;
            $updateFields['work'] = $work;
            
            //AVATAR
            if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                $newAvatar = $_FILES['avatar'];

                if(in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                    $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
                    $updateFields['avatar'] = $avatarName;
                }
            }

            //COVER
            if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
                $newCover = $_FILES['cover'];

                if(in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                    $coverName = $this->cutImage($newAvatar, 850, 310, 'media/covers');
                    $updateFields['cover'] = $coverName;
                }
            }

            UserHandler::updateUser($updateFields, $this->loggedUser->id);

        }
        
        $this->redirect('/config');
    
    }

    private function cutImage($file, $w, $h, $folder) {
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidht = $w;
        $newHeight = $newWidht / $ratio;

        if($newHeight < $h) {
            $newHeight = $h;
            $newWidht = $newHeight / $ratio;
        }

        $x = $w - $newWidht;
        $y = $h - $newHeight;
        $x = $x < 0 ? $x / 2 : $x; 
        $y = $y < 0 ? $y / 2 : $y; 

        $finalImage = imagecreatetruecolor($w, $h);
        switch($file[type]) {
            case 'image.jpeg':
            case 'image.jpg':
                $image = \imagecreatefromjpeg($file['tmp_name']);
            case 'image.png':
                $image = \imagecreatefrompng($file['tmp_name']);
            break;
        }

        imagecopyresampled(
            $finalImage, $img,
            $x, $y, 0, 0,
            $newWidht, $newHeight, $widthOrig, $heightOrig
        );

        $fileName = mds(time().rand(0,9999)).'jpg';

        imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;
    }
}