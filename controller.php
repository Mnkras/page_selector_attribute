<?php
namespace Concrete\Package\PageSelectorAttribute;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\TypeFactory;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Package\Package
{
    protected $pkgHandle = 'page_selector_attribute';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '2.1';

    public function getPackageDescription()
    {
        return t("Attribute that allows the selection of pages.");
    }

    public function getPackageName()
    {
        return t("Page Selector Attribute");
    }

    public function install()
    {
        $pkg = parent::install();
        /** @var TypeFactory $factory */
        $factory = $this->app->make(TypeFactory::class);
        $type = $factory->getByHandle('page_selector');
        if (!is_object($type)) {
            $type = $factory->add('page_selector', t('Page Selector'), $pkg);
            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            $col = $service->getByHandle('collection')->getController();
            $col->associateAttributeKeyType($type);
        }
    }
}
