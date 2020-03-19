<?

use App\Mysql;


function exist($email, $pass): int{
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $req = $db->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
    $req->execute([$email, $pass]);
    if($req->rowCount() == 1){
        $req = $req->fetch();
        if($req['isActive'] == 1){
            return 1;
        }else{
            if($req['isActive'] == 3){
                echo "Votre compte est entrain de subir un changement de mot de passe. Contactez un administrateur si vous n'etes pas à l'origine de cette requete.";
            }elseif($req['isActive'] == 0){
                echo "Votre compte a été bannis.";
            }else{
                echo "Votre compte n'est pas activé. Vérifiez vos emails.";
            }
            return 0;
        }
    }else{
        echo "Votre email ou votre mot de passe est incorrect.";
        return 0;
    }
}
function getInfo($id){
    $mysql = new Mysql();
    $db = $mysql->dbConnect();
    $req = $db->prepare("SELECT * FROM user WHERE id = ?");
    $req->execute([$id]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function isConnected(): int{
    if($_SESSION['state'] == "connected"){
        return 1;
    }
    return 0;
}

function connect(array $data): void{
    if(!isConnected()){
        $email = $data[0];
        $pass = sha1($data[1]);
        if($data[2]){
            setcookie("email", $email, time() + 3600, '/');
            setcookie("password", $pass, time() + 3600, '/');
        }
    }
}

function logout(): void{
    session_unset();
    session_destroy();
    setcookie('email', '', 0);
    setcookie('password', '', 0);
    header('Location: /');
}
