<?php
namespace Concrete\Package\PageSelectorAttribute\Attribute\PageSelector;

use Loader;
defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Attribute\Controller  {

    protected $searchIndexFieldDefinition = array('type' => 'integer', 'options' => array('default' => 0, 'notnull' => false));

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atPageSelector where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}
	
	public function searchForm($list) {
		$PagecID = $this->request('value');
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $PagecID, '=');
		return $list;
	}
	
	public function search() {
		$form_selector = Loader::helper('form/page_selector');
		print $form_selector->selectPage($this->field('value'), $this->request('value'), false);
	}
	
	public function form() {
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		$form_selector = Loader::helper('form/page_selector');
		print $form_selector->selectPage($this->field('value'), $value);
	}
	
	public function validateForm($p) {
		return $p['value'] != 0;
	}

	public function saveValue($value) {
		$db = Loader::db();
        if(!intval($value)) {
            $value = 0;
        }
		$db->Replace('atPageSelector', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atPageSelector where avID = ?', array($id));
		}
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atPageSelector where avID = ?', array($this->getAttributeValueID()));
	}
	
}