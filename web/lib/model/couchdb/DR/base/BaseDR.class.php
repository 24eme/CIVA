<?php

class BaseDR extends sfCouchdbDocument {
    public static function getRootDefinition() {
        return array(
            'definition' => array(
                'fields' => array(
                    '_id' => array(),
                    '_rev' => array(),
                    'acheteurs' => array(
                        'type' => 'collection',
                        'class' => 'DRAcheteurs',
                        'definition' => array(
                            'fields' => array(
                                '*' => array(
                                    'type' => 'array_collection',
                                        'definition' => array(
                                            'fields' => array(
                                                '*' => array('type' => 'string')
                                            )
                                        )
                                    )

                            )
                        )
                    ),
                    'campagne' => array(),
                    'cvi' => array(),
                    'VT' => array(
                        'type' => 'collection',
                        'definition' => array(
                            'fields' => array(
                                'surface' => array('type' => 'float'),
                                'volume' => array('type' => 'float'),
                            )
                     )
                     ),
                    'lies' => array('type' => 'float'),
                    'recolte' => array(
                        'type' => 'collection',
                        'class' => 'DRRecolte',
                        'definition' => array(
                            'fields' => array(
                                '*' => array(
                                    'type' => 'collection',
                                    'definition' => array(
                                        'fields' => array(
                                            'lieu' => array(
                                                'type' => 'collection',
                                                'class' => 'DRRecolteAppellation',
                                                'definition' => array(
                                                    'fields' => array(
                                                        '*' => array(
                                                            'type' => 'collection',
                                                            'class' => 'DRRecolteAppellationCepage',
                                                            'definition' => array(
                                                                'fields' => array(
                                                                    'detail' => array(
                                                                        'type' => 'array_collection',
                                                                        'definition' => array(
                                                                            'fields' => array(
                                                                                '*' => array(
                                                                                    'type' => 'collection',
                                                                                    'class' => 'DRRecolteAppellationCepageDetail',
                                                                                    'definition' => array(
                                                                                        'fields' => array(
                                                                                            'appellation' => array(),
                                                                                            'cepage' => array(),
                                                                                            'denomination' => array(),
                                                                                            'vtsgn' => array(),
                                                                                            'code_lieu' => array(),
                                                                                            'surface' => array('type' => 'float'),
                                                                                            'volume' => array('type' => 'float'),
                                                                                            'acheteurs' => array(
                                                                                                'type' => 'array_collection',
                                                                                                'definition' => array(
                                                                                                    'fields' => array(
                                                                                                        '*' => array(
                                                                                                            'type' => 'collection',
                                                                                                            'definition' => array(
                                                                                                                'fields' => array(
                                                                                                                    'cvi' => array(),
                                                                                                                    'quantite_vendue' => array('type' => 'float')
                                                                                                                )
                                                                                                            )
                                                                                                        )
                                                                                                    )
                                                                                                )
                                                                                            ),
                                                                                            'cooperatives' => array(
                                                                                                'type' => 'array_collection',
                                                                                                'definition' => array(
                                                                                                    'fields' => array(
                                                                                                        '*' => array(
                                                                                                            'type' => 'collection',
                                                                                                            'definition' => array(
                                                                                                                'fields' => array(
                                                                                                                    'cvi' => array(),
                                                                                                                    'quantite_vendue' => array('type' => 'float')
                                                                                                                )
                                                                                                            )
                                                                                                        )
                                                                                                    )
                                                                                                )
                                                                                            ),
                                                                                            'cave_particuliere' => array('type' => 'float'),
                                                                                            'volume_revendique' => array('type' => 'float'),
                                                                                            'volume_dplc' => array('type' => 'float'),
                                                                                        )
                                                                                    )
                                                                                )
                                                                            )
                                                                         )
                                                                    )
                                                                 )
                                                             )
                                                        ),
                                                        'rebeche' => array(
                                                            'type' => 'array_collection',
                                                            'class' => 'DRRecolteAppellationRebeche',
                                                            'definition' => array(
                                                                'fields' => array(
                                                                    '*' => array(
                                                                        'type' => 'collection',
                                                                        'class' => 'DRRecolteAppellationRebecheDetail',
                                                                        'definition' => array(
                                                                            'fields' => array(
                                                                                'appellation' => array(),
                                                                                'volume' => array('type' => 'float'),
                                                                                'cooperatives' => array(
                                                                                    'type' => 'array_collection',
                                                                                    'definition' => array(
                                                                                        'fields' => array(
                                                                                            '*' => array(
                                                                                                'type' => 'collection',
                                                                                                'definition' => array(
                                                                                                    'fields' => array(
                                                                                                        'cvi' => array(),
                                                                                                        'quantite_vendue' => array('type' => 'float')
                                                                                                    )
                                                                                                )
                                                                                            )
                                                                                        )
                                                                                    )
                                                                                ),
                                                                                'cave_particuliere' => array('type' => 'float')
                                                                            )
                                                                         )
                                                                     )
                                                                 )
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
    }

    public function setupDefinition() {
        $this->_definition = sfCouchdbJsonDefinitionParser::parse(self::getRootDefinition());
    }
}
