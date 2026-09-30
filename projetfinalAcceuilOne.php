<?php

include("connectDB.inc.php");


/*
=========================================================
1. SUPPRESSION D'UNE TACHE
=========================================================
*/

if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {

    $id = (int) $_GET['supprimer'];

    $queryDelete = "
        DELETE FROM tache
        WHERE id_tache = ?
    ";

    $stmtDelete = mysqli_prepare($link, $queryDelete);

    if (!$stmtDelete) {
        die("Erreur de préparation de la suppression : " . mysqli_error($link));
    }

    mysqli_stmt_bind_param(
        $stmtDelete,
        "i",
        $id
    );

    if (!mysqli_stmt_execute($stmtDelete)) {
        die("Erreur lors de la suppression : " . mysqli_stmt_error($stmtDelete));
    }

    mysqli_stmt_close($stmtDelete);

    /*
    Redirection pour éviter que le navigateur
    réexécute la suppression lors d'un actualisation.
    */

    header("Location: projetfinalAcceuil.php");
    exit;
}


/*
=========================================================
2. MODIFICATION D'UNE TACHE
=========================================================
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['modifier'])) {

    /*
    Récupération des données envoyées
    */

    $id_tache = (int) ($_POST['id_tache'] ?? 0);

    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $date_debut = $_POST['date_debut'] ?? '';
    $date_echeance = $_POST['date_echeance'] ?? '';

    $id_categorie = (int) ($_POST['id_categorie'] ?? 0);
    $id_effort = (int) ($_POST['id_effort'] ?? 0);
    $id_priorite = (int) ($_POST['id_priorite'] ?? 0);
    $id_statut = (int) ($_POST['id_statut'] ?? 0);


    /*
    Vérification minimale
    */

    if (
        $id_tache <= 0 ||
        $titre === '' ||
        $date_debut === '' ||
        $date_echeance === '' ||
        $id_categorie <= 0 ||
        $id_effort <= 0 ||
        $id_priorite <= 0 ||
        $id_statut <= 0
    ) {

        die("Données de modification invalides.");

    }


    /*
    UPDATE SQL
    */

    $queryUpdate = "
        UPDATE tache
        SET
            titre = ?,
            description = ?,
            date_debut = ?,
            date_echeance = ?,
            id_categorie = ?,
            id_effort = ?,
            id_priorite = ?,
            id_statut = ?

        WHERE id_tache = ?
    ";

    $stmtUpdate = mysqli_prepare($link, $queryUpdate);

    if (!$stmtUpdate) {
        die("Erreur de préparation de la modification : " . mysqli_error($link));
    }


    /*
    9 paramètres :

    titre
    description
    date_debut
    date_echeance
    id_categorie
    id_effort
    id_priorite
    id_statut
    id_tache

    s = string
    i = integer
    */

    mysqli_stmt_bind_param(
        $stmtUpdate,
        "ssssiiiii",
        $titre,
        $description,
        $date_debut,
        $date_echeance,
        $id_categorie,
        $id_effort,
        $id_priorite,
        $id_statut,
        $id_tache
    );


    if (!mysqli_stmt_execute($stmtUpdate)) {
        die("Erreur lors de la modification : " . mysqli_stmt_error($stmtUpdate));
    }

    mysqli_stmt_close($stmtUpdate);


    /*
    Retour au tableau
    */

    header("Location: projetfinalAcceuil.php");
    exit;
}


/*
=========================================================
3. REQUETE POUR AFFICHER LES TACHES
=========================================================
*/

$query = "
    SELECT
        tache.id_tache,
        tache.titre,
        tache.description,
        tache.date_debut,
        tache.date_echeance,

        categorie.id_categorie,
        categorie.libelle_categorie,

        effort.id_effort,
        effort.libelle_effort,

        priorite.id_priorite,
        priorite.libelle_priorite,

        statut.id_statut,
        statut.libelle_statut

    FROM tache

    INNER JOIN categorie
        ON tache.id_categorie = categorie.id_categorie

    INNER JOIN effort
        ON tache.id_effort = effort.id_effort

    INNER JOIN priorite
        ON tache.id_priorite = priorite.id_priorite

    INNER JOIN statut
        ON tache.id_statut = statut.id_statut

    WHERE tache.id_tache IS NOT NULL

    ORDER BY tache.id_tache DESC
";


$result = mysqli_query($link, $query);

if (!$result) {
    die("Erreur SQL : " . mysqli_error($link));
}

$count = mysqli_num_rows($result);

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Liste des tâches</title>

    <style>

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

        table {
            table-layout: auto;
            border: 2px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #eee;
        }

        input,
        textarea,
        select {
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            min-height: 80px;
        }

        .actions {
            white-space: nowrap;
        }



        footer {
            background-color: #0d387c;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 50px;
        }


    </style>

</head>

<body>

<header>
    <h1>Application prise de notes TP WEB dev Unistra</h1>
</header>

<p>
    <a href="projetfinal.php?id_titre">
        Création note
    </a>
</p>


<table>

    <caption>
        Nombre d'enregistrement :
        <?php echo $count; ?>
    </caption>


    <thead>

        <tr>

            <th>Titre</th>
            <th>Description</th>
            <th>Priorité</th>
            <th>Catégorie</th>
            <th>Effort</th>
            <th>Statut</th>
            <th>Date de début</th>
            <th>Date d'échéance</th>
            <th>Modifier</th>
            <th>Supprimer</th>

        </tr>

    </thead>


    <tbody>


<?php

while ($var = mysqli_fetch_assoc($result)) {

    /*
    =============================================
    VARIABLES DE LA LIGNE
    =============================================
    */

    $id_tache = (int) $var['id_tache'];

    $titre = htmlspecialchars($var['titre']);
    $description = htmlspecialchars($var['description']);

    $date_debut = htmlspecialchars($var['date_debut']);
    $date_echeance = htmlspecialchars($var['date_echeance']);

    $libelle_priorite = htmlspecialchars($var['libelle_priorite']);
    $libelle_categorie = htmlspecialchars($var['libelle_categorie']);
    $libelle_effort = htmlspecialchars($var['libelle_effort']);
    $libelle_statut = htmlspecialchars($var['libelle_statut']);


    /*
    =====================================================
    SI L'UTILISATEUR A CLIQUE SUR MODIFIER
    =====================================================
    */

    $modeModification = (
        isset($_GET['modifier'])
        && is_numeric($_GET['modifier'])
        && (int)$_GET['modifier'] === $id_tache
    );


    if ($modeModification) {

?>


        <!-- ==========================================
             LIGNE EN MODE MODIFICATION
        =========================================== -->

        <tr>

            <form method="POST" action="projetfinalAcceuil.php">

                <input
                    type="hidden"
                    name="id_tache"
                    value="<?php echo $id_tache; ?>"
                >


                <td>

                    <input
                        type="text"
                        name="titre"
                        value="<?php echo $titre; ?>"
                        required
                    >

                </td>


                <td>

                    <textarea
                        name="description"
                    ><?php echo $description; ?></textarea>

                </td>


                <td>

                    <select name="id_priorite" required>

                        <?php

                        /*
                        Récupération des priorités
                        */

                        $queryPriorite = "
                            SELECT id_priorite, libelle_priorite
                            FROM priorite
                            ORDER BY id_priorite
                        ";

                        $resultPriorite = mysqli_query(
                            $link,
                            $queryPriorite
                        );

                        while (
                            $priorite = mysqli_fetch_assoc($resultPriorite)
                        ) {

                            $selected = (
                                $priorite['id_priorite']
                                == $var['id_priorite']
                            )
                            ? 'selected'
                            : '';

                            echo "
                                <option
                                    value='{$priorite['id_priorite']}'
                                    $selected
                                >
                                    " . htmlspecialchars(
                                        $priorite['libelle_priorite']
                                    ) . "
                                </option>
                            ";
                        }

                        ?>

                    </select>

                </td>


                <td>

                    <select name="id_categorie" required>

                        <?php

                        $queryCategorie = "
                            SELECT
                                id_categorie,
                                libelle_categorie
                            FROM categorie
                            ORDER BY libelle_categorie
                        ";

                        $resultCategorie = mysqli_query(
                            $link,
                            $queryCategorie
                        );

                        while (
                            $categorie = mysqli_fetch_assoc(
                                $resultCategorie
                            )
                        ) {

                            $selected = (
                                $categorie['id_categorie']
                                == $var['id_categorie']
                            )
                            ? 'selected'
                            : '';

                            echo "
                                <option
                                    value='{$categorie['id_categorie']}'
                                    $selected
                                >
                                    " . htmlspecialchars(
                                        $categorie['libelle_categorie']
                                    ) . "
                                </option>
                            ";
                        }

                        ?>

                    </select>

                </td>


                <td>

                    <select name="id_effort" required>

                        <?php

                        $queryEffort = "
                            SELECT
                                id_effort,
                                libelle_effort
                            FROM effort
                            ORDER BY id_effort
                        ";

                        $resultEffort = mysqli_query(
                            $link,
                            $queryEffort
                        );

                        while (
                            $effort = mysqli_fetch_assoc($resultEffort)
                        ) {

                            $selected = (
                                $effort['id_effort']
                                == $var['id_effort']
                            )
                            ? 'selected'
                            : '';

                            echo "
                                <option
                                    value='{$effort['id_effort']}'
                                    $selected
                                >
                                    " . htmlspecialchars(
                                        $effort['libelle_effort']
                                    ) . "
                                </option>
                            ";
                        }

                        ?>

                    </select>

                </td>


                <td>

                    <select name="id_statut" required>

                        <?php

                        $queryStatut = "
                            SELECT
                                id_statut,
                                libelle_statut
                            FROM statut
                            ORDER BY id_statut
                        ";

                        $resultStatut = mysqli_query(
                            $link,
                            $queryStatut
                        );

                        while (
                            $statut = mysqli_fetch_assoc($resultStatut)
                        ) {

                            $selected = (
                                $statut['id_statut']
                                == $var['id_statut']
                            )
                            ? 'selected'
                            : '';

                            echo "
                                <option
                                    value='{$statut['id_statut']}'
                                    $selected
                                >
                                    " . htmlspecialchars(
                                        $statut['libelle_statut']
                                    ) . "
                                </option>
                            ";
                        }

                        ?>

                    </select>

                </td>


                <td>

                    <input
                        type="date"
                        name="date_debut"
                        value="<?php echo $date_debut; ?>"
                        required
                    >

                </td>


                <td>

                    <input
                        type="date"
                        name="date_echeance"
                        value="<?php echo $date_echeance; ?>"
                        required
                    >

                </td>


                <td class="actions">

                    <button
                        type="submit"
                        name="modifier"
                    >
                        Enregistrer
                    </button>

                    <a href="projetfinalAcceuil.php">
                        Annuler
                    </a>

                </td>


                <td class="actions">

                    <a
                        href="projetfinalAcceuil.php?supprimer=<?php echo $id_tache; ?>"
                        onclick="return confirm('Voulez-vous vraiment supprimer cette tâche ?');"
                    >
                        Supprimer
                    </a>

                </td>


            </form>

        </tr>


<?php

    } else {

?>


        <!-- ==========================================
             LIGNE NORMALE
        =========================================== -->

        <tr>

            <td>
                <?php echo $titre; ?>
            </td>

            <td>
                <?php echo $description; ?>
            </td>

            <td>
                <?php echo $libelle_priorite; ?>
            </td>

            <td>
                <?php echo $libelle_categorie; ?>
            </td>

            <td>
                <?php echo $libelle_effort; ?>
            </td>

            <td>
                <?php echo $libelle_statut; ?>
            </td>

            <td>
                <?php echo $date_debut; ?>
            </td>

            <td>
                <?php echo $date_echeance; ?>
            </td>


            <!-- MODIFIER -->

            <td class="actions">

                <a
                    href="projetfinalAcceuil.php?modifier=<?php echo $id_tache; ?>"
                >
                    Modifier
                </a>

            </td>


            <!-- SUPPRIMER -->

            <td class="actions">

                <a
                    href="projetfinalAcceuil.php?supprimer=<?php echo $id_tache; ?>"
                    onclick="return confirm('Voulez-vous vraiment supprimer cette tâche ?');"
                >
                    Supprimer
                </a>

            </td>

        </tr>


<?php

    }

}

?>

    </tbody>

</table>

<footer>
    <p><strong>Fait par l'Équipe 3 :</strong> Ngoum Diana &bull; Chenal Léo &bull; Mbougueng David &bull; Lindor Carl-André &bull; Balla Tabi Jean-Luc</p>
</footer>

</body>

</html>


<?php

mysqli_free_result($result);

mysqli_close($link);

?>