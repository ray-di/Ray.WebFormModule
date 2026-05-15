<?php

declare(strict_types=1);

/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use ArrayIterator;
use function assert;
use Aura\Filter\FilterFactory;
use Aura\Filter\SubjectFilter;
use Aura\Html\HelperLocator;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\AntiCsrfInterface;
use Aura\Input\Builder;
use Aura\Input\BuilderInterface;
use Aura\Input\Fieldset;
use Exception;
use function is_string;
use Ray\Di\Di\Inject;
use Ray\Di\Di\PostConstruct;
use Ray\WebFormModule\Exception\CsrfViolationException;
use Ray\WebFormModule\Exception\LogicException;
use Stringable;
use function trigger_error;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractForm extends Fieldset implements FormInterface
{
    /** @var SubjectFilter */
    protected $filter;

    /** @var array<string, array<int, string>>|null */
    protected ?array $errorMessages = null;

    protected HelperLocator $helper;

    protected ?AntiCsrfInterface $antiCsrf = null;

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
     */
    public function __toString() : string
    {
        try {
            if (! $this instanceof ToStringInterface) {
                throw new LogicException(ToStringInterface::class . ' is not implemented');
            }

            return $this->toString();
        } catch (Exception $e) {
            trigger_error($e->getMessage() . PHP_EOL . $e->getTraceAsString(), E_USER_ERROR);
        }

        // Reachable when a custom error handler intercepts E_USER_ERROR without halting.
        return ''; // @phpstan-ignore deadCode.unreachable
    }

    /**
     * @param BuilderInterface     $builder
     * @param FilterFactory        $filterFactory
     * @param HelperLocatorFactory $helperFactory
     */
    #[Inject]
    public function setBaseDependencies(
        BuilderInterface $builder,
        FilterFactory $filterFactory,
        HelperLocatorFactory $helperFactory
    ) : void {
        assert($builder instanceof Builder);
        $this->builder = $builder;
        $this->filter = $filterFactory->newSubjectFilter();
        $this->helper = $helperFactory->newInstance();
    }

    public function setAntiCsrf(AntiCsrfInterface $antiCsrf) : void
    {
        $this->antiCsrf = $antiCsrf;
    }

    public function enableAntiCsrf(AntiCsrfInterface $antiCsrf) : void
    {
        $this->antiCsrf = $antiCsrf;
        if (isset($this->inputs[AntiCsrf::TOKEN_KEY])) {
            return;
        }

        $this->antiCsrf->setField($this);
    }

    #[PostConstruct]
    public function postConstruct() : void
    {
        $this->init();
        if ($this->antiCsrf instanceof AntiCsrfInterface) {
            $this->enableAntiCsrf($this->antiCsrf);
        }
    }

    /** {@inheritdoc} */
    public function input(string $input) : string
    {
        $inputHtml = $this->helper->input($this->get($input));
        assert(is_string($inputHtml) || $inputHtml instanceof Stringable);

        return (string) $inputHtml;
    }

    /** {@inheritdoc} */
    public function error(string $input) : string
    {
        if ($this->errorMessages === null) {
            /** @var \Aura\Filter\Failure\FailureCollection|null $failure */
            $failure = $this->filter->getFailures();
            if ($failure === null) {
                return '';
            }

            /** @var array<string, array<int, string>> $messages */
            $messages = $failure->getMessages();
            $this->errorMessages = $messages;
        }

        if (isset($this->errorMessages[$input])) {
            return $this->errorMessages[$input][0];
        }

        return '';
    }

    /**
     * @param array<string, mixed> $attr attributes for the form tag
     *
     * @throws \Aura\Input\Exception\NoSuchInput
     * @throws \Aura\Html\Exception\HelperNotFound
     */
    public function form(array $attr = []) : string
    {
        /** @var string $form */
        $form = $this->helper->form($attr);
        if (isset($this->inputs['__csrf_token'])) {
            /** @var string $input */
            $input = $this->helper->input($this->get('__csrf_token'));
            $form .= $input;
        }

        return $form;
    }

    /**
     * Applies the filter to a subject.
     *
     * @param array<string, mixed> $data
     *
     * @throws CsrfViolationException
     */
    public function apply(array $data) : bool
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
     * @return array<string, array<int, string>>
     */
    public function getFailureMessages() : array
    {
        /** @var array<string, array<int, string>> $messages */
        $messages = $this->filter->getFailures()->getMessages();

        return $messages;
    }

    /**
     * Returns all the fields collection
     *
     * @return ArrayIterator<string, mixed>
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->inputs);
    }
}
