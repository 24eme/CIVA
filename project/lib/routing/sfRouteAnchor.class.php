<?php

class sfRouteAnchor extends sfRoute
{
  public function generate($params, $context = array(), $absolute = false) {
        $sf_anchor = null;
        if (array_key_exists('sf_anchor', $params)) {
            $sf_anchor = $params['sf_anchor'];
            unset($params['sf_anchor']);
        }

        $url = parent::generate($params, $context, $absolute);

        if (!is_null($sf_anchor)) {
            $url .= $sf_anchor;
        }
        return $url;
  }
}