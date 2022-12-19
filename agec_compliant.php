<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

class AGEC_Compliant extends Module {
    function __construct()
    {
        $this->name = 'agec_compliant';
        $this->author = 'Adilis';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->displayName = $this->l('AGEC compliance');
        $this->description = $this->l('Module for AGEC compliance');
        $this->confirmUninstall = $this->l('Are you sure ?');

        parent::__construct();
    }


    public function install() {
        if (file_exists($this->getLocalPath().'sql/install.php')) {
            require_once($this->getLocalPath().'sql/install.php');
        }

       /* if (!\Tab::getIdFromClassName('AdminAGECCompliant')) {
            $tab = new Tab();
            $tab->class_name = 'AdminAGECCompliant';
            $tab->id_parent = 'SELL';
            $tab->module = $this->name;
            foreach(\Language::getLanguages(false) as $lang) {
                $tab->name[$lang['id_lang']] = $this->displayName;
            }
            if (!$tab->add()) {
                return false;
            }
        }*/

        return parent::install();
    }

    public function uninstall()
    {
        if (file_exists($this->getLocalPath().'sql/uninstall.php')) {
            require_once($this->getLocalPath().'sql/uninstall.php');
        }
        return parent::uninstall();
    }

    public function getContent() {
        if (\Tools::isSubmit('submit'.$this->name.'Module')) {

            $id_feature_dyeing = (int)\Tools::getValue('AC_ID_FEATURE_DYEING');
            $id_feature_weaving = (int)\Tools::getValue('AC_ID_FEATURE_WEAVING');
            $id_feature_confection = (int)\Tools::getValue('AC_ID_FEATURE_CONFECTION');

            if (!$id_feature_dyeing || !$id_feature_weaving || !$id_feature_confection) {
                $this->_html .= $this->displayError($this->l('Invalid values'));
            } else {
                Configuration::updateValue('AC_ID_FEATURE_DYEING', $id_feature_dyeing);
                Configuration::updateValue('AC_ID_FEATURE_WEAVING', $id_feature_weaving);
                Configuration::updateValue('AC_ID_FEATURE_CONFECTION', $id_feature_confection);
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            }

            if (!count($this->context->controller->errors)) {
                $redirect_after = $this->context->link->getAdminLink('AdminModules', true);
                $redirect_after .= '&conf=4&configure='.$this->name.'&module_name='.$this->name;
                \Tools::redirectAdmin($redirect_after);
            }
        }

        return $this->renderForm();
    }

    private function renderForm() {
        $helper = new \HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = \Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit'.$this->name.'Module';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false);
        $helper->currentIndex .= '&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = \Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'fields_value' => array(
                'AC_ID_FEATURE_DYEING' => \Tools::getValue('AC_ID_FEATURE_DYEING', \Configuration::get('AC_ID_FEATURE_DYEING')),
                'AC_ID_FEATURE_WEAVING' => \Tools::getValue('AC_ID_FEATURE_WEAVING', \Configuration::get('AC_ID_FEATURE_WEAVING')),
                'AC_ID_FEATURE_CONFECTION' => \Tools::getValue('AC_ID_FEATURE_CONFECTION', \Configuration::get('AC_ID_FEATURE_CONFECTION')),
            )
        );

        return $helper->generateForm([
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Parameters'),
                        'icon' => 'icon-cogs'
                    ],
                    'input' => [
                        [
                            'type' => 'select',
                            'name' => 'AC_ID_FEATURE_DYEING',
                            'id' => 'AC_ID_FEATURE_DYEING',
                            'label' => $this->l('Please select a feature for dyeing'),
                            'required' => true,
                            'options' => [
                                'default' => ['value' => null, 'label' => $this->l('Please select a feature')],
                                'query' => \Feature::getFeatures(\Context::getContext()->cookie->id_lang),
                                'id' => 'id_feature',
                                'name' => 'name'
                            ]
                        ],
                        [
                            'type' => 'select',
                            'name' => 'AC_ID_FEATURE_WEAVING',
                            'id' => 'AC_ID_FEATURE_WEAVING',
                            'label' => $this->l('Please select a feature for weaving'),
                            'required' => true,
                            'options' => [
                                'default' => ['value' => null, 'label' => $this->l('Please select a feature')],
                                'query' => \Feature::getFeatures(\Context::getContext()->cookie->id_lang),
                                'id' => 'id_feature',
                                'name' => 'name'
                            ]
                        ],
                        [
                            'type' => 'select',
                            'name' => 'AC_ID_FEATURE_CONFECTION',
                            'id' => 'AC_ID_FEATURE_CONFECTION',
                            'label' => $this->l('Please select a feature for confection'),
                            'required' => true,
                            'options' => [
                                'default' => ['value' => null, 'label' => $this->l('Please select a feature')],
                                'query' => \Feature::getFeatures(\Context::getContext()->cookie->id_lang),
                                'id' => 'id_feature',
                                'name' => 'name'
                            ]
                        ],
                    ],
                    'submit' => [
                        'title' => $this->l('Save')
                    ]
                ]
            ]
        ]);
    }

}