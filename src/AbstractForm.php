<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Filter\FilterFactory;
use Aura\Filter\SubjectFilter;
use Aura\Html\HelperLocator;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\AntiCsrfInterface;
use Aura\Input\BuilderInterface;
use Aura\Input\Fieldset;
use Ray\WebFormModule\Exception\CsrfViolationException;

abstract class AbstractForm extends Fieldset implements FormInterface
{
    /**
     * @var SubjectFilter
     */
    protected $filter;

    /**
     * @var null | array
     */
    protected $errorMessages;

    /**
     * @var HelperLocator
     */
    protected $helper;

    /**
     * @var AntiCsrfInterface
     */
    protected $antiCsrf;

    /**
     * @param BuilderInterface     $builder
     * @param FilterFactory        $filterFactory
     * @param HelperLocatorFactory $helperFactory
     *
     * @\Ray\Di\Di\Inject
     */
    public function setBaseDependencies(
        BuilderInterface $builder,
        FilterFactory $filterFactory,
        HelperLocatorFactory $helperFactory
    ) {
        $this->builder  = $builder;
        $this->filter = $filterFactory->newSubjectFilter();
        $this->helper = $helperFactory->newInstance();
    }

    public function __construct()
    {
    }

    public function setAntiCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->antiCsrf = $antiCsrf;
    }

    /**
     * @\Ray\Di\Di\PostConstruct
     */
    public function postConstruct()
    {
        $this->init();
        if ($this->antiCsrf instanceof AntiCsrfInterface) {
            $this->antiCsrf->setField($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function input($input)
    {
        return $this->helper->input($this->get($input));
    }

    /**
     * @inheritdoc
     */
    public function error($input)
    {
        if (! $this->errorMessages) {
            $failure = $this->filter->getFailures();
            if ($failure) {
                $this->errorMessages = $failure->getMessages();
            }
        }

        if (isset($this->errorMessages[$input])) {
            return $this->errorMessages[$input][0];
        }

        return '';
    }

    /**
     * @param array $attr Attributes for the form tag.
     *
     * @return string
     *
     * @throws \Aura\Html\Exception\HelperNotFound
     * @throws \Aura\Input\Exception\NoSuchInput
     */
    public function form($attr = [])
    {
        $form = $this->helper->form($attr);
        if (isset($this->inputs['__csrf_token'])) {
            $form .= $this->helper->input($this->get('__csrf_token'));
        }

        return $form;
    }

    /**
     * Applies the filter to a subject.
     *
     * @param array $data
     *
     * @return bool
     * @throws CsrfViolationException
     */
    public function apply(array $data)
    {
        if ($this->antiCsrf && ! $this->antiCsrf->isValid($data)) {
            throw new CsrfViolationException;
        }
        $this->fill($data);
        $isValid = $this->filter->apply($data);

        return $isValid;
    }

    /**
     * Returns all failure messages for all fields.
     *
     * @return array
     */
    public function getFailureMessages()
    {
        $messages = $this->filter->getFailures()->getMessages();

        return $messages;
    }

    /**
     * Returns all the fields collection
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->inputs);
    }

    public function __clone()
    {
        $this->filter = clone $this->filter;
        $this->init();
    }
}
