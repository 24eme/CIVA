<?php

abstract class importAbstractTask extends sfBaseTask
{
    protected static $months_fr = array(
    "août" => "08",
    "avr" => "04",
    "déc" => "12",
    "févr" => "02",
    "janv" => "01",
    "juil" => "07",
    "juin" => "06",
    "mai" => "05",
    "mars" => "03",
    "nov" => "11",
    "oct" => "10",
    "sept" => "09",
    );

    protected function convertToFloat($number) {

        return round(str_replace(",", ".", $number) * 1, 2);
    }

    protected function convertToDateObject($date) {
        if (preg_match('/^([0-9]{2})-([a-zûé]+)-([0-9]{2})$/', $date, $matches)) {
      
            return new DateTime(sprintf('%d-%d-%d', $matches[3], self::$months_fr[$matches[2]], $matches[1]));
        }

        if (preg_match('/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/', $date, $matches)) {

            return new DateTime(sprintf('%d-%d-%d', $matches[3], $matches[2], $matches[1]));
        }
        
        if (preg_match('/^([1-3]?[0-9]+)([0-9]{2})([0-9]{4})$/', $date, $matches)) {

            return new DateTime(sprintf('%d-%d-%d', $matches[3], $matches[2], $matches[1]));
        }

        throw new sfException(sprintf("La date '%s' est invalide", $date));
    }

    protected function convertOuiNon($indicateur) {

        return (int) ($indicateur == 'O');
    }

    protected function getKey($key, $withDefault = false) 
    {
        if ($withDefault) {
            
            return ($key)? $key : Configuration::DEFAULT_KEY;
        } 
        if (!$key) {
            
            throw new Exception('La clé "'.$key.'" n\'est pas valide');
        }
        
        return $key;
    }

    protected function verifyVolume($value, $can_be_negatif = false) {
        $this->verifyFloat($value, $can_be_negatif);
    }

    protected function verifyFloat($value, $can_be_negatif = false) {
        if ($can_be_negatif && !(preg_match('/^[\-]{0,1}[0-9]+\.[0-9]+$/', $value))) {
            throw new sfException(sprintf("Nombre flottant '%s' invalide", $value));
        } elseif(!$can_be_negatif && !(preg_match('/^[0-9]+\.[0-9]+$/', $value))) {
            throw new sfException(sprintf("Nombre flottant '%s' invalide", $value));
        }

        $value = $this->convertToFloat($value);

        if(!$can_be_negatif && $value < 0) {
          throw new sfException(sprintf("Nombre flottant '%s' négatif", $value));
        }
    }

    public function logLignes($type, $message, $lines, $num_ligne = null) {
        echo sprintf("%s;%s (de la ligne %s à %s) :", $type, $message, $num_ligne-count($lines), $num_ligne);
        foreach($lines as $i => $line) {
          echo sprintf(" - %s : %s", $i, implode($line, ";"));
        }

        echo "\n";
    }

    public function logLigne($type, $message, $line, $num_ligne = null, $separator = ";") {
        $this->log(sprintf("%s;%s (ligne %s) : %s", $type, $message, $num_ligne, implode($line, $separator)));
    }
    
    protected function getCouleur($couleur_key) {
        switch ($couleur_key) {
            case 'BL':
                return 'Blanc';
            case 'RS':
                return 'Rose';
            case 'RG':
                return 'Rouge';
            default:
                throw new sfException("La couleur $couleur_key n'est pas connue dans la configuration.");
        }
        return null;
    }
    
    public function green($string) {
        return "\033[32m".$string."\033[0m";
    }
        
    public function yellow($string) {
        return "\033[33m".$string."\033[0m";
    }
    
    
}