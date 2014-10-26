<?php
namespace Concrete\Package\PageSelectorAttribute;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\Type as AttributeType;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'page_selector_attribute';
	protected $appVersionRequired = '5.7.1';
	protected $pkgVersion = '2.0';
	
	public function getPackageDescription() {
		return t("Attribute that allows the selection of pages.");
	}
	
	public function getPackageName() {
		return t("Page Selector Attribute");
	}
	
	public function install() {
		parent::install();
		$pkgh = \Package::getByHandle('page_selector_attribute');
		\Loader::model('attribute/categories/collection');
		$col = AttributeKeyCategory::getByHandle('collection');
		AttributeType::add('page_selector', t('Page Selector'), $pkgh);
		$col->associateAttributeKeyType(AttributeType::getByHandle('page_selector'));
	}
}