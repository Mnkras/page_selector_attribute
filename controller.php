<?php      

defined('C5_EXECUTE') or die(_("Access Denied."));

class PageSelectorAttributePackage extends Package {

	protected $pkgHandle = 'page_selector_attribute';
	protected $appVersionRequired = '5.4.0';
	protected $pkgVersion = '1.0';
	
	public function getPackageDescription() {
		return t("Attribute that allows the selection of pages.");
	}
	
	public function getPackageName() {
		return t("Page Selector Attribute");
	}
	
	public function install() {
		$pkg = parent::install();
		$pkgh = Package::getByHandle('page_selector_attribute'); 
		Loader::model('attribute/categories/collection');
		$col = AttributeKeyCategory::getByHandle('collection');
		$pageselector = AttributeType::add('page_selector', t('Page Selector'), $pkgh);
		$col->associateAttributeKeyType(AttributeType::getByHandle('page_selector'));
	}
}