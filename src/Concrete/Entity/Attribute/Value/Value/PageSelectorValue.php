<?php
namespace Concrete\Package\PageSelectorAttribute\Entity\Attribute\Value\Value;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Page\Page;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atPageSelector")
 */
class PageSelectorValue extends AbstractValue
{
    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    protected $value;

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Page
     */
    public function getPageObject()
    {
        return Page::getByID($this->getValue());
    }

    /**
     * @param integer $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param Page $page
     */
    public function setPageObject(Page $page)
    {
        $this->setValue($page->getCollectionID());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}