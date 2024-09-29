<?php

    require 'connection.php';

    if(!empty($_POST['name']) || !empty($_POST['username']) || !empty($_POST['email']) || !empty($_POST['password']) || !empty($_POST['password_confirm'])){

        $db = dbConnect();
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $r = 'user';

        if($password === $_POST['password_confirm']){

            $hash_pass = password_hash($password, PASSWORD_DEFAULT);
            
            $req = $db->prepare('INSERT INTO user(nom,password,username,email,role) VALUES (:nom, :password, :username, :email, :role)');
            $req->bindParam(':nom', $name);
            $req->bindParam(':password', $hash_pass);
            $req->bindParam(':username', $username);
            $req->bindParam(':email', $email);
            $req->bindParam(':role', $r);
           
            if ( $req->execute()) {
                
                $r = $db->prepare('SELECT * FROM user WHERE email = :email');
                $r->bindParam(':email', $email);
                $r->execute();
                $user = $r->fetch(PDO::FETCH_ASSOC);

                $req = $db->prepare('INSERT INTO compte(solde,user_id) VALUES (0, :user_id)');
                $req->bindParam(':user_id', $user['id']);
                $req->execute();

                echo 'inscription reussie';
                header('location: login.php');
            } else {
                echo "Erreur : " . $stmt->error;
            }
    
            $req->close();
            
        }else{
            echo "Les mots de passe ne correspondent pas.";
        }

    }

?>

<!DOCTYPE html>
<html lang="en">


<!-- auth-register.html  21 Nov 2019 04:05:01 GMT -->
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>MaBanque</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="assets/css/app.min.css">
  <link rel="stylesheet" href="assets/bundles/jquery-selectric/selectric.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='assets/img/favicon.ico' />
  <!-- CSS de Toastr -->
  <link rel="stylesheet" href="./assets/css/toastr.min.css">
</head>

<body>
  <div class="loader"></div>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="card card-primary">
              <div class="card-header">
                <h4>Register</h4>
              </div>
              <div class="card-body">
                <form method="POST" action="register.php">
                  <div class="row">
                    <div class="form-group col-6">
                      <label for="frist_name">Name</label>
                      <input id="frist_name" type="text" class="form-control" name="name" autofocus>
                    </div>
                    <div class="form-group col-6">
                      <label for="last_name">Useranme</label>
                      <input id="username" type="text" class="form-control" name="username">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email">
                    <div class="invalid-feedback">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-6">
                      <label for="password" class="d-block">Password</label>
                      <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator"
                        name="password">
                      <div id="pwindicator" class="pwindicator">
                        <div class="bar"></div>
                        <div class="label"></div>
                      </div>
                    </div>
                    <div class="form-group col-6">
                      <label for="password2" class="d-block">Password Confirmation</label>
                      <input id="password2" type="password" class="form-control" name="password_confirm">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="agree" class="custom-control-input" id="agree">
                      <label class="custom-control-label" for="agree">I agree with the terms and conditions</label>
                    </div>
                  </div>
                  <div class="form-group">
                    <button type="submit" id="toastr-1" class="btn btn-primary btn-lg btn-block">
                      Register
                    </button>
                  </div>
                </form>
              </div>
              <div class="mb-4 text-muted text-center">
                Already Registered? <a href="login.php">Login</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- General JS Scripts -->
  <script src="assets/js/app.min.js"></script>
  <!-- JS Libraies -->
  <script src="assets/bundles/jquery-pwstrength/jquery.pwstrength.min.js"></script>
  <script src="assets/bundles/jquery-selectric/jquery.selectric.min.js"></script>
  <!-- Page Specific JS File -->
  <script src="assets/js/page/auth-register.js"></script>
  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <!-- Custom JS File -->
  <script src="assets/js/custom.js"></script>
   <!-- JS de Toastr -->
   <script src="./assets/js/toastr.min.js"></script>
   <script type="text/javascript">
    // Exemple pour une notification de succès
    toastr.success('Votre inscription a été réussie !', 'Succès', {timeOut: 5000});

    // Exemple pour une notification d'erreur
    toastr.error('Une erreur s\'est produite lors de la connexion.', 'Erreur', {timeOut: 5000});

    // Exemple pour une notification d'information
    toastr.info('Vous avez un nouveau message.', 'Information', {timeOut: 5000});

    // Exemple pour une notification d'avertissement
    toastr.warning('Attention, vérifiez vos informations.', 'Avertissement', {timeOut: 5000});
</script>

</body>


<!-- auth-register.html  21 Nov 2019 04:05:02 GMT -->
</html>