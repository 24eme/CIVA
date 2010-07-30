<?php
class Configuration extends BaseConfiguration {
      public function getArrayAppellationsMout() {
          $appellations = $this->getRecolte();
          $appellations_array_mouts = array();
          foreach($appellations as $appellation_key => $appellation) {
              if ($appellation->getMout() == 1) {
                $appellations_array_mouts[$appellation_key] = $appellation;
              }
          }
          return $appellations_array_mouts;
      }

      public function getArrayAppellations() {
          $appellations = $this->getRecolte();
          $appellations_array = array();
          foreach($appellations as $appellation_key => $appellation) {
              $appellations_array[$appellation_key] = $appellation;
          }

          return $appellations_array;
      }
}