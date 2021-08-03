<?php 

function getURL($url){
	$ch = curl_init();
	try {
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);   
		//curl_setopt($ch, CURLOPT_TIMEOUT, 5);         
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		//curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		//$response = 
		return curl_exec($ch);

		if (curl_errno($ch)) {
			echo curl_error($ch);
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == intval(200)) {
			//echo $response;
		} else {
			echo "Ressource introuvable : " . $http_code;
		}
	} catch (\Throwable $th) {
		throw $th;
	} finally {
		curl_close($ch);
	}
}

function getLastEdit($url){
	return;
}

function getTechno($url){
	$url = str_replace(array("http://", "https://" , "www."), "", $url);
	$url = 'https://www.wappalyzer.com/lookup/' . $url . '/';

	$htmlTargetSite = getURL($url);

	if(str_contains($htmlTargetSite, 'Node.js') == true){ return 'Node.js'; }
	if(str_contains($htmlTargetSite, 'php') == true){ return 'php'; }
	return;
}

function VerifyMentions($url){

	$htmlTargetSite = getURL($url);
	
	if(strpos($htmlTargetSite, 'Mentions') == true || strpos($htmlTargetSite, 'mentions') == true || strpos($htmlTargetSite, 'cookie') == true|| strpos($htmlTargetSite, 'Cookies') == true || strpos($htmlTargetSite, 'cookies') == true || strpos($htmlTargetSite, 'Confidentialité') == true || strpos($htmlTargetSite, 'confidentialité') == true || strpos($htmlTargetSite, 'cookie') == true || strpos($htmlTargetSite, 'protection') == true || strpos($htmlTargetSite, 'Protection') == true){
		 return 'Présent'; 
	}
	else{
		  return 'Absent';
	}
	return;
}

function containEmailInUrl($url) {
    //curl
    // verify with regex

    $htmlTargetSite = getURL($url);
    $mailRegex = '#\s+[A-Za-z0-9_-]{2,}@[a-z.]{5,}\s+#';

    //$htmlTargetSite = 'HelloWWWWW sacha@gmail.com';
    preg_match_all($mailRegex, $htmlTargetSite,$result);
    if($result[0] != null) {
      return $result[0];
    }
    else{
      return '';
    }
}

function containPhoneInUrl($url) {

  $htmlTargetSite = getURL($url);

  if (preg_match_all('/((\+)33|0|0033)[1-9](\d{2}){4}/igm', $htmlTargetSite, $result)) {
      echo $result[0];
  } else {
      echo '';
  }
}

function is_siren($siren)
{
	if (strlen($siren) != 9) return 1; // le SIREN doit contenir 9 caractères
	if (!is_numeric($siren)) return 2; // le SIREN ne doit contenir que des chiffres

	// on prend chaque chiffre un par un
	// si son index (position dans la chaîne en commence à 0 au premier caractère) est impair
	// on double sa valeur et si cette dernière est supérieure à 9, on lui retranche 9
	// on ajoute cette valeur à la somme totale

	for ($index = 0; $index < 9; $index ++)
	{
		$number = (int) $siren[$index];
		if (($index % 2) != 0) { if (($number *= 2) > 9) $number -= 9; }
		$sum = $number;
	}

	// le numéro est valide si la somme des chiffres est multiple de 10
	if (($sum % 10) != 0) return 3; else return 0;		
}

// fonction permettant de contrôler la validité d'un numéro SIRET
function is_siret($siret)
{
	if (strlen($siret) != 14) return 1; // le SIRET doit contenir 14 caractères
	if (!is_numeric($siret)) return 2; // le SIRET ne doit contenir que des chiffres

	// on prend chaque chiffre un par un
	// si son index (position dans la chaîne en commence à 0 au premier caractère) est pair
	// on double sa valeur et si cette dernière est supérieure à 9, on lui retranche 9
	// on ajoute cette valeur à la somme totale

	for ($index = 0; $index < 14; $index ++)
	{
		$number = (int) $siret[$index];
		if (($index % 2) == 0) { if (($number *= 2) > 9) $number -= 9; }
		$sum = $number;
	}

	// le numéro est valide si la somme des chiffres est multiple de 10
	if (($sum % 10) != 0) return 3; else return 0;		
}

?>
