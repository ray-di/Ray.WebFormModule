<?php

declare(strict_types=1);

namespace Ray\WebFormModule;

use ArrayIterator;
use Aura\Filter\FilterFactory;
use Aura\Filter\SubjectFilter;
use Aura\Html\Exception\HelperNotFound;
use Aura\Html\HelperLocator;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\AntiCsrfInterface;
use Aura\Input\BuilderInterface;
use Aura\Input\Exception\NoSuchInput;
use Aura\Input\Fieldset;
use Ray\Di\Di\Inject;
use Ray\Di\Di\PostConstruct;
use Ray\WebFormModule\Exception\CsrfViolationException;
use Ray\WebFormModule\Exception\LogicException;
use Throwable;

use function trigger_error;

use const E_USER_ERROR;
use const PHP_EOL;

abstract class AbstractForm extends Fieldset implements FormInterface
{
    /** @var SubjectFilter */
    protected $filter;

    /** @var array<string, array<string>>|null */
    protected $errorMessages;

    /** @var HelperLocator */
    protected $helper;

    /** @var AntiCsrfInterface */
    protected $antiCsrf;

    public function __construct()
    {
    }

    public function __clone()
    {
        $this->filter = clone $this->filter;
        $this->init();
    }

    /**
     * Return form markup string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            if (! $this instanceof ToStringInterface) {
                throw new LogicException(ToStringInterface::class . ' is not implemented');
            }

            return $this->toString();
        } catch (Throwable $e) {
            trigger_error($e->getMessage() . PHP_EOL . $e->getTraceAsString(), E_USER_ERROR);

            return '';
        }
    }

    #[Inject]
    public function setBaseDependencies(
        BuilderInterface $builder,
        FilterFactory $filterFactory,
        HelperLocatorFactory $helperFactory,
    ) {
        $this->builder = $builder;
        $this->filter = $filterFactory->newSubjectFilter();
        $this->helper = $helperFactory->newInstance();
    }

    public function setAntiCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->antiCsrf = $antiCsrf;
    }

    #[PostConstruct]
    public function postConstruct()
    {
        $this->init();
        if (! ($this->antiCsrf instanceof AntiCsrfInterface)) {
            return;
        }

        $this->antiCsrf->setField($this);
    }

    /**
     * {@inheritDoc}
     */
    public function input($input)
    {
        return $this->helper->input($this->get($input));
    }

    /**
     * {@inheritDoc}
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
     * @param array<string, mixed> $attr attributes for the form tag
     *
     * @return string
     *
     * @throws HelperNotFound
     * @throws NoSuchInput
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
     * @param array<string, mixed> $data
     *
     * @return bool
     *
     * @throws CsrfViolationException
     */
    public function apply(array $data)
    {
        if ($this->antiCsrf && ! $this->antiCsrf->isValid($data)) {
            throw new CsrfViolationException();
        }

        $this->fill($data);

        return $this->filter->apply($data);
    }

    /**
     * Returns all failure messages for all fields.
     *
     * @return array<string, array<string>>
     */
    public function getFailureMessages()
    {
        return $this->filter->getFailures()->getMessages();
    }

    /**
     * Returns all the fields collection
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->inputs);
    }
}
