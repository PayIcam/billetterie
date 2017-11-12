<?php

namespace Shotgun;

class Securise
{
    // Données entrantes
    public static function bdd($string)
    {
        // On regarde si le type de string est un nombre entier (int)
        if(ctype_digit($string))
        {
            $string = intval($string);
        }
        // Pour tous les autres types
        else
        {
            $string = mysql_real_escape_string($string);
            $string = addcslashes($string, '%_');
        }
        return $string;


        }
     // Données sortantes
    public static function html($string)
    {
    	return htmlentities($string);
    }

	function nettoyer($chaine)
	{
		$caracteres=array('a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a',
			'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
			'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
			'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
			'Œ' => 'oe', 'œ' => 'oe',
			'$' => 's', '0'=>'0');
		$chaine = strtr($chaine, $caracteres);
		$chaine = preg_replace('#[^A-Za-z0-9.-_]+#', '-', $chaine);

		return $chaine;
	}
}
?>
