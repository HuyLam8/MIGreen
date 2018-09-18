<?php
session_start();

//init_set('display_errors', 1);


  // Check of user is ingelogged en anders terug naar de login pagina
include_once ("../autorisatie/UserIsLoggedin.php");
include_once ("../gebruiker_klantbeheer/UserCustomerDaoMysql.php");
$userLoggedin = new UserIsLoggedin();
$userLoggedin->backToLoging();
  // Check of de admin is ingelogged....
$adminLoggedin = "";
if( ! $userLoggedin->isAdmin() )
{
  $adminLoggedin = "style='display: none;'";
  echo "<h1 style='margin-top:50px;'>Geen gerbuikersrecht als admin.....</h1>";
}

ini_set('display_errors', 1);
    // Header in de bovenkant
include ("../header/header.php");

    // Is logged in class
include_once ("../autorisatie/UserDaoMysql.php");
    // customerDao voor selecteren van alle klanten
include ('../klantbeheer/CustomerDaoMysql.php');
    // Roep de class CustomerDaoMysql aan voor sql functionaliteiten om klantenlijst op te halen
$customerdao = new CustomerDaoMysql();
$customers = $customerdao-> selectAllCustomers();
// include user_customer class 
include_once ("../gebruiker_klantbeheer/UserCustomerDaoMysql.php");
include ("../autorisatie/HashPassword.php"); // Hash PWD

//    // Title van de pagina...
//    if(!isset($_SESSION))
//    {
//        $_SESSION["title"] = "Log hier in";
//    }

    // Customerdao aanmaken voor het ophalen van alle klanten
$customerdaomysql = new CustomerDaoMysql();
$customers = $customerdaomysql-> selectAllCustomers();

    // Kijk eerst of alle velden zijn ingevoerd met isset()
if( isset($_POST['username']) && isset($_POST['password']) && isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['role']) ) {

        // Controleren of de user al bestaat
    $newUserName = $_POST['username'];
    $oldUserName = null;
        // Roep de class UserDaoMysql aan voor sql functionaliteit om user te checken
    $userDao = new UserDaoMysql();
    $oldUser = $userDao->selectUser( $_POST['username'] );
    $oldUserName = $oldUser->getUsername();

        //Geef melding als de user al bestaat
    if( $oldUserName !== null && $newUserName == $oldUserName )
    {
        echo "<br> <h2>Deze username bestaat al in de database.</h2>";
            // Session leeg maken!!!!
        $_SESSION = array();
    }

        // Wachtwoord checks
        // Controleren op hoofdletters
    if(!preg_match('/[A-Z]/', $_POST['password'] )){
        $_SESSION = array();
        echo "<br> <h2> Je moet minimaal een hoofdletter invoeren! </h2>";
    }

        // Controleren op cijfers
    if (!preg_match('([0-9])', $_POST['password'] )){
        $_SESSION = array();
        echo "<br> <h2> Je moet minimaal een cijfer invoeren! </h2>";
    }

        // Controleren of wachtwoorden gelijk zijn
    if( $_POST['password'] != $_POST['password2'] )
    {
               // Session leeg maken!!!!
        $_SESSION = array();
        echo "<br> <h2>Helaas... uw wachtwoord is niet gelijk....</h2>";
    }


    else
    {
            //Hash het opgegeven password
        $hash = new HashPassword();
        $hash_password = $hash->hashPwd($_POST['password']);

            // Roep de class UserDaoMysql aan voor sql functionaliteit om user in te voeren in database
        $userDao = new UserDaoMysql();
        $userDao->insertUser( $_POST['username'], $hash_password, $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['role'] );

            // Roep de class CustomerDaoMysql aan voor sql functionaliteiten om klantenlijst op te halen
        $customerdao = new CustomerDaoMysql();
        $customers = $customerdao-> selectAllCustomers();

            // Roep de class UserCustomerDaoMysql aan voor sql functionaliteit om user_customer in database te stoppen
        $userCustomerDao = new UserCustomerDaoMysql();
            //clear all userCustomers
        $userCustomerDao->clearUserCustomer($_POST['username']);
        foreach($_POST['clients'] as $customerName) {
         $userCustomerDao-> UserCustomerDaoMysql($_POST['username'], $customerName);  
     }

     echo "<p>Aanmaken gebruiker gelukt</p>";
     header('Location: ../gebruikersbeheer/overzicht.php');
 }

} else {
        // foutmeldingen als niet alles is ingevuld

}

?>

<div class="header-left">
    <p class="breadcrumb">Home <i id="triangle-breadcrumb" class="fas fa-caret-right"></i> Gebruikersoverzicht</p>
    <h2>Nieuwe gebruiker aanmaken</h2>
</div>

<head>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="../css/content.css">
    <script type="text/javascript" src="../js/gebruiker_klantFunctions.js"></script>
    
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#add-field").click(function(){
                $("#customers").clone().appendTo("#dropdown");
            });
        });
    </script> -->

    <meta charset="utf-8">
    <title>Gebruiker Aanmaken</title>
</head>
<body>
    <div class="grid-container" <?php echo $adminLoggedin ?> >

        <!-- form elements -->

        <div class="content">

            <form method="post" enctype="multipart/form-data" action="createuser.php">

                <div class="user-form form-field-padding form-field-style">
                    Gebruikersnaam
                    <br><input type="text" name="username" minlength=5 class="input-text-style" required>
                </div>


                <div class="password-form form-field-padding form-field-style">

                    <div class="password-form-initial">
                        Wachtwoord <span class="info-symbol password-info"><i class="fas fa-info-circle"></i><span class="password-infotext">Je wachtwoord moet minimaal bestaan uit:<p> 8 karakter met 1 hoofdletter en 1 nummer</p></span></span>
                        <br><input type="password" name="password" pattern="(?=.*\d)(?=.*[A-Z]).{8,}" title="minimaal: 8 karakters, 1 Hoofdletter, 1 Nummer" required>
                    </div>
                    <div class="password-form-confirm">
                        Herhaal wachtwoord <br><input type="password" name="password2" class="input-text-style"  required>
                    </div>
                </div>

                <div class="form-field-padding form-field-padding form-field-style">
                    <div class="fullname-form-fn">
                        Voornaam
                        <br><input type="text" name="firstname" minlength="2" class="input-text-style" required>
                    </div>
                    <div class="fullname-form-ln">
                        Achternaam
                        <br><input type="text" name="lastname" minlength="2" class="input-text-style" required>
                    </div>
                </div>

                <div class="form-field-padding form-field-style email-form">
                    E-mailadres
                    <br><input type="email" name="email" class="input-text-style" required><br>
                </div>

                <div class="role-form form-field-padding form-field-style">
                    Rol
                    <br>
                    <select id="roles" name="role" required>
                        <optgroup label="Kies een rol">
                            <!--<option selected disabled>Kies een rol</option>-->
                            <option value="user" selected>gebruiker</option>
                            <option value="admin">admin</option>
                        </optgroup>
                    </select>
                </div>

                <div id="dropdown" class="customer-form form-field-padding form-field-style">
                    Gekoppelde klant(en)
                    <br>
                    <select id="customers" name="customers[]" required>
                        <optgroup label="Kies een klant">
                            <option value="0" selected hidden>Kies een klant</option>
                            <?php foreach($customers as $customer):?>
                                <option value="<?=$customer["customerName"]?>"><?=$customer["customerName"]?></option>
                            <?php endforeach;?>
                        </optgroup>
                    </select>
                </div>
                <div class="duplicate-button" id="poep">
                    <a id="add-field" type="button" onclick="addField();"><img src="../res/add.svg"></a>
                </div>

                <!-- end form elements -->

                <div class="footer"></div>

                <!-- buttons   -->

                <!-- buttons   -->

                <div class="footer-right">
                    <div class="buttons-form">
                        <a href="overzicht.php" target="_self">
                            <button class="button-form-secondary" type="button">Annuleren</button></a>
                            <button class="button-form-primary" type="submit"> Opslaan </button>
                            <!-- buttons -->
                            <div>
                            </form>
                        </div>
                    </body>
                    </html>
