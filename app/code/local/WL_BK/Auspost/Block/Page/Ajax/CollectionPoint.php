<?php

/**
  * @category       WL
  * @package        Auspost
  * @class          WL_Auspost_Block_Page_Ajax_CollectionPoint
  * @description    Extended from the Mage_Core_Block_Template class, give methods to get collection points ajax request.
 */


class WL_Auspost_Block_Page_Ajax_CollectionPoint extends Mage_Core_Block_Template
{

/**
 *
 * Call API to get array list of collection points by postcode
 * 
 * @param    string $postcode The Postcode
 * @return   array associative array collection points
 * 
 */

    public function getCollectionPoints ($postcode = null)
    {
        $api = Mage::getModel('auspost/api');
        $collection_points = $api->getCustomerCollectionPoints($postcode);
        $_SESSION['auspost_collection_points'] = $collection_points;
        return $collection_points;
    }
    
/**
 *
 * Call API to get array list of collection points by id
 * 
 * @param    string $id The identifier of a collection point
 * @return   array associative array collection point information
 * 
 */

    public function getCollectionPointById ($id, $postcode = null)
    {
        $collection_points = $_SESSION['auspost_collection_points'];
        if (!empty($collection_points))
            foreach ($collection_points as $point)
                if ($point['DeliveryPointIdentifier'] == $id)
                    return $point;
        return null;
    }
}
