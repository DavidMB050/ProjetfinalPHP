
<?php 
include("connectDB.inc.php");

/*
if ( array_key_exists('id_tache', $_GET ) and is_numeric ($_GET ['id_tache'])){
    $id_tache = intval($_GET ['id_tache']);

}

else{
exit();
};
*/


/*  Mise en place des requêtes pour afficher les options du formulaire */

$querryTache = "SELECT tache.id_tache, tache.titre, tache.description,
 tache.date_debut, tache.date_echeance
 FROM `tache`";


$querryCategorie = "SELECT * 
    FROM `categorie`";

$querryEffort = "SELECT * 
    FROM `effort`";

$querryPriorite = "SELECT * 
    FROM `priorite`";

$querryStatut = "SELECT * 
    FROM `statut`";




/* Mise en place de variables pour stocker les résultats des requêtes */

$resultTache = mysqli_query($link,$querryTache) or die ($querryTache.''.mysqli_error($link));
$resultCategorie = mysqli_query($link,$querryCategorie) or die ($querryCategorie.''.mysqli_error($link));
$resultEffort = mysqli_query($link,$querryEffort) or die ($querryEffort.''.mysqli_error($link));
$resultPriorite = mysqli_query($link,$querryPriorite) or die ($querryPriorite.''.mysqli_error($link));
$resultStatut = mysqli_query($link,$querryStatut) or die ($querryStatut.''.mysqli_error($link));

/* Mise en place de variables sous forme de tableaux/listes pour stocker les résultats des requêtes */

$listeTache= array();
$listeCategorie= array();
$listeEffort= array();
$listePriorite= array();
$listeStatut= array();




while($var = mysqli_fetch_assoc($resultTache)){
    $listeTache [$var['id_tache']] = $var ['titre'];
   
}; 


while($var = mysqli_fetch_assoc($resultCategorie)){
    $listeCategorie [$var['id_categorie']] = $var ['libelle_categorie'];
    
}; 



while($var = mysqli_fetch_assoc($resultEffort)){
    $listeEffort [$var['id_effort']] = $var ['libelle_effort'];
    
}; 


while($var = mysqli_fetch_assoc($resultPriorite)){
   $listePriorite [$var['id_priorite']] = $var ['libelle_priorite'];
    
}; 


while($var = mysqli_fetch_assoc($resultStatut)){
    $listeStatut [$var['id_statut']] = $var ['libelle_statut'];
}; 


/* Stockage des nouvelles valeurs de tâches dans des variables PHP */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // traitement

$dateDebut = $_POST['dateDebut'];
$dateEcheance = $_POST['dateEcheance'];
$titreTache = $_POST['titreTache'];
$descriptionTache = $_POST['descriptionTache'];
$id_priorite = $_POST['selectPriorite'];
$id_effort = $_POST['selectEffort'];
$id_categorie = $_POST['selectCategorie'];
$id_statut = $_POST['selectStatut'];


/* Requêtes SQL pour l'insertions de nouvelles tâches dans le tableau des tâches */


    $querryInsertTache = "
        INSERT INTO tache
        (
            date_debut,
            date_echeance,
            titre,
            description,
            id_effort,
            id_categorie,
            id_priorite,
            id_statut
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";


  /* =================================================
       5. PRÉPARATION DE LA REQUÊTE
       ================================================= */

    $stmt = mysqli_prepare($link, $querryInsertTache);


    if (!$stmt) {

        die("Erreur préparation SQL : " . mysqli_error($link));
    }


    /* =================================================
       6. ASSOCIATION DES VARIABLES
       ================================================= */

    mysqli_stmt_bind_param(
        $stmt,
        "ssssiiii",
        $dateDebut,
        $dateEcheance,
        $titreTache,
        $descriptionTache,
        $id_effort,
        $id_categorie,
        $id_priorite,
        $id_statut
        );


       
    /* =================================================
       7. EXÉCUTION
       ================================================= */

    if (mysqli_stmt_execute($stmt)) {

        echo "<p style='color: green; text-align: center;'>
                La tâche a bien été enregistrée.
              </p>";

    } else {

        echo "<p style='color: red; text-align: center;'>
                Erreur lors de l'enregistrement : "
                . mysqli_stmt_error($stmt) .
             "</p>";
    }


    mysqli_stmt_close($stmt);

};

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet To-Do List - Équipe 3</title>
    <style>
        /* ==========================================
           1. STYLE GÉNÉRAL DE LA PAGE (MAQUETTE)
           ========================================== */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9; /* Fond gris très clair de la maquette */
            margin: 0;
            padding: 0;
        }

        
        header {
            background-color: #0d387c;
            color: white;
            text-align: center;
            padding: 15px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        main {
            max-width: 1300px;
            margin: 20px auto;
            padding: 0 15px;
            box-sizing: border-box;
        }

        /* ==========================================
           2. FORMULAIRE DE SAISIE 
           ========================================== */
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e1e4e8;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            max-width: 600px;
            margin: 0 auto 40px auto;
            box-sizing: border-box;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #555;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="date"], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            background-color: #ffffff;
            font-family: inherit;
        }

        textarea {
            height: 80px;
            resize: none;
        }

        .Ensemblebouton {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .Ensemblebouton label, .Ensemblebouton select {
            width: 47%;
            margin: 0;
        }

        /* Couleurs exactes des menus déroulants de la photo */
        .selectprio { background-color: #f5c236; color: #333; font-weight: bold; }
        .selectstat { background-color: #f26622; color: white; font-weight: bold; }
        .selectcat { background-color: #16823c; color: white; font-weight: bold; }
        .selecteffort { background-color: #69db7c; color: #2b2b2b; font-weight: bold; }

        .btn-sauvegarder {
            background-color: #0d387c;
            color: white;
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 6px;
            width: 100%;
            margin-top: 25px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-sauvegarder:hover {
            background-color: #1c4a94;
        }

        
        .board-title {
            text-align: center;
            color: #0d387c;
            margin-bottom: 25px;
        }

        /* PAR DÉFAUT (MOBILE FIRST) : On force l'affichage vertical block */
        .board {
            display: block; 
            width: 100%;
        }

        /* Chaque colonne prend toute la largeur sur mobile */
        .colonne {
            width: 100%; 
            display: block;
            background-color: #ebecf0; 
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px; 
            min-height: 120px;
            border: 1px solid #e1e4e8;
            box-sizing: border-box;
        }

        .colonne h3 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #0d387c;
            color: #0d387c;
            text-align: center;
        }

        /* Style des cartes de tâches */
        .carte-tache {
            background-color: white;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #6c757d;
        }

        /* Bordure dynamique de la carte selon sa colonne */
        .zone-1 .carte-tache { border-left-color: #f26622; } /* À débuter */
        .zone-2 .carte-tache { border-left-color: #2475f2; } /* En cours */
        .zone-4 .carte-tache { border-left-color: #8c939d; } /* Annulé */

        .carte-tache h4 {
            margin: 0 0 5px 0;
            color: #0d387c;
        }

        .carte-tache p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #555;
        }

        .carte-dates {
            font-size: 11px;
            color: #666;
            margin-bottom: 8px;
            font-style: italic;
        }

        .carte-details {
            font-size: 12px;
            color: #333;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            line-height: 1.5;
        }

        /* ==========================================
           4. STYLE DES BOUTONS DE STATUT (ÉTAPE 3D)
           ========================================== */
        .carte-tache div:nth-of-type(3) {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .btn-statut {
            background-color: white;
            font-weight: bold;
            border: 2px solid #ccc;
            padding: 6px 8px;
            font-size: 11px;
            border-radius: 4px;
            cursor: pointer;
            flex: 1 1 auto; /* S'adaptent sur mobile */
            text-align: center;
        }

        .btn-debuter { border-color: #f26622; color: #f26622; }
        .btn-encours { border-color: #2475f2; color: #2475f2; }
        .btn-termine { border-color: #16823c; color: #16823c; }
        .btn-annule  { border-color: #8c939d; color: #8c939d; }

        /* Étape 3D : Bouton du statut actuel désactivé */
        .btn-statut:disabled {
            background-color: #e9ecef;
            opacity: 0.4;
            cursor: not-allowed;
            border-style: dashed;
        }

        .btn-supprimer {
            background-color: #fff5f5;
            color: #e03131;
            border: 1px solid #ffc9c9;
            padding: 8px;
            font-size: 12px;
            border-radius: 4px;
            width: 100%;
            margin-top: 12px;
            cursor: pointer;
            font-weight: bold;
        }

        /* Hachure/Barre le texte si dans la colonne Annulé */
        .zone-4 .carte-tache h4,
        .zone-4 .carte-tache p,
        .zone-4 .carte-tache .carte-dates,
        .zone-4 .carte-tache .carte-details {
            text-decoration: line-through;
            color: #8c939d;
        }

        .zone-4 .carte-tache {
            opacity: 0.7;
            background-color: #f8f9fa;
        }

        footer {
            background-color: #0d387c;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 50px;
        }

        /* ==========================================
           5. Responsive (ORDINATEUR >= 768px)
           ========================================== */
        @media screen and (min-width: 768px) {
            main {
                margin: 30px auto;
                padding: 0 20px;
            }

            .board {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                gap: 20px;
            }

            .colonne {
                flex: 1;
                margin-bottom: 0; 
                min-height: 450px; 
            }

            form {
                padding: 25px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Application prise de notes TP WEB dev Unistra</h1>
</header>

<main>

<p><a href="projetfinalAcceuilOne.php"> Acceuil </a></p>

    <!-- FORMULAIRE DE SAISIE -->
    <form id="formTache" method="POST">

   

        <div class="divdates">
            <label>Date début :</label>
            <input type="date" class="datedebut" id="dateDebut" required  name="dateDebut">
        </div>  
        
        <label class="labeltitre">Titre :</label>
        <input type="text" required class="titrenote" id="titreTache" name="titreTache" placeholder="TP CSS">

        <label>Description :</label>
        <textarea id="descriptionTache" name="descriptionTache" placeholder="Lorem ipsum..."></textarea>



        <div class="Ensemblebouton">
            <label class="labelprio">Priorité :</label>
            <select class="selectprio" id="selectPriorite" name="selectPriorite">
                
            
         <?php 

                 foreach ($listePriorite as $keyIdpriorite => $valuepriorite) {
                    echo "<option value = \"$keyIdpriorite\"> $valuepriorite</option>";

                 };
                


                 ?> 



            </select>

            <label class="labelsat">Statut :</label>
            <select class="selectstat" id="selectStatut" name="selectStatut">
                <!-- Rempli dynamiquement par JavaScript (Étape 3C) -->
                  <?php 
                  
                 foreach ($listeStatut as $keyIdstatut => $valuestatut) {
                    echo "<option value = \"$keyIdstatut\"> $valuestatut</option>";

                 };
                
                 ?> 

            </select>


            <label class="labelcat">Catégorie :</label>
            <select class="selectcat" id="selectCategorie" name="selectCategorie">
                <!-- Rempli dynamiquement par JavaScript (Étape 3C) -->

                 <?php 
                 
                 foreach ($listeCategorie as $keyIdcategorie => $valuecategorie) {
                    echo "<option value = \"$keyIdcategorie\"> $valuecategorie </option>";

                 };
                
                 ?> 

            </select>


            <label class="labeleffort">Effort :</label>
            <select class="selecteffort" id="selectEffort"  name="selectEffort">
                <!-- Rempli dynamiquement par JavaScript (Étape 3C) -->

                 <?php 
                 
                 foreach ($listeEffort as $keyIdeffort => $valueeffort) {
                    echo "<option value = \"$keyIdeffort\"> $valueeffort </option>";

                 };
                
                 ?> 


            </select>
        </div>

        <div class="divdates">
             <label>Date échéance :</label>
             <input type="date" class="datefin" id="dateEcheance" required name="dateEcheance">
        </div>
        
        <button type="submit" class="btn-sauvegarder" > sauvegarder </button>
    </form>



</main>

<footer>
    <p><strong>Fait par l'Équipe 3 :</strong> Ngoum Diana &bull; Chenal Léo &bull; Mbougueng David &bull; Lindor Carl-André &bull; Balla Tabi Jean-Luc</p>
</footer>


</body>
</html>
