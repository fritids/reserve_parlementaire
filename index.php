<?php

$path_du_site = $_SERVER['REQUEST_URI'];

require 'vendor/autoload.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->config(array(
	'templates.path' => './views/',
	'assets_path' => $path_du_site.'assets',
	'path' => $path_du_site,
	'total' => include 'assets/data/total.php',
	'communes' => include 'assets/data/communes.php',
	'departements' => include 'assets/data/departements.php',
	'parlementaires' => include 'assets/data/parlementaires.php'
));

$data = array('app' => $app);


/**
* Affichage du formulaire
**/
$app->get('/', function () use ($app, $data) {
	$limit = 20;
	$total = $app->config('total');
	$data['pagination'] =  pagination_to_html(1, sizeof($total), $limit);
	$total = array_slice($total, 0, $limit);
	$data['table'] = array_to_html($total, $app);
	$app->render('form.php', $data);
});

$app->post('/', function () use($app, $data) {
	$page = $_POST['pagination'];
	$limit = 20;
	$total = $app->config('total');
	
	$app->somme_totale = 0;

	$return = array();
	$return['status'] = 'success';
	$return['data'] = array();
	$return['data']['caption'] = '';

	if ($_POST['parlementaire_form'] || $_POST['commune_form'] || $_POST['departement_form']) {
		$total = array_filter($total, function($elem) use($app) {

			$calc_done = FALSE;
			if ($_POST['parlementaire_form'] != '') {
				$return = FALSE;
				if (isset($elem[5]) && (stripos($elem[5], $_POST['parlementaire_form']) !== FALSE) && isset($elem[4])) {
					$app->somme_totale += $elem[4];
					$calc_done = TRUE;
				} else {
					return FALSE;
				}
			}

			if ($_POST['commune_form'] != '') {
				$return = FALSE;
				if (isset($elem[0]) && (stripos($elem[0], $_POST['commune_form']) !== FALSE) && isset($elem[4])) {
					if (!$calc_done) {
						$app->somme_totale += $elem[4];
						$calc_done = TRUE;
					}
				} else {
					return FALSE;
				}
			}

			if ($_POST['departement_form'] != '') {
				$return = FALSE;
				if (isset($elem[1]) && ($elem[1] == $_POST['departement_form']) && isset($elem[4])) {
					if (!$calc_done) {
                                                $app->somme_totale += $elem[4];
                                                $calc_done = TRUE;
                                        }
				} else {
					return FALSE;
				}
			}

			return TRUE;
		});

		if (sizeof($total) > 0) {
			$return['data']['caption'] = get_caption($app);
		}
	}


	$return['data']['pagination'] =  pagination_to_html($page, sizeof($total), $limit);

	$total = array_slice($total, ($page - 1) * $limit, $limit);
	$return['data']['table'] = array_to_html($total, $app);

	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($return);
	exit;

	$app->render('form.php', $data);
});

/**
* Recherche des communes
**/
$app->post('/search/communes', function () use ($app, $data) {

	$communes = $app->config('communes');
	$values = array();
	$values['term'] = $_POST['term'];
	$values['items'] = array_filter($communes, function($value) {
		$term = $_POST['term'];
		return (stripos($value, $term) !== FALSE);
	});
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($values);
	exit;
});

$app->run();

function get_caption($app) {
	$caption_header = 'Résultat de votre recherche:';

	$caption_body = 'Total des subventions allouées';
	if ($_POST['parlementaire_form'] != '') {
		$caption_body .= ' par <b>'.$_POST['parlementaire_form'].'</b>';
	}

	if ($_POST['commune_form'] != '') {
		$caption_body .= ' pour la commune <b>'.$_POST['commune_form'].'</b>';
	}

	if ($_POST['departement_form'] != '') {
		$dpt = $app->config('departements');

		if (isset($dpt[$_POST['departement_form']])) {
			$caption_body .= ' pour le département <b>'.$dpt[$_POST['departement_form']].'</b>';
		}
	}

	$caption_body .= ': '.$app->somme_totale.'&euro;';
 
	return '<p class="text-left alert alert-success">'.$caption_header.'<br/>'.$caption_body.'</p>';
}

function array_to_html($array, $app) {
	$result = '';
	$dpt = $app->config('departements');
	foreach($array as $elem) {
		$result .= '<tr>';
			$result .= !isset($elem[1]) ? '<td>-</td>' : '<td>' .$elem[1]. ' - '.$dpt[$elem[1]].'</td>';
			$result .= !isset($elem[0]) ? '<td>-</td>' : '<td>' .$elem[0]. '</td>';
			$result .= !isset($elem[5]) ? '<td>-</td>' : '<td>' .$elem[5]. '</td>';
			$result .= !isset($elem[6]) ? '<td>-</td>' : '<td>' .$elem[6]. '</td>';
			$result .= !isset($elem[2]) ? '<td>-</td>' : '<td>' .$elem[2]. '</td>';
			$result .= !isset($elem[3]) ? '<td>-</td>' : '<td>' .$elem[3]. '</td>';
			$result .= !isset($elem[4]) ? '<td>-</td>' : '<td>' .$elem[4]. '</td>';
		$result .= '</tr>';
	}
	return $result;
}

function pagination_to_html($current, $total_pages, $limit) {

	$adjacents = 2;
	$page = $current;
									//how many items to show per page
	if($page) 
		$start = ($page - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0
	
	$prev = $page - 1;							//previous page is page - 1
	$next = $page + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{	
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= '<li class="active" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';
				else
					$pagination.= '<li class="" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="active" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';
					else
						$pagination.= '<li class="" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';				
				}
				$pagination.= '<li class="" value="'.$next.'"><a href="'.$next.'">&gt;</a></li>';	
				$pagination.= '<li class="" value="'.$lastpage.'"><a href="'.$lastpage.'">&gt;&gt;</a></li>';	
			
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				
				$pagination.= '<li class="" value="1"><a href="1">&lt;&lt;</a></li>';	
				$pagination.= '<li class="" value="'.$prev.'"><a href="'.$prev.'">&lt;</a></li>';


				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="active" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';	
					else
						$pagination.= '<li class="" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';	
				}
				$pagination.= '<li class="" value="'.$next.'"><a href="'.$next.'">&gt;</a></li>';	
				$pagination.= '<li class="" value="'.$lastpage.'"><a href="'.$lastpage.'">&gt;&gt;</a></li>';	
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= '<li class="" value="1"><a href="1">&lt;&lt;</a></li>';	
				$pagination.= '<li class="" value="'.$prev.'"><a href="'.$prev.'">&lt;</a></li>';

				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="active" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';	
					else
						$pagination.= '<li class="" value="'.$counter.'"><a href="#">'.$counter.'</a></li>';	
				}
			}
		}
		
	}
	return $pagination;
}

