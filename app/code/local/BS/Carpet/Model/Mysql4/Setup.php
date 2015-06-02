<?php

class BS_Carpet_Model_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
	public function getDefaultEntities()
    {
    	return array(
            'catalog_product' => array(
                'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes'        => array(
                    'is_carpet_product' => array(
    	                'group'             => 'General',
                        'type'              => 'int',
                        'backend'           => 'carpet/entity_attribute_backend_boolean_config',
                        'frontend'          => '',
                        'label'             => 'Is this Carpet product?',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'carpet/entity_attribute_source_boolean_config',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => false,
                        'default'           => '0',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'visible_in_advanced_search' => false,
                        'used_in_product_listing' => false,
                        'used_for_sort_by'  => false,
                        'unique'            => false,
                    ),
                    'max_carpet_length' => array(
                        'group'             => 'General',
                        'type'              => 'decimal',
                        'backend'           => 'carpet/entity_attribute_backend_carpet_length',
                        'frontend'          => '',
                        'label'             => 'Max Carpet Length (default: unlimited)',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => false,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'visible_in_advanced_search' => false,
                        'used_in_product_listing' => false,
                        'used_for_sort_by'  => false,
                        'unique'            => false,
                    )
                )
            )
        );
    }
}