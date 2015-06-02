<?php

class Sag_Gallery_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        /*
        $read= Mage::getSingleton('core/resource')->getConnection('core_read'); 
        $value=$read->query("select * from core_url_rewrite where id_path like 'gallery_category_%'"); 
        while ($row = $value->fetch()){ 
            echo "<pre>";
            print_r($row); 
            echo "</pre>";
        }
        */
        //$write = Mage::getSingleton('core/resource')->getConnection('core_write');
        //$write->query("update core_url_rewrite set store_id ='21' where id_path like 'gallery_category_%'");
        $this->loadLayout();
        $g = $this->getRequest()->getParam('g');
        //$this->setData('browseBy', $g);
        
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle("Table throws | Gallery");
        $head->setDescription("Custom printed table throws / branded table throws have been used in the marketing industry for ages . These table throws are also widely used at marketing events , trade shows , retails stores , retail outsets , libraries , out door events , schools , universities and product launch events .");
        $head->setKeywords("marketing events gallery,trade shows gallery,retails stores gallery,retail outsets gallery,libraries gallery,out door events gallery,schools gallery,universities gallery,product launch events gallery");
        $this->renderLayout();
    }
	
	
	/*bulk make enteries to the url_rewrite table*/
	
	public function bulkEntriesAction($_store_id=''){
		
		$_store_id = $this->getRequest()->getParams('store_id');
		 Zend_Debug::dump($_store_id);		
		
		if(!$_store_id ){
			echo 'please pass some value like store_id/5 .';
			return false;
			}
		
			/*core read first from the table category*/
			$_connection_read = Mage::getSingleton('core/resource')->getConnection('core_read');
			$_connection_write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$_category_tbl = $_connection_read->getTableName('category');
			$_category_store_tbl = $_connection_read->getTableName('category_store');
			$_core_url_rewrite_tbl = $_connection_read->getTableName('core_url_rewrite');
			$_categories_sql        = "Select category_id,title,url,filename from ".$_category_tbl;
			$_categories_rows       = $_connection_read->fetchAll($_categories_sql); //fetchRow($_sql), fetchOne($_sql),...
			
			/*delete from core_url_rewrite*/
			$_url_rewrite = "DELETE from ".$_core_url_rewrite_tbl." Where `id_path` like('%gallery%') ";
			$_result = $_connection_write->query($_url_rewrite);
			
			// Zend_Debug::dump($_result);
			 echo '<------------------------------------------------------------>';
			
			/*check if there $_categories_rows are not empty*/
			// exit;
	 
	
	if(count($_categories_rows) >0){			
			
	foreach($_categories_rows as $_categories_row){				
				
	$_insert_core_url_sql = "INSERT INTO `".$_core_url_rewrite_tbl."` SET 
					store_id = ".$_store_id['store_id'].",										
					id_path='gallery_category_".$_categories_row['category_id']."',
					request_path='".$_categories_row['url'].".html',
					target_path='gallery/category/index/cat/".$_categories_row['category_id']."',
					is_system=0										
					";
										
				 echo '<br/>'.$_insert_core_url_sql;	
				$_result_set= $_connection_write->query($_insert_core_url_sql);	
				print($_result_set);				
				//exit;
				}
			}//end of if statement
			
			// Zend_Debug::dump($_categories_rows);
			
		}

}