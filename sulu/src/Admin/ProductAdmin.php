<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Product;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\TogglerToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class ProductAdmin extends Admin
{
    const PRODUCT_LIST_KEY = 'products';

    const PRODUCT_FORM_KEY = 'product_details';

    const PRODUCT_LIST_VIEW = 'app.products_list';

    const PRODUCT_ADD_FORM_VIEW = 'app.product_add_form';

    const PRODUCT_EDIT_FORM_VIEW = 'app.product_edit_form';

    /**
     * @var ViewBuilderFactoryInterface
     */
    private $viewBuilderFactory;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        WebspaceManagerInterface $webspaceManager
    )
    {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->webspaceManager = $webspaceManager;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $module = new NavigationItem('app.products');
        $module->setPosition(40);
        $module->setIcon('fa-dollar');
        $module->setView(static::PRODUCT_LIST_VIEW);

        $navigationItemCollection->add($module);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();

        // Configure Product List View
        $listToolbarActions = [new ToolbarAction('sulu_admin.add'), new ToolbarAction('sulu_admin.delete')];
        $listView = $this->viewBuilderFactory->createListViewBuilder(self::PRODUCT_LIST_VIEW, '/products/:locale')
            ->setResourceKey(Product::RESOURCE_KEY)
            ->setListKey(self::PRODUCT_LIST_KEY)
            ->setTitle('app.products')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddView(static::PRODUCT_ADD_FORM_VIEW)
            ->setEditView(static::PRODUCT_EDIT_FORM_VIEW)
            ->addToolbarActions($listToolbarActions);
        $viewCollection->add($listView);

        // Configure Product Add View
        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(self::PRODUCT_ADD_FORM_VIEW, '/products/:locale/add')
            ->setResourceKey(Product::RESOURCE_KEY)
            ->setBackView(static::PRODUCT_LIST_VIEW)
            ->addLocales($locales);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(self::PRODUCT_ADD_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Product::RESOURCE_KEY)
            ->setFormKey(self::PRODUCT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::PRODUCT_EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::PRODUCT_ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);

        // Configure Product Edit View
        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(self::PRODUCT_EDIT_FORM_VIEW, '/products/:locale/:id')
            ->setResourceKey(Product::RESOURCE_KEY)
            ->setBackView(static::PRODUCT_LIST_VIEW)
            ->setTitleProperty('title')
            ->addLocales($locales);
        $viewCollection->add($editFormView);

        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(self::PRODUCT_EDIT_FORM_VIEW . '.details', '/details')
            ->setResourceKey(Product::RESOURCE_KEY)
            ->setFormKey(self::PRODUCT_FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([new ToolbarAction('sulu_admin.save'), new ToolbarAction('sulu_admin.delete')])
            ->setParent(static::PRODUCT_EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);
    }
}