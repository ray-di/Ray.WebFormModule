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
use Aura\Input\Builder;
use Aura\Input\BuilderInterface;
use Aura\Input\Form;

abstract class AbstractForm extends Form implements FormInterface
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
        BuilderInterface $builder = null,
        FilterFactory $filterFactory = null,
        HelperLocatorFactory $helperFactory = null
    ) {
        $this->builder  = $builder ?: new Builder;
        $this->filter = $filterFactory ? $filterFactory->newSubjectFilter() : (new FilterFactory)->newSubjectFilter();
        $this->helper = $helperFactory ? $helperFactory->newInstance() : (new HelperLocatorFactory)->newInstance();
    }

    public function __construct()
    {
    }

    /**
     * @param AntiCsrfInterface $antiCsrf
     */
    public function setCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->setAntiCsrf($antiCsrf);
    }

    /**
     * @\Ray\Di\Di\PostConstruct
     */
    public function postConstruct()
    {
        $this->init();
        if ($this->antiCsrf instanceof AntiCsrfInterface) {
            $this->setAntiCsrf($this->antiCsrf);
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
    public function error($input, $format = '%s', $layout = '%s')
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
     * @param array|object $subject The subject to be filtered.     * @param array $data
     *
     * @return bool
     */
    public function apply(array $data)
    {
        $isValid = $this->filter->apply($data);

        return $isValid;
    }

    /**
     * Gets the filter messages.
     *
     * @param string $name The input name to get the filter message for; if
     *                     empty, gets all messages for all inputs.
     *
     * @return array The filter messages.
     */
    public function getMessages($name = null)
    {
        $messages = $this->filter->getFailures()->getMessages();
        if ($name && isset($messages[$name])) {
            return $messages[$name];
        }

        return $messages;
    }

    public function __clone()
    {
        $this->filter = clone $this->filter;
    }
}
