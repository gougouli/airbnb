<?php


namespace App;


use PDO;

class User
{
    private $money = 0;

    public function getInfoUser($id, $acco = 1){
        $db = Mysql::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        if($acco){
            $accolist = new AccomodationList();
            $info['accomodation'] = $accolist->getBySeller($id);
        }
        //var_dump($info);
        return $info;
    }

    public function validate($id): void{
        $db = Mysql::getInstance();
        $stmt = $db->prepare("UPDATE users SET isActive = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function exist($email, $pass, $hash=1): int{
        $db = Mysql::getInstance();
        if($hash){
            $pass = sha1($pass);
        }
        $req = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $req->execute([$email, $pass]);
        if($req->rowCount() == 1){
            $req = $req->fetch();
            if($req['isActive'] == 1){
                return $req['id'];
            }else{
                if($req['isActive'] == 3){
                    $_SESSION['errors'][] = "Votre compte est entrain de subir un changement de mot de passe. Contactez un administrateur si vous n'etes pas à l'origine de cette requete.";
                }elseif($req['isActive'] == 0){
                    $_SESSION['errors'][] =  "Votre compte a été bannis.";
                }else{
                    $_SESSION['errors'][] =  "Votre compte n'est pas activé. Vérifiez vos emails.";
                }
            }
        }else{
            $_SESSION['errors'][] =  "Votre email ou votre mot de passe est incorrect.";

        }
        return 0;
    }

    public function enoughMoney($money, $id)
    {
        $this->money = $this->getInfoUser($id)['money'];
        if($this->money >= $money){
            return true;
        }
        return false;
    }

    public function removeMoney($money, $id)
    {
        $this->money = $this->money - $money;
        $db = Mysql::getInstance();
        $stmt = $db->prepare("UPDATE users SET money = ? WHERE id = ?");
        $stmt->execute([$this->money,$id]);
    }


}
