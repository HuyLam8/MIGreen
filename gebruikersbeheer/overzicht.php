<?php
  include('../header/header.php'); 
  include('../autorisatie/UserDaoMysql.php');      
?> 

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <link rel="stylesheet" type="text/css" href="overzicht.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <script type="text/javascript" src="../js/overzichtFunctions.js"></script>

  <meta charset="utf-8">
  <title>Gebruikersoverzicht</title>
</head>

<body>
  <div class="grid-container">
    <div class="header-left">
      <h1>Home</h1>
      <h2>Gebruikersoverzicht</h2>
    </div>
    <div class="header-mid"></div>
    <div class="header-right"><button class="new-user-button" type="button" name="button">Nieuwe gebruiker aanmaken</button></div>
    <div class="content">

    <table>
        <thead>
          <tr>
           <th>GEBRUIKERSNAAM</th>
           <th>VOORNAAM</th>
           <th>ACHTERNAAM</th>
           <th>ROL</th>
           <th></th>
         </tr>
       </thead>
       <tbody>

      <?php 
        $userDao = new UserDaoMysql();
        $users = $userDao-> selectViewCurrentUsers(); 
      ?>

        <?php foreach($users as $user):
           $username = $user["userName"];?>
          <tr>
            <td><?=$user["userName"] ?></td>
            <td><?=$user["firstname"] ?></td>
            <td><?=$user["lastname"] ?></td>
            <td><?=$user["role"] ?></td>
            <td class="icon-cell">
                <a href="../gebruikersbeheer/overzicht.php?action=edit"><i class="fas fa-pencil-alt glyph-icon"></i></a>
                <a href="../gebruikersbeheer/overzicht.php?action=delete&userName=<?php echo $username; ?>"><i class="fas fa-trash-alt glyph-icon" onclick="return confirmDelete('<?php echo $username ?>');"></i></a>
            </td>
          </tr>
        <?php endforeach;?>

      </tbody>
  </div>
</div>

<?php

   if (! isset($_GET["action"])) {
        $action = "Home";
    } else {
        echo "action wordt gezet naar parameter<br>";
        $action = $_GET["action"];
    }
    
   if (! isset($_GET["userName"])) {
         $userName = null;
     } else {
         $userName = $_GET["userName"];
       echo "$userName<br>";
     }

    switch ($action) {
        case "edit":
            echo "hier komt edit()";
            break;
        case "delete":
            echo "Delete functie wordt opgeroepen in switch action<br>";
            delete($userName, $userDao);
            break;
    }

    function delete($name, $dao) {
     echo "Hello from the inside of the delete function :)<br> You are now deleting " . $name;
        $succes = $dao->deactivateUser($name);
//        var_dump $succes;
        header("Location: ../gebruikersbeheer/overzicht.php");
        
    }
      
      
//     function delete()
//     {
//         if ($_SESSION['username'] == $userName) {
//           echo "Je kunt niet jezelf verwijderen dummy!";
//         } else {
//           if ($userDao-> deleteUser($userName)) {
//             echo "Gebruiker verwijderd";
//           } else {
//             echo "Gebruiker kan niet verwijderd worden";
//           }
//         }
//         
//     }

?>

</body>

</html>

