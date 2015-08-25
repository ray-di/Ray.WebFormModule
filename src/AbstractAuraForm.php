<?php
/**
 * This file is part of the Ray.WebFormModule package
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Aura\Html\HelperLocator;
use Aura\Html\HelperLocatorFactory;
use Aura\Input\AntiCsrfInterface;
use Aura\Input\Form;
use Ray\Di\Di\Inject;

abstract class AbstractAuraForm extends Form implements FormInterface
{
    /**
     * @var HelperLocator
     */
    protected $helper;

    /**
     * HTML
     *
     * @var string
     */
    protected $string = '<form></form>';

    /**
     * @Inject
     */
    public function setFormHelper(HelperLocatorFactory $factory)
    {
        $this->helper = $factory->newInstance();
    }

    /**
     * @param AntiCsrfInterface $antiCsrf
     */
    public function setCsrf(AntiCsrfInterface $antiCsrf)
    {
        $this->setAntiCsrf($antiCsrf);
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
        $errorMessages = $this->getFilter()->getMessages($input);
        array_filter($errorMessages, function (&$item) use ($format) {
            $item = sprintf($format, $item);
        });
        $errors = implode('', $errorMessages);

        return sprintf($layout, $errors);
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
}
