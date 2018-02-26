<div class="row">
    <div class="col-md-5">
        <p><h3>Liste des participants au Gala</h3></p>
        <p>Actuellement <?php echo htmlspecialchars($number_participants) ?> invités</p>
    </div>
</div>
<form action="index.php" method="post">
    <div class="row">
        <div class= "col-md-3">
            <input type="input-medium search-query" class="form-control" name ="recherche" id="recherche" placeholder="Nom, prénom, initiales..."
            value="<?php if(isset($_POST['recherche'])){echo htmlspecialchars($_POST['recherche']);} ?>">
        </div>
        <button class="btn btn-primary" type="submit">Rechercher</button>

        <div class="col-md-3">
            <a href="secured/ajouter_invite.php" class="btn btn-primary">Ajouter un invité</a>
        </div>
    </div>
</form>
<br/>
<div> <h1 class="numero_page"> Page <?php echo htmlspecialchars($current_page). "/" . htmlspecialchars($total_number_pages); ?> </h1></div>