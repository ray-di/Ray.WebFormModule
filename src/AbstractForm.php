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

/** @psalm-suppress PropertyNotSetInConstructor */
abstract class AbstractForm extends Fieldset implements FormInterface
{
    /**
     * @var SubjectFilter
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected $filter; // @phpstan-ignore-line

    /** @var array<string, array<string>>|null */
    protected $errorMessages;

    /** @var HelperLocator */
    protected $helper;

    /** @var AntiCsrfInterface|null */
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

            return ''; // @codeCoverageIgnore @phpstan-ignore deadCode.unreachable
        }
    }

    #[Inject]
    public function setBaseDependencies(
        BuilderInterface $builder,
        FilterFactory $filterFactory,
        HelperLocatorFactory $helperFactory,
    ): void {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->builder = $builder; // @phpstan-ignore-line
        $this->filter = $filterFactory->newSubjectFilter();
        $this->helper = $helperFactory->newInstance();
    }

    public function setAntiCsrf(AntiCsrfInterface $antiCsrf): void
    {
        $this->antiCsrf = $antiCsrf;
    }

    #[PostConstruct]
    public function postConstruct(): void
    {
        $this->init();
        if ($this->antiCsrf === null) {
            return;
        }

        $this->antiCsrf->setField($this);
    }

    /**
     * {@inheritDoc}
     */
    public function input($input)
    {
        /**
         * @var string $result
         * @psalm-suppress UndefinedMagicMethod
         */
        $result = $this->helper->input($this->get($input)); // @phpstan-ignore-line

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function error($input)
    {
        if ($this->errorMessages === null) {
            $failure = $this->filter->getFailures();
            /**
             * @psalm-suppress RedundantConditionGivenDocblockType - getFailures() can return null at runtime
             * @phpstan-ignore notIdentical.alwaysTrue
             */
            if ($failure !== null) {
                /** @var array<string, array<string>> $messages */
                $messages = $failure->getMessages();
                $this->errorMessages = $messages;
            } else {
                $this->errorMessages = [];
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
        /**
         * @var string $form
         * @psalm-suppress UndefinedMagicMethod
         */
        $form = $this->helper->form($attr); // @phpstan-ignore-line
        if (isset($this->inputs['__csrf_token'])) {
            /** @psalm-suppress UndefinedMagicMethod */
            $form .= $this->helper->input($this->get('__csrf_token')); // @phpstan-ignore-line
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
        if ($this->antiCsrf !== null && ! $this->antiCsrf->isValid($data)) {
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
        /** @var array<string, array<string>> $messages */
        $messages = $this->filter->getFailures()->getMessages();

        return $messages;
    }

    /**
     * Returns all the fields collection
     *
     * @return ArrayIterator<array-key, mixed>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->inputs);
    }
}
