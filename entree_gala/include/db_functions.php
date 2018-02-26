<?php

function connect_to_db()
{
    global $bd;
    try
    {
        require('data/config.php');
        $bd = new PDO('mysql:host='.$bdd_url.';dbname='.$bdd_database.';charset=utf8',$bdd_login,$bdd_password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
        $bd ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(Exeption $e)
    {
        die('erreur:'.$e->getMessage());
    }
}

function nb_participants()
{
    global $bd;
    $participants = $bd->prepare('SELECT COUNT(*) FROM guests');
    $participants->execute();
    $nb_participants = $participants->fetch()['COUNT(*)'];
    return $nb_participants;
}
function nb_entrees()
{
    global $bd;
    $entrees = $bd->prepare('SELECT COUNT(*) FROM entrees');
    $entrees->execute();
    $nb_entrees = $entrees->fetch()['COUNT(*)'];
    return $nb_entrees;
}
function all_guests($rang)
{
    global $bd;
    $liste_invite = $bd->prepare('SELECT * FROM guests LIMIT :rang,25');
    $liste_invite->bindParam('rang', $rang, PDO::PARAM_INT);
    $liste_invite->execute();
    $invite = $liste_invite->fetchAll();
    return $invite;
}
function select_single_lign($id)
{
    global $bd;
    $edit_query = $bd->prepare('SELECT * FROM guests WHERE id =:id');
    $edit_query->execute(array('id' => $id));
    $edit_data = $edit_query -> fetch();
    return $edit_data;
}

function nombre_invites($guest)
{
    global $bd;

    $nb_invite = $bd -> prepare('SELECT count(*) nb FROM icam_has_guest where icam_id=:id');
    $nb_invite -> execute(array('id' => $guest));
    $nb_invite = $nb_invite->fetch()['nb'];
    if($nb_invite == 0){
        return "";
    }
    return $nb_invite;
    
}
function set_invites($id)
{
    global $bd;
    $invites_id = $bd->prepare('SELECT guest_id FROM icam_has_guest WHERE icam_id =:icam_id');
    $invites_id -> execute(array('icam_id' => $id));
    $invites_id = $invites_id->fetchall();

    foreach($invites_id as $invite)
    {
        $invite_data = select_single_lign($invite['guest_id']);
        $invites_data[] = $invite_data;
    }
    return $invites_data;
}
function count_promo($promo)
{
    global $bd;
    $nb = $bd -> prepare('SELECT COUNT(*) nb FROM guests where promo=:promo');
    $nb -> execute(array('promo' => $promo));
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_creneau($creneau)
{
    global $bd;
    $nb = $bd -> prepare('SELECT COUNT(*) nb FROM guests where plage_horaire_entrees=:plage_horaire_entrees');
    $nb -> execute(array('plage_horaire_entrees' => $creneau));
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_conference()
{
    global $bd;
    $nb = $bd -> prepare('SELECT COUNT(*) nb FROM guests where buffet=1');
    $nb -> execute();
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_diner()
{
    global $bd;
    $nb = $bd -> prepare('SELECT COUNT(*) nb FROM guests where repas=1');
    $nb -> execute();
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_icam($condition)
{
    global $bd;
    if ($condition==true)
    {
        $nb = $bd -> prepare('SELECT COUNT(*) nb FROM guests where is_icam=1');
    }
    else
    {
        $nb = $bd -> prepare('SELECT COUNT(*) nb FROM guests where is_icam=0');
    }
    $nb -> execute();
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_bracelet($condition)
{
    global $bd;
    if ($condition==True)
    {
        $nb = $bd -> prepare('SELECT COUNT(bracelet_id) nb FROM guests');
    }
    else
    {
        $nb = $bd -> prepare('SELECT SUM(CASE WHEN bracelet_id IS NULL THEN 1 ELSE 0 END) nb FROM guests');
    }
    $nb -> execute();
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_telephone($condition)
{
    global $bd;
    if ($condition==True)
    {
        $nb = $bd -> prepare('SELECT COUNT(telephone) nb FROM guests WHERE is_icam=1');
    }
    else
    {
        $nb = $bd -> prepare('SELECT SUM(CASE WHEN telephone IS NULL THEN 1 ELSE 0 END) nb FROM guests WHERE is_icam=1');
    }
    $nb -> execute();
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_price($condition)
{
    global $bd;
    if ($condition==True)
    {
        $nb = $bd -> prepare('SELECT COUNT(price) nb FROM guests WHERE price !=0');
    }
    else
    {
        $nb = $bd -> prepare('SELECT COUNT(price) nb FROM guests WHERE price =0');
    }
    $nb -> execute();
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function count_payement($payement)
{
    global $bd;
    $nb = $bd -> prepare('SELECT COUNT(paiement) nb FROM guests WHERE paiement=:paiement');
    $nb-> execute(array('paiement' => $payement));
    $nb = $nb->fetch()['nb'];
    return $nb;
}
function determination_recherche($recherche, $rang)
{
    try{
        global $bd;
        if(strpos($recherche, " ") !== false){
            $pieces = explode(" ", $recherche);
            $requete1 = 'SELECT *, DAY(inscription), MONTH(inscription) FROM guests WHERE (nom LIKE "' . $pieces[0] . '%" AND prenom LIKE "' . $pieces[1] . '%") OR (nom LIKE "' . $pieces[1] . '%" AND prenom LIKE "' . $pieces[0] . '%")';
            $requete2 = 'SELECT count(*) FROM guests WHERE (nom LIKE "' . $pieces[0] . '%" AND prenom LIKE "' . $pieces[1] . '%") OR (nom LIKE "' . $pieces[1] . '%" AND prenom LIKE "' . $pieces[0] . '%")';
            echo $requete1;
        }else if(!is_numeric($recherche)){
            $requete1 = 'SELECT *, DAY(inscription), MONTH(inscription) FROM guests WHERE nom LIKE "%' . $recherche . '%" OR prenom LIKE "%' . $recherche . '%" LIMIT ' . $rang . ',16';
            $requete2 = 'SELECT count(*) FROM guests WHERE nom LIKE "%' . $recherche . '%" OR prenom LIKE "%' . $recherche . '%"';
        }else{
            $requete1 = 'SELECT *, DAY(inscription), MONTH(inscription) FROM guests WHERE ' . $recherche . ' = bracelet_id LIMIT ' . $rang . ',16';
            $requete2 = 'SELECT count(*) FROM guests WHERE ' . $recherche . ' = bracelet_id LIMIT ' . $rang . ',16';
        }
        
        $recherche_bdd =$bd-> prepare($requete1);
        $count_recherche =$bd->prepare($requete2);
        $count_recherche -> execute(array('recherche' => $recherche));
        $count_recherche = $count_recherche->fetch()['count(*)'];

        $recherche_bdd -> execute();
        $recherche_bdd = $recherche_bdd-> fetchall();

        $_SESSION['count_recherche'] = $count_recherche;
        $_SESSION['search_match'] = $count_recherche .' entrées correspondent à la recherche';
        return $recherche_bdd;
    }catch (Exception $e){
        return null;
    }
    
}

function set_quotas()
{
    function set_parametres()
    {
        global $bd;
        $parametre=$bd->prepare('SELECT * FROM configs');
        $parametre->execute();
        $valeurs=$parametre->fetchAll();
        for($i=4; $i<=9; $i++)
        {
            $quota = $valeurs[$i];
            $quotas[] = $quota;
        }
        return $quotas;
    }
    function set_current_status($quotas)
    {
        // var_dump($quotas);
        foreach($quotas as $quota)
        {
            switch($quota['name'])
            {
                case 'quota_conferences':
                {
                    $current = count_conference();
                    $quota['current'] = $current;
                    $quota['name'] = 'Conférence';
                    $status[]=$quota;
                    break;
                }
                case 'quota_entree_21h45_22h30':
                {
                    $horaire='21h45-22h30';
                    $current = count_creneau($horaire);
                    $quota['current'] = $current;
                    $quota['name'] = 'Premier Créneau';
                    $status[]=$quota;
                    break;
                }
                case 'quota_entree_21h_21h45':
                {
                    $horaire='21h-21h45';
                    $current = count_creneau($horaire);
                    $quota['current'] = $current;
                    $quota['name'] = 'Deuxième Créneau';
                    $status[]=$quota;
                    break;
                }
                case 'quota_entree_22h30_23h':
                {
                    $horaire='22h30-23h';
                    $current = count_creneau($horaire);
                    $quota['current'] = $current;
                    $quota['name'] = 'Troisième Créneau';
                    $status[]=$quota;
                    break;
                }
                case 'quota_repas':
                {
                    $current = count_diner();
                    $quota['current'] = $current;
                    $quota['name'] = 'Dîner';
                    $status[]=$quota;
                    break;
                }
                case 'quota_soirees':
                {
                    global $nb_total;
                    $nb_total = $quota['value'];
                    $current = nb_participants();
                    $quota['current'] = $current;
                    $quota['name'] = 'Total';
                    $status[]=$quota;
                    break;
                }
            }
        }
        return $status;
    }
    global $bd;
    global $nb_total;

    $nb_inscris = nb_participants();
    $nb_icam = count_icam(true);
    $nb_invite = count_icam(false);
    $nb_bracelets = count_bracelet(true);
    $nb_no_bracelets = count_bracelet(false);
    $nb_telephone = count_telephone(true);

    $quotas = set_parametres();
    $status = set_current_status($quotas);

    $status[]=array('name' => 'bracelets/max', 'name1' => 'nombre de bracelets distribués', 'value1' => $nb_bracelets, 'name2' => 'total de bracelets', 'value2' => 3300);
    $status[]=array('name' => 'bracelets/inscris', 'name1' => 'nombre de bracelets distribués', 'value1' => $nb_bracelets, 'name2' => 'nombre d\'inscris', 'value2' => $nb_inscris);
    $status[]=array('name' => 'telephone/icam', 'name1' => 'nombre de numéros de téléphone renseignés', 'value1' => $nb_telephone, 'name2' => 'nombre d\'Icams', 'value2' => $nb_icam);
    $status[]=array('name' => 'icam/invité', 'name1' => 'nombre d\'Icams', 'value1' => $nb_icam, 'name2' => 'nombre d\'invités', 'value2' => $nb_invite);

    // $status[]=array('name' => 'telephone/icam', 'nombre de numéros de téléphone renseignés' => $nb_telephone, 'nombre d\'Icams' => $nb_icam);
    // $status[]=array('name' => 'icam/invité', 'nombre d\'Icams' => $nb_icam, 'nombre d\'invités' => $nb_invite);

    return $status;
}
function set_has_arrived($id, $arrived)
{
    global $bd;
    date_default_timezone_set('Europe/Belgrade');
    $arrival_time = date('Y-m-d H:i:s', time());

    if(has_arrived($id)){
        $requete = 'DELETE FROM entrees WHERE guest_id = ' . $id;
    }else{
        $requete = 'INSERT INTO entrees VALUES ("' . $id . '", "' . $arrived . '", "' . $arrival_time . '")';
    }
    
    echo $requete;
    $arrival = $bd->prepare($requete);
    $arrival -> execute();
}
function has_arrived($id)
{
    global $bd;
    $recherche_bdd =$bd-> prepare('SELECT arrived FROM `entrees` WHERE guest_id = :id');
    $recherche_bdd -> bindParam('id', $id, PDO::PARAM_INT);
    $recherche_bdd -> execute();
    $recherche_bdd = $recherche_bdd->fetch()['arrived'];

    if($recherche_bdd == 0){
        return false;
    }else{
        return true;
    }
}

function is_icam($id)
{
    global $bd;
    $recherche_bdd =$bd-> prepare('SELECT is_icam FROM guests WHERE id = :id');
    $recherche_bdd -> bindParam('id', $id, PDO::PARAM_INT);
    $recherche_bdd -> execute();
    $recherche_bdd = $recherche_bdd->fetch()['is_icam'];

    if($recherche_bdd == 0){
        return false;
    }else{
        return true;
    }
}

function get_inviter($id)
{
    global $bd;
    try{
    $recherche_bdd =$bd-> prepare('SELECT icam_id FROM guests JOIN icam_has_guest WHERE icam_has_guest.guest_id = guests.id AND guests.id = :id');
    $recherche_bdd -> bindParam('id', $id, PDO::PARAM_INT);
    $recherche_bdd -> execute();
    $inviter_id = $recherche_bdd->fetch()['icam_id'];

     $recherche_bdd =$bd-> prepare('SELECT * FROM guests WHERE id = :id');
    $recherche_bdd -> bindParam('id', $inviter_id, PDO::PARAM_INT);
    $recherche_bdd -> execute();
    $recherche_bdd = $recherche_bdd-> fetchall();
    return $recherche_bdd;
}catch (Exception $e){
    return null;
}
}

function get_guests($id)
{
    global $bd;
    $recherche_bdd =$bd-> prepare('SELECT t2.nom AS nom, t2.prenom AS prenom FROM guests t1 JOIN guests t2, icam_has_guest WHERE icam_has_guest.icam_id = t1.id AND icam_has_guest.guest_id = t2.id AND t1.id = :id');
    $recherche_bdd -> bindParam('id', $id, PDO::PARAM_INT);
    $recherche_bdd -> execute();
    $recherche_bdd = $recherche_bdd-> fetchall();
    return $recherche_bdd;
}

function repas($id)
{
    global $bd;
    $recherche_bdd =$bd-> prepare('SELECT repas FROM guests WHERE id = :id');
    $recherche_bdd -> bindParam('id', $id, PDO::PARAM_INT);
    $recherche_bdd -> execute();
    $recherche_bdd = $recherche_bdd->fetch()['repas'];

    return $recherche_bdd;
}

function buffet($id)
{
    global $bd;
    $recherche_bdd =$bd-> prepare('SELECT buffet FROM guests WHERE id = :id');
    $recherche_bdd -> bindParam('id', $id, PDO::PARAM_INT);
    $recherche_bdd -> execute();
    $recherche_bdd = $recherche_bdd->fetch()['buffet'];

    return $recherche_bdd;
}