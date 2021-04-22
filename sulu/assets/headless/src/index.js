import { startApp } from 'sulu-headless-bundle';
import interceptAnchorClick from 'sulu-headless-bundle/src/utils/interceptAnchorClick';
import viewRegistry from 'sulu-headless-bundle/src/registries/viewRegistry';
import App from './components/App';
import HomepageTemplatePage from './views/HomepageTemplatePage';
import DefaultTemplatePage from './views/DefaultTemplatePage';
import ProductPresentationTemplatePage from './views/ProductPresentationTemplatePage';

import 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle';
import 'bootstrap/dist/css/bootstrap.min.css';
import './style.css';

// register views for rendering page templates
viewRegistry.add('page', 'homepage', HomepageTemplatePage);
viewRegistry.add('page', 'default', DefaultTemplatePage);
viewRegistry.add('page', 'productPresentation', ProductPresentationTemplatePage);

// register views for rendering article templates
// viewRegistry.add('article', 'headless-template', HeadlessTemplateArticle);

// start react application in specific DOM element
startApp(document.getElementById('sulu-headless-container'), App);

const navigationLinks = document.querySelectorAll('nav a');
navigationLinks.forEach((navigationLink) => {
    navigationLink.addEventListener('click', interceptAnchorClick);
});
