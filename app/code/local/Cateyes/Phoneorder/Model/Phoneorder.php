<?php

class Cateyes_Phoneorder_Model_Phoneorder extends Mage_Core_Model_Abstract
{

	private static $allowedFields = array(
		"url",
		"phone",
		"comment",
		"status",
		"date",
	);

	public function _construct()
    {
        parent::_construct();
        $this->_init('phoneorder/phoneorder');
    }    


    /**
     * Metoda zapisuje dane do bazy
     * @param array $data tablica pól formularza
     * @return boolena	zwraca true lub false 
     */
	public function saveRequest($data)
	{
		if(empty($data))
		{
			return false;
		}

		$_d = array();
		foreach($data as $k => $v)
		{
			if(in_array($k, self::$allowedFields) == true)
			{
				$_d[$k] = $v;	
			}			
		}

		Mage::getModel('phoneorder/phoneorder')->setData($_d)
			->save();

		return true;
	}

}

