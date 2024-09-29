<?php
session_start();
require './connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

$db = dbConnect();
$req = $db->prepare('SELECT solde FROM compte WHERE user_id = :id');
$req->bindParam(':id', $_SESSION['user_id']);
$req->execute();
$result = $req->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $montant = $_POST['montant'];
    $transaction = $_POST['transaction'];
    $message = '';

    if ($transaction == 'depot') {

        $per = $db->prepare('UPDATE compte SET solde = solde + :montant WHERE user_id = :id');
        $per->bindParam(':montant', $montant);
        $per->bindParam(':id', $_SESSION['user_id']);
        $per->execute();
        // $message = "Dépôt effectué avec succès.";
    } elseif ($transaction == 'retrait') {

        if ($result['solde'] >= $montant) {
            $per = $db->prepare('UPDATE compte SET solde = solde - :montant WHERE user_id = :id');
            $per->bindParam(':montant', $montant);
            $per->bindParam(':id', $_SESSION['user_id']);
            $per->execute();
            // $message = "Retrait effectué avec succès.";
        } else {
            $message = "Solde insuffisant.";
        }
    } elseif ($transaction == 'transfert') {

        $id_destinataire = $_POST['id_destinataire'];

        if ($result['solde'] >= $montant) {

            $per = $db->prepare('UPDATE compte SET solde = solde - :montant WHERE user_id = :id');
            $per->bindParam(':montant', $montant);
            $per->bindParam(':id', $_SESSION['user_id']);
            $per->execute();

            $per = $db->prepare('UPDATE compte SET solde = solde + :montant WHERE user_id = :id_destinataire');
            $per->bindParam(':montant', $montant);
            $per->bindParam(':id_destinataire', $id_destinataire);
            $per->execute();

            // $message = "Transfert effectué avec succès.";
        } else {
            $message = "Solde insuffisant pour effectuer le transfert.";
        }
    }

    $req = $db->prepare('SELECT solde FROM compte WHERE user_id = :id');
    $req->bindParam(':id', $_SESSION['user_id']);
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);

    // Renvoyer la réponse avec le nouveau solde en JSON
    echo json_encode(['newSolde' => $result['solde'], 'message' => $message]);
}
?>





<!DOCTYPE html>
<html lang="en">


<!-- index.html  21 Nov 2019 03:44:50 GMT -->
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>MaBanque</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="./assets/css/app.min.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/components.css">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="./assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='./assets/img/favicon.ico' />
</head>

<body>

<div class="loader"></div>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <nav class="navbar navbar-expand-lg main-navbar sticky">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
									collapse-btn"> <i data-feather="align-justify"></i></a></li>
            <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                <i data-feather="maximize"></i>
              </a></li>
            <li>
              <form class="form-inline mr-auto">
                <div class="search-element">
                  <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="200">
                  <button class="btn" type="submit">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </form>
            </li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown"
              class="nav-link dropdown-toggle nav-link-lg nav-link-user"> <img alt="image" src="./assets/img/user.png"
                class="user-img-radious-style"> <span class="d-sm-none d-lg-inline-block"></span></a>
            <div class="dropdown-menu dropdown-menu-right pullDown">
              <div class="dropdown-title"><?= $_SESSION['nom']; ?></div>
              <a href="profile.php" class="dropdown-item has-icon"> <i class="far
										fa-user"></i> Profile
              </a> 
              <div class="dropdown-divider"></div>
              <a href="login.php" class="dropdown-item has-icon text-danger"> <i class="fas fa-sign-out-alt"></i>
                Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
    </div>


      <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
          <!-- <div class="sidebar-brand">
            <a href="index.html"> <img alt="image" src="./assets/img/logo.png" class="header-logo" /> <span
                class="logo-name">Otika</span>
            </a>
          </div> -->
          <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li class="dropdown active">
              <a href="home.php" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
            </li>
            <li class="dropdown">
              <a href="#" class="nav-link"><i
                  data-feather="user"></i><span>Mon Compte</span></a>
            </li>

          </ul>
        </aside>
      </div>
      <!-- Main Content -->
      <div class="main-content">

<section class="section">
          <div class="row"  style="display:flex; justify-content:center;">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <div class="card">
                <div class="card-statistic-4">
                  <div class="align-items-center justify-content-between">
                    <div class="row ">
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                        <div class="card-content">
                          <h5 class="font-15"> Customers</h5>
                          <h2 class="mb-3 font-12">Effectuez vos transactions sans craintes</h2>
                          <!-- <p class="mb-0"><span class="col-orange">09%</span> Decrease</p> -->
                        </div>
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                        <div class="banner-img">
                          <img src="./assets/img/banner/2.png" alt="">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
              <div class="card">
                <div class="card-statistic-4">
                  <div class="align-items-center justify-content-between">
                    <div class="row ">
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                        <div class="card-content">
                          <h5 class="font-15">Mon Solde</h5>
                          <h2 class="mb-3 font-18" id="solde">
                            <?php
                                if ($result) {
                                    echo $result['solde'];
                                } else {
                                    echo "Aucun compte trouvé pour cet utilisateur.";
                                }
                            ?>
                          </h2>
                          <!-- <p class="mb-0"><span class="col-green">42%</span> Increase</p> -->
                        </div>
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                        <div class="banner-img">
                          <img src="./assets/img/banner/4.png" alt="">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row" style="display:flex; justify-content:center; ">
          <div class="col-md-6 col-lg-12 col-xl-6" style="display: flex; justify-content: space-between; gap: 40px;">

              <div class="card" style="flex: 1;">
                <div class="card-header">
                  <h4>historique de transactions</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Client Name</th>
                          <th>Date</th>
                          <th>Payment Method</th>
                          <th>Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>John Doe </td>
                          <td>11-08-2018</td>
                          <td>NEFT</td>
                          <td>$258</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="card" style="flex: 1;">
                  <div class="card-header">
                    <h4>Transaction</h4>
                  </div>
                  <form action="home.php" method="POST" id="transactionForm">
                        <div class="card-body">
                           
                            <div class="form-group">
                            <label>Montant</label>
                            <input type="text" class="form-control" name="montant">
                            </div>

                            <div class="section-title">Type de transaction</div>
                            <div class="form-group">
                            <!-- <label>Select <code>.form-control-sm</code></label> -->
                            <select class="form-control form-control-sm" name="transaction">
                                <option value="depot">depot</option>
                                <option value="retrait">retrait</option>
                                <option value="transfert">transfert</option>

                            </select>
                            </div>

                            <div class="section-title mt-0">Identifiant du compte</div>
                                <div class="form-group">
                                    <!-- <label>Text <code>.form-control-sm</code></label> -->
                                    <input type="text" class="form-control form-control-sm" name="id_destinataire">
                                </div>
                            </div>

                            <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                            Transferer
                            </button>

                        </div>
                        </div>
                    </form>
                </div>
            </div>
          </div>
        </section>

        <div class="settingSidebar">
          <a href="javascript:void(0)" class="settingPanelToggle"> <i class="fa fa-spin fa-cog"></i>
          </a>
          <div class="settingSidebar-body ps-container ps-theme-default">
            <div class=" fade show active">
              <div class="setting-panel-header">Setting Panel
              </div>
              <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">Select Layout</h6>
                <div class="selectgroup layout-color w-50">
                  <label class="selectgroup-item">
                    <input type="radio" name="value" value="1" class="selectgroup-input-radio select-layout" checked>
                    <span class="selectgroup-button">Light</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="value" value="2" class="selectgroup-input-radio select-layout">
                    <span class="selectgroup-button">Dark</span>
                  </label>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">Sidebar Color</h6>
                <div class="selectgroup selectgroup-pills sidebar-color">
                  <label class="selectgroup-item">
                    <input type="radio" name="icon-input" value="1" class="selectgroup-input select-sidebar">
                    <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                      data-original-title="Light Sidebar"><i class="fas fa-sun"></i></span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="icon-input" value="2" class="selectgroup-input select-sidebar" checked>
                    <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                      data-original-title="Dark Sidebar"><i class="fas fa-moon"></i></span>
                  </label>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">Color Theme</h6>
                <div class="theme-setting-options">
                  <ul class="choose-theme list-unstyled mb-0">
                    <li title="white" class="active">
                      <div class="white"></div>
                    </li>
                    <li title="cyan">
                      <div class="cyan"></div>
                    </li>
                    <li title="black">
                      <div class="black"></div>
                    </li>
                    <li title="purple">
                      <div class="purple"></div>
                    </li>
                    <li title="orange">
                      <div class="orange"></div>
                    </li>
                    <li title="green">
                      <div class="green"></div>
                    </li>
                    <li title="red">
                      <div class="red"></div>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <div class="theme-setting-options">
                  <label class="m-b-0">
                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                      id="mini_sidebar_setting">
                    <span class="custom-switch-indicator"></span>
                    <span class="control-label p-l-10">Mini Sidebar</span>
                  </label>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <div class="theme-setting-options">
                  <label class="m-b-0">
                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                      id="sticky_header_setting">
                    <span class="custom-switch-indicator"></span>
                    <span class="control-label p-l-10">Sticky Header</span>
                  </label>
                </div>
              </div>
              <div class="mt-4 mb-4 p-3 align-center rt-sidebar-last-ele">
                <a href="#" class="btn btn-icon icon-left btn-primary btn-restore-theme">
                  <i class="fas fa-undo"></i> Restore Default
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="main-footer">
        <div class="footer-left">
          <a href="templateshub.net">Templateshub</a></a>
        </div>
        <div class="footer-right">
        </div>
      </footer>

    </div>
  </div>

    <!-- General JS Scripts -->
    <script src="./assets/js/app.min.js"></script>
    <!-- JS Libraies -->
    <script src="./assets/bundles/apexcharts/apexcharts.min.js"></script>
    <!-- Page Specific JS File -->
    <script src="./assets/js/page/index.js"></script>
    <!-- Template JS File -->
    <script src="./assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="./assets/js/custom.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#transactionForm").on('submit', function(e) {
            e.preventDefault(); // Empêche le rechargement de la page

            var formData = $(this).serialize();

            var transactionType = $("input[name='transaction']").val(); 

            if (transactionType === 'depot' || transactionType === 'retrait') {
                $.ajax({
                url: 'home.php', // Script PHP pour traiter les transactions
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Actualiser le solde sur la page
                    $("#solde").html(response.newSolde);
                    alert(response.message); 
                },
                error: function() {
                    alert('Une erreur est survenue');
                }
                });
            } else {

                this.submit(); 
            }
            });
        });
    </script>


</body>
