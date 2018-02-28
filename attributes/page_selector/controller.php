<?php
namespace Concrete\Package\PageSelectorAttribute\Attribute\PageSelector;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspectorInterface;
use Concrete\Core\Error\ErrorList\Error\Error;
use Concrete\Core\Error\ErrorList\Error\FieldNotPresentError;
use Concrete\Core\Error\ErrorList\Field\AttributeField;
use Concrete\Core\Support\Facade\Application;
use Concrete\Package\PageSelectorAttribute\Entity\Attribute\Value\Value\PageSelectorValue;
use Page;
use Permissions;
use Concrete\Core\Attribute\Controller as AttributeTypeController;

class Controller extends AttributeTypeController
{
    protected $searchIndexFieldDefinition = ['type' => 'integer', 'options' => ['default' => 0, 'notnull' => false]];

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('file');
    }

    public function searchForm($list)
    {
        $PagecID = $this->request('value');
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $PagecID, '=');

        return $list;
    }

    public function search()
    {
        $form_selector = Application::getFacadeApplication()->make('helper/form/page_selector');
        echo $form_selector->selectPage($this->field('value'), $this->request('value'), false);
    }

    public function form()
    {
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        }
        $form_selector = Application::getFacadeApplication()->make('helper/form/page_selector');
        echo $form_selector->selectPage($this->field('value'), (isset($value)) ? $value : null);
    }

    public function validateForm($p)
    {
        if (intval($p['value']) > 0) {
            $c = Page::getByID(intval($p['value']));
            if (is_object($c) && !$c->isError()) {
                return true;
            } else {
                return new Error(t('You must specify a valid page for %s', $this->getAttributeKey()->getAttributeKeyDisplayName()),
                    new AttributeField($this->getAttributeKey())
                );
            }
        } else {
            return new FieldNotPresentError(new AttributeField($this->getAttributeKey()));
        }
    }

    public function getDisplayValue()
    {
        $at_val = $this->getValue();
        $html = '';
        $at_page = Page::getByID($at_val);
        if (is_object($at_page) && !$at_page->isInTrash()) {
            $cpc = new Permissions($at_page);
            if ($cpc->canViewPage()) {
                $url = $at_page->getCollectionPath();
                $name = h($at_page->getCollectionName());
                $html = '<a href="' . $url . '" title="' . $name . '">' . $name . '</a>';
            } else {
                $html = '<a href="" class="page_denied">' . t('access denied') . '</a>';
            }
        }

        return $html;
    }

    public function exportValue(\SimpleXMLElement $akv)
    {
        $av = $akv->addChild('value');
        $cID = $this->getAttributeValue()->getValue();
        /** @var Number $nh */
        $nh = Application::getFacadeApplication()->make('helper/number');
        if ($nh->isInteger($cID)) {
            $av->addChild('cID', ContentExporter::replacePageWithPlaceHolder($cID));
        } else {
            $av->addChild('cID', 0);
        }
    }

    /**
     * @param \SimpleXMLElement $akv
     *
     * @return bool|string|void
     */
    public function importValue(\SimpleXMLElement $akv)
    {
        if (isset($akv->value->cID)) {
            $cIDVal = (string) $akv->value->cID;
            /** @var ValueInspectorInterface $inspector */
            $inspector = Application::getFacadeApplication()->make('import/value_inspector');
            $result = $inspector->inspect($cIDVal);
            $cID = $result->getReplacedValue();
            if ($cID) {
                return $this->createAttributeValue($cID);
            }
        }
    }

    public function createAttributeValue($mixed)
    {
        if (is_object($mixed) && method_exists($mixed, 'getCollectionID')) {
            $mixed = $mixed->getCollectionID();
        }

        $value = new PageSelectorValue();
        $value->setValue((int) $mixed);

        return $value;
    }

    public function createAttributeValueFromRequest()
    {
        $data = $this->post();
        if (intval($data['value']) > 0) {
            $c = Page::getByID(intval($data['value']));

            return $this->createAttributeValue($c);
        }

        return $this->createAttributeValue(0);
    }

    public function getAttributeValueClass()
    {
        return PageSelectorValue::class;
    }
}
