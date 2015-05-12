<?php
/**
 * Model for VracCertification
 *
 */

class VracCertification extends BaseVracCertification 
{
    
    public function getChildrenNode() 
    {
        return $this->getGenres();
    }

    public function getGenres()
    {
        return $this->filter('^genre');
    } 

}