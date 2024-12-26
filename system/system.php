<?php
$pack_whitelist = array(
    "bramus/router",
    "carbonphp/carbon-doctrine-types",
    "doctrine/inflector",
    "illuminate/bus",
    "illuminate/collections",
    "illuminate/container",
    "illuminate/contracts",
    "illuminate/events",
    "illuminate/filesystem",
    "illuminate/macroable",
    "illuminate/pipeline",
    "illuminate/support",
    "illuminate/view",
    "jenssegers/blade",
    "nesbot/carbon",
    "psr/clock",
    "psr/clock-implementation",
    "psr/container",
    "psr/container-implementation",
    "psr/simple-cache",
    "symfony/deprecation-contracts",
    "symfony/finder",
    "symfony/polyfill-mbstring",
    "symfony/polyfill-php80",
    "symfony/translation",
    "symfony/translation-contracts",
    "symfony/translation-implementation",
    "voku/portable-ascii",
    "webdernargor/ratricat"
);
$install_packages = array();
$packages = \Composer\InstalledVersions::getInstalledPackages();
$pack_count = 0;
foreach ($packages as $package) {
    if (!in_array($package, $pack_whitelist)) {
        $pack_count++;
        $install_packages[] = $package;
    }
}


$folder_migration = __DIR__ . "/../database/migrations/";
$files_migration = glob($folder_migration . "*.php");
// pre_r($files_migration);
// foreach ($files_migration as $phpFile_migration) {
//     require($phpFile_migration);
// }



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevTools</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1e1e1e;
            color: #ffffff;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 200px;
        }

        .version {
            text-align: center;
            color: #888;
            margin-bottom: 30px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .stat-box {
            background-color: #2a2a2a;
            padding: 50px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            flex: 1 1 150px;
            margin: 5px;
        }

        .plugins {
            text-align: center;
            margin-top: 20px;
        }
        .button-create-migration{
            background-color: #2a2a2a;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            flex: 1 1 150px;
            margin: 5px;
            color: #ffffff;
            border: 1px solid #ffffff;
            cursor: pointer;
        }
        .button-create-migration:hover{
            background-color: #ffffff;
            color: #000000;
        }
        .input-create-migration{
            background-color: #fff;
    
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            flex: 1 1 150px;
            margin: 5px;
        }
        .button-migration{
            background-color: #2a2a2a;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            flex: 1 1 150px;
            margin: 5px;
            color: #ffffff;
            border: 1px solid #ffffff;
            cursor: pointer;
            text-decoration: none;
        }
        .button-migration:hover{
            background-color: #ffffff;
            color: #000000;
        }

        .button-rollback{
            background-color: #2a2a2a;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            flex: 1 1 150px;
            margin: 5px;
            color: #ffffff;
            border: 1px solid #ffffff;
            cursor: pointer;
            text-decoration: none;
        }
        .button-rollback:hover{
            background-color: #ffffff;
            color: #000000;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
        }

        .footer a {
            color: #4a4a4a;
            text-decoration: none;
            margin: 0 10px;
        }



        .modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  color: #000;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
.Resource{
    position: absolute;
padding: 10px;
bottom:5%;
}

    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="<?= URL() ?>/devtools/image/logo_cats/png" alt="DevTools Logo">
        </div>
        <div class="version">
            Ratricat DevTools
        </div>
        <div class="stats">

        <div class="stat-box">
                <h3 style="cursor: pointer;" onclick="openModal('#migration_modal')"><?= count($files_migration) ?> Migration files</h3>


                <div id="migration_modal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close" onclick="closeModal('#migration_modal')">&times;</span>
    <?php 
    if(count($files_migration)>0){
    foreach ($files_migration as $file) { ?>
        <p><?= $file ?></p>
   <?php } ?>
   <?php }else{ ?>
   <p>No migration files found</p>
   <?php } ?>
  </div>

</div>


            </div>

            <div class="stat-box">
                <h3 style="cursor: pointer;" onclick="openModal('#routes_modal')"><?= count($routerlist) ?> Routes</h3>
                <div class="modal" id="routes_modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('#routes_modal')">&times;</span>
                        <?php 
                        if(count($routerlist)>0){
                        foreach ($routerlist as $route) { 
                            ?>
                            <p><?= $route['method'] ?> <?= $route['url'] ?></p>
                        <?php } ?>
                        <?php }else{ ?>
                        <p>No routes found</p>
                        <?php } ?>
                    </div>
                </div>
            </div>


            <div class="stat-box">
                    <h3 style="cursor: pointer;" onclick="openModal('#packages_modal')"><?= $pack_count ?> packages</h3>
                    <div class="modal" id="packages_modal">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal('#packages_modal')">&times;</span>
                            <?php 
                            if(count($install_packages)>0){
                            foreach ($install_packages as $package) { ?>
                                <p><a href="https://packagist.org/packages/<?= $package ?>" target="_new"><?= $package ?></a></p>
                            <?php }
                            }else{
                            ?>
                            <p>No packages installed</p>
                            <?php } ?>
                        </div>
                    </div>
            </div>
        </div>
        <div class="plugins">
            <div class="stat-box">
             <form action="<?= URL() ?>/system/devtools/" method="get">
                <input type="hidden" name="action" value="create_migration">
                <input required class="input-create-migration" type="text" name="table_name" placeholder="Table Name">
                <button class="button-create-migration" type="submit">Create Migration</button>
             </form>
            </div>
            <div class="stat-box">
                <a class="button-migration" href="<?= URL() ?>/system/devtools/?action=migration">PHP Migration</a>
            </div>
            <div class="stat-box">
                <a class="button-rollback" href="<?= URL() ?>/system/devtools/?action=rollback">PHP Rollback</a>
            </div>
        </div>

    </div>
    <div class="Resource">
        <div>
            <h3>Resource</h3>
            <select name="" id="">
                <option value="">seleted Resource</option>
                <option value=""></option>
            </select>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
     <?php
     if(isset($_GET['action']) && $_GET['action']=='create_migration'){
        $migrationScript = __DIR__ . '/../bootstrap/create-migration.php';
        $tableName=$_GET['table_name'];
        ob_start();
        passthru("php $migrationScript $tableName", $res);
        $output = ob_get_clean(); 
        $response=json_decode($output,true);
        if($response['success']=='success'){
            echo "Swal.fire('Success','".$response['message']."','success').then(()=>{
                window.location.href='".URL()."/system/devtools/';
            });";
        }else{
            echo "Swal.fire('Error','".$response['message']."','error').then(()=>{
                window.location.href='".URL()."/system/devtools/';
            });";
        }
     }else if(isset($_GET['action']) && $_GET['action']=='migration'){
        $migrationScript = __DIR__ . '/../bootstrap/app.php';
        ob_start();
        passthru("php $migrationScript migrate", $res);
        $output = ob_get_clean(); 
        $response=json_decode($output,true);
        if($response['success']=='success'){
            echo "Swal.fire('Success',`".$response['message']."`,'success').then(()=>{
                window.location.href='".URL()."/system/devtools/';
            });";
        }else{
            echo "Swal.fire('Error',`".$response['message']."`,'error').then(()=>{
                window.location.href='".URL()."/system/devtools/';
            });";
        }
     }else if(isset($_GET['action']) && $_GET['action']=='rollback'){
        $migrationScript = __DIR__ . '/../bootstrap/app.php';
        ob_start();
        passthru("php $migrationScript rollback", $res);
        $output = ob_get_clean(); 
        $response=json_decode($output,true);
        if($response['success']=='success'){
            echo "Swal.fire('Success',`".$response['message']."`,'success').then(()=>{
                window.location.href='".URL()."/system/devtools/';
            });";
        }else{
            echo "Swal.fire('Error',`".$response['message']."`,'error').then(()=>{
                window.location.href='".URL()."/system/devtools/';
            });";
        }
     }else
     ?>
var modal_open=false;
var open_modal=null;
function openModal(element){
let modal = document.querySelector(element);

  modal.style.display = "block";
  modal_open=true;
  open_modal=element;

}




function closeModal(element){
    modal_open=false;
    open_modal=null;
let modal = document.querySelector(element);
modal.style.display = "none";

}

document.onkeydown=function(e){
    if(e.key=='Escape' && modal_open){
        closeModal(open_modal);
    }
}

document.onclick=function(e){
    if(e.target.classList.contains('modal')){
        if(modal_open){
            closeModal(open_modal);
        }
    }
}




    </script>
</body>

</html>