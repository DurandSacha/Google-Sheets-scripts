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
 
		return curl_exec($ch);

		if (curl_errno($ch)) {
			echo curl_error($ch);
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == intval(200)) {
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
		 return 'Present'; 
	}
	else{
		  return 'Absent';
	}
	return;
}

function containEmailInUrl($url) {

    $htmlTargetSite = getURL($url);
    $mailRegex = '/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[fr|com|info|gouv]/';

    preg_match($mailRegex, $htmlTargetSite,$result);
    if($result != null && strpos($result[0], 'exemple') == false && strpos($result[0], 'example') == false) {
			if(is_array($result) == true){
				return $result[0];
			}
			else{
				return $result;
			}
    } else { return null;}
}

function containEmailInWebsite($url) {

	 $context = stream_context_create(array(
	'http' => array('ignore_errors' => true),
  ));

	//scrap an email
    $mail = containEmailInUrl($url);
    if ($mail != '') {
      return $mail;
    }
    // search other pages
    else{
      $html = file_get_html($url, false, $context);
			if(!empty($html) && $html->find('a') != null){
				foreach ($html->find('a') as $link) {
					$contactRegex = '#contact|contactez-nous|about|Contact#';
					preg_match($contactRegex, $link->href,$result);
					if($result != null){
						$website = $url . '/'. $link->href ;
						$mail = containEmailInUrl($website);
						return $mail;
					}
					else{
						return null;
					}
				}
			}
    }
}

function containPhoneInUrl($url) {

  $htmlTargetSite = getURL($url);

  preg_match('#^0[1-68]([-. ]?[0-9]{2}){4}$#', $htmlTargetSite, $result);
	preg_match('#((\+)33|0|0033)[1-9](\d{2}){4}#im', $htmlTargetSite, $result2);
	
	if($result != null){
			return $result;
	}
	elseif($result2 != null){
			return $result;
	}
	else{
			return 'null';
	}
}

function is_phone($num) {

  if (preg_match_all('/((\+)33|0|0033)[1-9](\d{2}){4}/im', $num, $result)) {
      return true; 
	} else { return false; }
}

function is_siren($siren)
{
	if (strlen($siren) != 9) return 1; // le SIREN doit contenir 9 caractères
	if (!is_numeric($siren)) return 2; // le SIREN ne doit contenir que des chiffres

	for ($index = 0; $index < 9; $index ++)
	{
		$number = (int) $siren[$index];
		if (($index % 2) != 0) { if (($number *= 2) > 9) $number -= 9; }
		$sum = $number;
	}
	if (($sum % 10) != 0) return 3; else return 0;		
}

function is_siret($siret)
{
	if (strlen($siret) != 14) return 1; // le SIRET doit contenir 14 caractères
	if (!is_numeric($siret)) return 2; // le SIRET ne doit contenir que des chiffres

	for ($index = 0; $index < 14; $index ++)
	{
		$number = (int) $siret[$index];
		if (($index % 2) == 0) { if (($number *= 2) > 9) $number -= 9; }
		$sum = $number;
	}

	// le numéro est valide si la somme des chiffres est multiple de 10
	if (($sum % 10) != 0) return 3; else return 0;		
}

function checkEmail($email) {
	
	list($nom, $dom) = explode("@", $email);

	if(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
		return false;
	}

	if( strlen($email > 18) || strlen($dom > 13) || strlen($nom > 20) || substr_count($dom, '.') > 2){
		return false;
	}

	if(preg_match('#exemple|example|referencement|spam#',$email,$result)) {
		return false;
	}
	
	if (gethostbyname($dom) == $dom) {
		return false;
	}

	if (!checkdnsrr($email) == false){
		return false;
	}
	
	//check with API
	/*
	if(checkEmailWithApi($email) == false){
		echo 'false';
		return false;
	}
	*/
	return true;
}

function AdaptProtocolToUrl($url) {

	// replace url with https prefix
	$url = str_replace(['http://'] , 'https://', $url);
	if(strpos($url, 'https://') === false){
		$url = 'https://'.$url;
	}

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	curl_setopt($ch, CURLINFO_SSL_VERIFYRESULT, true);
	curl_exec($ch);

	$info = curl_getinfo($ch);

	// get information ( Certificats SSL is accept ? )
	if(strpos(json_encode($info), 'Accept') != false){
		$protocol = 'https';
	}
	else{
		$protocol = 'http';
	}

	$simpleDomain = str_replace(['https://', 'http://'] , '', $url);
	$url = strtolower($protocol) . '://' . $simpleDomain;

	curl_close($ch);

	return $url;

}

?>
