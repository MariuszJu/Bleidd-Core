<?php

namespace Bleidd\Form;

use Bleidd\Facade\Event;
use Bleidd\Util\ArrayUtil;
use Bleidd\Util\Inflector;
use Bleidd\Form\Renderer\FormRenderer;
use Bleidd\Form\Traits\AttributesTrait;
use Bleidd\Form\Element\AbstractElement;
use Bleidd\Form\Events\BeforeFormRender;
use Bleidd\Form\Traits\FormElementsTrait;
use Bleidd\Form\Renderer\VerticalFormRenderer;

class FormBuilder
{

    use FormElementsTrait, AttributesTrait;

    /** @var array */
    protected $options = [];

    /** @var FormRenderer */
    protected $renderer;

    /** @var Fieldset[] */
    protected $fieldsets;

    /** @var array|object */
    protected $data;

    /** @var string */
    protected $hydrator;

    /** @var object */
    protected $object;

    const CLASS_METHODS_HYDRATOR = 'class_methods';
    const CLASS_METHODS_CAMELCASE_HYDRATOR = 'class_methods_camel_case';
    const CLASS_PROPERTIES_HYDRATOR = 'class_properties';
    const CLASS_PROPERTIES_CAMELCASE_HYDRATOR = 'class_properties_camel_case';

    /**
     * FormBuilder constructor
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->setAttribute('name', $name);
        $this->fieldsets = [];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @param string $hydrator
     * @return $this
     */
    public function setHydrator(string $hydrator): self
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * @return string
     */
    public function getHydrator(): string
    {
        if (empty($this->hydrator)) {
            $this->hydrator = self::ARRAY_HYDRATOR;
        }

        return $this->hydrator;
    }

    /**
     * @param $object
     * @return $this
     */
    public function bind($object): self
    {
        $this->object = $object;
        $this->recursivelySetDataForm($this, $object);

        return $this;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data = []): self
    {
        foreach ($data as $key => $value) {
            switch ($this->getHydrator()) {
                case self::CLASS_METHODS_HYDRATOR:
                    if (is_callable($callback = [$this->object, sprintf('set_%s', $key)])) {
                        call_user_func_array($callback, [$value]);
                    }
                    break;

                case self::CLASS_METHODS_CAMELCASE_HYDRATOR:
                    if (is_callable($callback = [$this->object, sprintf('set%s', Inflector::toCamelCase($key, true))])) {
                        call_user_func_array($callback, [$value]);
                    }
                    break;

                case self::CLASS_PROPERTIES_HYDRATOR:
                    if (property_exists($this->object, $key) || $this->object instanceof \stdClass) {
                        $this->object->$key = $value;
                    }
                    break;

                case self::CLASS_PROPERTIES_CAMELCASE_HYDRATOR:
                    if (property_exists($this->object, $property = Inflector::toCamelCase($key))) {
                        $this->object->$property = $value;
                    }
                    break;
            }
        }

        return $this;
    }

    /**
     * @param FormRenderer $renderer
     * @return $this
     */
    public function setRenderer(FormRenderer $renderer): self
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * @return FormRenderer
     */
    public function getRenderer(): FormRenderer
    {
        if (empty($this->renderer)) {
            $this->renderer = new VerticalFormRenderer();
        }

        return $this->renderer;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * @throws \Exception
     * @param $elementOrFieldset
     * @return FormBuilder
     */
    public function add($elementOrFieldset): self
    {
        if ($elementOrFieldset instanceof AbstractElement) {
            $this->addElement($elementOrFieldset);
        } else if ($elementOrFieldset instanceof Fieldset) {
            $this->addFieldset($elementOrFieldset);
        } else {
            throw new \Exception('Given element is not Element nor an Fieldset');
        }
        
        return $this;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function setAction(string $action): self
    {
        return $this->setAttribute('action', $action);
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): self
    {
        return $this->setAttribute('method', $method);
    }

    /**
     * @return Fieldset[]
     */
    public function getFieldsets(): array
    {
        return $this->fieldsets;
    }

    /**
     * @param Fielset $fieldset
     * @return $this
     */
    protected function addFieldset(Fielset $fieldset): self
    {
        $this->fieldsets[] = $fieldset;
        return $this;
    }

    /**
     * @return string
     */
    protected function render(): string
    {
        Event::fire(new BeforeFormRender($this));

        return $this->getRenderer()->render($this);
    }

    /**
     * @param string $name
     * @return Fieldset|null
     */
    protected function getFieldsetByName(string $name): ?Fieldset
    {
        foreach ($this->getFieldsets() as $fieldset) {
            if ($fieldset->getName() == $name) {
                return $fieldset;
            }
        }

        return null;
    }

    /**
     * @param FormBuilder|Fieldset $formOrFieldset
     * @param object               $object
     */
    protected function recursivelySetDataForm($formOrFieldset, $object)
    {
        if (!$formOrFieldset instanceof FormBuilder && !$formOrFieldset instanceof Fieldset) {
            return;
        }

        $elements = $formOrFieldset instanceof FormBuilder
            ? array_merge($formOrFieldset->getElements(), $formOrFieldset->getFieldsets())
            : $formOrFieldset->getElements();

        foreach ($elements as $element) {
            if ($element instanceof Fieldset) {
                $this->recursivelySetDataForm($element, $object);
                continue;
            }

            $elementName = $element->getName();

            switch ($this->getHydrator()) {
                case self::CLASS_METHODS_HYDRATOR:
                    if (is_callable($callback = [$this->object, sprintf('get_%s', $elementName)])) {
                        $value = call_user_func($callback);
                    }
                    break;

                case self::CLASS_METHODS_CAMELCASE_HYDRATOR:
                    if (is_callable($callback = [$this->object, sprintf('get%s', Inflector::toCamelCase($elementName, true))])) {
                        $value = call_user_func($callback);
                    }
                    break;

                case self::CLASS_PROPERTIES_HYDRATOR:
                    if (property_exists($this->object, $elementName)) {
                        $value = $this->object->$elementName;
                    }
                    break;

                case self::CLASS_PROPERTIES_CAMELCASE_HYDRATOR:
                    if (property_exists($this->object, $property = Inflector::toCamelCase($elementName))) {
                        $value = $this->object->$property;
                    }
                    break;
            }

            $element->setValue($value ?? null);

            if ($element instanceof Select) {
                foreach ($element->getValueOptions() as $valueOption) {
                    if ($valueOption->getValue() == (isset($value) ? $value : null)) {
                        $valueOption->isSelected(true);
                    }
                }
            }
        }

//        if (is_array($data)) {
//            foreach ($data as $key => $value) {
//                if (is_array($value)) {
//                    $this->recursivelySetDataForm($formOrFieldset->getFieldsetByName($key), $value);
//                } else {
//                    if ($element = $formOrFieldset->getElementByName($key)) {
//                        $element->setValue($value);
//                    }
//                }
//            }
//        }
    }

}
