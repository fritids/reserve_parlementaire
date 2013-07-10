<!DOCTYPE html>
<html>
<head>
	<title>Réserve parlementaire</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap -->
	<link href="<?php echo $app->config('assets_path'); ?>/css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="<?php echo $app->config('assets_path'); ?>/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
	<link href="<?php echo $app->config('assets_path'); ?>/css/jquery-ui.min.css" rel="stylesheet" media="screen">
	<link href="<?php echo $app->config('assets_path'); ?>/css/chosen.css" rel="stylesheet" media="screen">
	<style type="text/css">
	#newCalc a{
		color: white;
	}
	</style>
</head>
<body>
	<script type="text/javascript">
	var path = '<?php echo $app->config('path'); ?>';
	</script>
	<div class="container" style="margin: 20px auto; max-width: 800px;">
		<div class="alert alert-info">
			Pour aficher le détail de l'utilisation de la réserve parlementaire (ou ministérielle),<br/>
			choisissez l'un (ou plusieurs) des champs ci-dessous. Le résultat s'affiche dessous.
		</div>
		<div id='form_div'>
			<form id='form_search_reserve' action="<?php echo $app->config('path'); ?>" method="POST" class="form-search">
				<input type='hidden' name='pagination' id='pagination_form' value="1"/>
				
				<div class="input-append">
					<select name="parlementaire_form" class="chzn-select" data-placeholder='Chercher par parlementaire'>
						<option value=""></option>
						<option value="">Tous les parlementaires</option>
						<?php foreach($app->config('parlementaires') as $parlementaire): ?>
							<option value="<?php echo $parlementaire; ?>"><?php echo $parlementaire; ?></option>
						<?php endforeach; ?>
					</select>
				</div> 

				<div class="input-append">
					<select name="commune_form" id="communes" class="chzn-select" data-placeholder='Chercher par communes'>
						<option value=""></option>
						<option value="">Toutes les communes</option>
					</select>
				</div> 

				<div class="input-append">
					<select name="departement_form" class="chzn-select" data-placeholder='Chercher par département'>
						<option value=""></option>
						<option value="">Tous les départements</option>
						<?php foreach($app->config('departements') as $zipcode => $departement): ?>
							<option value="<?php echo $zipcode; ?>"><?php echo $zipcode; ?> - <?php echo $departement; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</form>
		</div>

	    <table class="table table-striped table-bordered table-hover table-condensed">
	    	<caption id='table_caption' style='display: none'>
	    	</caption>
		    <thead>
			    <tr>
				    <th>département</th>
				    <th>Commune</th>
				    <th>Parlementaire</th>
				    <th>Nature de la réserve utilisée</th>
				    <th>Nature du projet</th>
				    <th>Coût du projet</th>
				    <th>Subvention allouée</th>
			    </tr>
		    </thead>
		    <tbody id='table_body'>
		    	<tr class="loading" style='display:none;'>
					<td colspan="7" style="text-align:center"><img src="<?php echo $app->config('assets_path'); ?>/img/ajax-loader.gif" /></td>
		    	</tr>
			    <?php echo $table?>
		    </tbody>
	    </table>
        <div class="pagination">
		    <ul id='pagination_body'>
		    	<?php echo $pagination; ?>
		    </ul>
	    </div>

		<div class="alert alert-error" style='display: none;'>
		</div>

		<div class="alert alert-success" style='display: none;'>
		</div>

		<span class="label label-success" id="newCalc" style='text-align: center; margin: 0px auto; max-width: 180px; display: none; font-size: 15px;'>
			<a href='#'>Recommencer le calcul</a>
		</span>
	</div>
	<script src="<?php echo $app->config('assets_path'); ?>/js/jquery.min.js"></script>
	<script src="<?php echo $app->config('assets_path'); ?>/js/jquery-ui.js"></script>
	<script src="<?php echo $app->config('assets_path'); ?>/js/bootstrap.min.js"></script>
	<script src="<?php echo $app->config('assets_path'); ?>/js/chosen.jquery.min.js"></script>
	<script src="<?php echo $app->config('assets_path'); ?>/js/chosen-ajax.jquery.min.js"></script>
	<script src="<?php echo $app->config('assets_path'); ?>/js/post.js"></script>
</body>
</html>
