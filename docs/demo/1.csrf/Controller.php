<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\WebFormModule;

use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\WebFormModule\Annotation\FormValidation;

class Controller
{
    public $response = ['code' => 200, 'body' => ''];

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @Inject
     * @Named("contact_form")
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    public function indexAction()
    {
        $this->response['body'] = $this->form;

        return $this;
    }

    /**
     * @FormValidation(form="form", onFailure="badRequestAction")
     */
    public function createUser($post)
    {
        $this->response['code'] = 201;
        $this->response['body'] = "{$post['message']} is sent by {$post['name']} !";

        return $this;
    }

    public function badRequestAction($name)
    {
        $this->response['code'] = 400;

        return $this->indexAction();
    }
}
