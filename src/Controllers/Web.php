<?php
namespace Controllers;

use Models\InterfaceWeb\InterfaceWebModel;
use Views\InterfaceWeb\InterfaceWebView;

class Web extends AbstractController
{
    function getMethod()
    {
        $this->renderInterface();
    }

    function postMethod()
    {
        $this->renderInterface();
    }

    private function renderInterface()
    {
        $model = new InterfaceWebModel();
        $model->handleRequest();
        $url = $model->getCurrentUrl();
        $content = $model->getDisplayContent();
        $view = new InterfaceWebView();
        $view->render($url, $content);
    }
}
