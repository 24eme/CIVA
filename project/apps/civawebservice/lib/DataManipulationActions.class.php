<?php

class DataManipulationActions extends sfActions
{
    protected function renderData($datas) {
        if ($this->getRequest()->getRequestFormat() == "json") {
            return $this->renderJson($datas);
        } elseif ($this->getRequest()->getRequestFormat() == "xml") {
            return $this->renderXml($datas);
        } elseif ($this->getRequest()->getRequestFormat() == "debug") {
            return $this->renderHtml($datas);
        }

        return sfView::SUCCESS;
    }

    protected function renderJson($datas) {
        return $this->renderText(json_encode($datas));
    }

    protected function renderXml($datas) {
        return $this->renderText($this->xml_encode($datas));
    }

    protected function renderHtml($datas) {
        echo "<pre>";
        print_r($datas);
        echo "</pre>";
        return sfView::NONE;
    }

    protected function xml_encode($datas) {
        $xml = new XmlConstruct('root');
        $xml->fromArray($datas);
        return $xml->getDocument();
    }

    protected function keepData($item, $keep_data) {
       if ($keep_data && is_array($keep_data) && is_array($item)) {
            $keys_item = array_keys($item);
            foreach($keys_item as $key_item) {
                if (!in_array($key_item, $keep_data)) {
                    unset($item[$key_item]);
                }
            }
      }
      return $item;
    }

    protected function buildItemDeclarations($item) {
        $new_item = $this->keepData($item, array('_id', '_rev', 'cvi', 'campagne'));
        if (isset($item['validee']) && $item['validee']) {
            $new_item['validee'] = '1';
        } else {
            $new_item['validee'] = '0';
        }
        sfContext::switchTo('civa');
        $new_item['url_pdf'] = sfContext::getInstance()->getRouting()->generate('print', array('annee' => $item['campagne']), true);
        sfContext::switchTo('civawebservice');
        return $new_item;
    }
    
    protected function buildDeclarationsData($items, $build_item = true,  $root = "declarations") {
      $datas = array($root => array());
      foreach($items as $item) {
        if ($build_item) {
            $datas[$root][$item['_id']] = $this->buildItemDeclarations($item);
        } else {
            $datas[$root][$item['_id']] = $item;
        }
      }
      return $datas;
    }

   protected function buildTiersData($items, $root = "tiers") {
      $datas = array($root => array());
      foreach($items as $item) {
        unset($item['db2']);
        $datas[$root][$item['_id']] = $item;
      }
      return $datas;
   }

   
   protected function buildCompteData($items, $root = "compte") {
      $datas = array($root => array());
      foreach($items as $item) {
        unset($item['mot_de_passe']);
        unset($item['db2']);
        $datas[$root][$item['_id']] = $item;
      }
      return $datas;
   }

}