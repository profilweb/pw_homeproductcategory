<?php
/**
* PW HomeProductcategory
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @author    Profil Web
* @copyright Copyright 2021 ©profilweb All right reserved
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @package   pw_homecategories
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

Class Pw_HomeProductcategory extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'pw_homeproductcategory';
        $this->author = 'Profil Web';
        $this->version = '1.5.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '8.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('PW Home Product Category');
        $this->description = $this->l('Display products from one category on your homepage');

        $this->templateFile = 'module:pw_homeproductcategory/views/templates/hook/pw_homeproductcategory.tpl';
    }

    public function install()
    {
        $this->_clearCache('*');

        Configuration::updateValue('PW_HOME_PROD_CAT_NBR', 8);
        Configuration::updateValue('PW_HOME_PROD_CAT_BGCOLOR', '#FFFFFF');
        Configuration::updateValue('PW_HOME_PROD_CAT_CAT', (int) Context::getContext()->shop->getCategory());

        return parent::install()
            //&& $this->registerHook('actionProductAdd')
            //&& $this->registerHook('actionProductUpdate')
            //&& $this->registerHook('actionProductDelete')
            && $this->registerHook('displayHome')
            //&& $this->registerHook('displayOrderConfirmation2')
            //&& $this->registerHook('displayCrossSellingShoppingCart')
            //&& $this->registerHook('actionCategoryUpdate')
            //&& $this->registerHook('actionAdminGroupsControllerSaveAfter')
        ;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }

    /*public function hookActionProductAdd($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductDelete($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionAdminGroupsControllerSaveAfter($params)
    {
        $this->_clearCache('*');
    }*/

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function getContent()
    {
        $output = '';
        $errors = [];

        if (Tools::isSubmit('submitPWHomeProductCategory')) {
            $nbr = Tools::getValue('PW_HOME_PROD_CAT_NBR');
            if (!Validate::isInt($nbr) || $nbr <= 0) {
                $errors[] = $this->l('The number of products is invalid. Please enter a positive number.');
            }

            $cat = Tools::getValue('PW_HOME_PROD_CAT_CAT');
            if (!Validate::isInt($cat) || $cat <= 0) {
                $errors[] = $this->l('The category ID is invalid. Please choose an existing category ID.');
            }

            $bgcolor = Tools::getValue('PW_HOME_PROD_CAT_BGCOLOR');
            if (empty($bgcolor)) {
                $errors[] = $this->l('The background-color cannot be empty. Please choose an value.');
            }

            if (count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                Configuration::updateValue('PW_HOME_PROD_CAT_NBR', (int) $nbr);
                Configuration::updateValue('PW_HOME_PROD_CAT_CAT', (int) $cat);
                Configuration::updateValue('PW_HOME_PROD_CAT_BGCOLOR', $bgcolor);

                $this->_clearCache('*');

                $output = $this->displayConfirmation($this->l('The settings have been updated.'));
            }
        }

        return $output . $this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],

                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Number of products to be displayed'),
                        'name' => 'PW_HOME_PROD_CAT_NBR',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Set the number of products that you would like to display on homepage (default: 8).'),
                    ],
                    [
                        'type' => 'categories',
                        'tree' => [
                          'id' => 'pw_home_product_category',
                          'selected_categories' => [Configuration::get('PW_HOME_PROD_CAT_CAT')],
                        ],
                        'label' => $this->l('Category from which to pick products to be displayed'),
                        'name' => 'PW_HOME_PROD_CAT_CAT',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('background-color of the block'),
                        'name' => 'PW_HOME_PROD_CAT_BGCOLOR',
                        'class' => 'lg',
                        'desc' => $this->l('Set the background color (default: #ffffff).'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPWHomeProductCategory';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        return [
            'PW_HOME_PROD_CAT_BGCOLOR' => Tools::getValue('PW_HOME_PROD_CAT_BGCOLOR', Configuration::get('PW_HOME_PROD_CAT_BGCOLOR')),
            'PW_HOME_PROD_CAT_NBR' => Tools::getValue('PW_HOME_PROD_CAT_NBR', (int) Configuration::get('PW_HOME_PROD_CAT_NBR')),
            'PW_HOME_PROD_CAT_CAT' => Tools::getValue('PW_HOME_PROD_CAT_CAT', (int) Configuration::get('PW_HOME_PROD_CAT_CAT')),
        ];
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('pw_homeproductcategory'))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);

            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('pw_homeproductcategory'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $products = $this->getProducts();
        $cat_info = $this->getCategoryInfo((int) Configuration::get('PW_HOME_PROD_CAT_CAT'));

        if (!empty($products)) {
            return [
                'bgcolor' => $this->getConfigFieldsValues()['PW_HOME_PROD_CAT_BGCOLOR'],
                'products' => $products,
                'cat_info' => $cat_info,
                'allProductsLink' => Context::getContext()->link->getCategoryLink($this->getConfigFieldsValues()['PW_HOME_PROD_CAT_CAT']),
            ];
        }
        
        return false;
    }

    protected function getCategoryInfo($id_category, $imageType = 'category_default')
    {
        
        // Get context
        $context = Context::getContext();

        // Get category object
        $category = new Category($id_category, $context->language->id);
       
        // Check if category exists and has an image
        if (!Validate::isLoadedObject($category) || !$category->id_image) {
            return false;
        }

        // Prepare result
        $result = [
            'name' => $category->name,
            'image_url' => false,
        ];

        // Get image URL if exists
        if ($category->id_image) {
            $image = new Image($category->id_image);
            if (Validate::isLoadedObject($image)) {
                $result['image_url'] = $context->link->getCatImageLink(
                    $category->link_rewrite,
                    $category->id,
                    $imageType
                );
            }
        }

        return $result;
    }

    protected function getProducts()
    {
        $category = new Category((int) Configuration::get('PW_HOME_PROD_CAT_CAT'));

        $searchProvider = new CategoryProductSearchProvider(
            $this->context->getTranslator(),
            $category
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = Configuration::get('PW_HOME_PROD_CAT_NBR');
        if ($nProducts < 0) {
            $nProducts = 12;
        }

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

        $query->setSortOrder(new SortOrder('product', 'position', 'asc'));

        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = $presenterFactory->getPresenter();

        $products_for_template = [];

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products_for_template;
    }

    protected function getCacheId($name = null)
    {
        $cacheId = parent::getCacheId($name);
        if (!empty($this->context->customer->id)) {
            $cacheId .= '|' . $this->context->customer->id;
        }

        return $cacheId;
    }
}
