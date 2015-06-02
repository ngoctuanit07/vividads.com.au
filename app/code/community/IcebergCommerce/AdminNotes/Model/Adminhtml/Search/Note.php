<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_AdminNotes
 * @copyright  Copyright (c) 2011 Iceberg Commerce
 */

class IcebergCommerce_AdminNotes_Model_Adminhtml_Search_Note extends Varien_Object
{
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('adminnotes/note_collection')
            ->addNoteSearchFilter($this->getQuery())
            ->setPage(1, 10)
            ->load();


        foreach ($collection->getItems() as $note) {
            $arr[] = array(
                'id'            => 'adminnote/1/'.$note->getNoteId(),
                'type'          => 'Page Note',
                'name'          => $note->getTitle(),
                'description'   => strlen($note->getNote()) > 100 ? substr($note->getNote(),0,100).'...' : $note->getNote(),
                'url'           => Mage::helper('adminhtml')->getUrl($note->getPath()),//$note->getPath(),
            );
        }

        $this->setResults($arr);

        return $this;
    }
}
