<?php

/**
 * home actions.
 *
 * @package    civa
 * @subpackage home
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class homeActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        $detail = new DRRecolteAppellationCepageDetail();

        $doc = new DR();
        $doc->load(sfCouchdbManager::getClient()->getDoc('DR-6701800180-2009'));

        $doc2 = new DR();
        $doc2->load(sfCouchdbManager::getClient()->getDoc('DR-6701800180-2009'));
        echo $this->sizeofvar($doc);

        echo $doc->get('recolte/appellation_1/lieu/cepage_SY/detail/0/surface');
    }

    // convertion d'un nombre d'octet en kB, MB, GB
    private function convert_SIZE($size)
    {
        $unite = array('B','kB','MB','GB');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unite[$i];
    }

    // liste toutes les variables active et leur taille mémoire
    private function aff_variables()
    {
       echo '<br/>';
       global $datas ;
       foreach($GLOBALS as $Key => $Val)
       {
          if ($Key != 'GLOBALS')
          {
             echo' <br/>'. $Key .' &asymp; '.$this->sizeofvar( $Val );
          }
       }
        echo' <br/>';
    }


    //affiche l'empreinte mémoire  d'une variable
    private function sizeofvar($var)
    {

      $start_memory = memory_get_usage();
      $temp =unserialize(serialize($var ));
      $taille = memory_get_usage() - $start_memory;
      return $this->convert_SIZE($taille) ;
    }

    //affiche des info sur l'espace mémoire du script PHP
    private function memory_stat()
    {
       echo  'Mémoire -- Utilisé : '. $this->convert_SIZE(memory_get_usage(false)) .
       ' || Alloué : '.
       $this->convert_SIZE(memory_get_usage(true)) .
       ' || MAX Utilisé  : '.
       $this->convert_SIZE(memory_get_peak_usage(false)).
       ' || MAX Alloué  : '.
       $this->convert_SIZE(memory_get_peak_usage(true)).
       ' || MAX autorisé : '.
       ini_get('memory_limit') ;  ;
    }

}
