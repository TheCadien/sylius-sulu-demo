import { startApp } from 'sulu-headless-bundle';
import interceptAnchorClick from 'sulu-headless-bundle/src/utils/interceptAnchorClick';
import viewRegistry from 'sulu-headless-bundle/src/registries/viewRegistry';
import HomepageTemplatePage from './views/HomepageTemplatePage';
import DefaultTemplatePage from './views/DefaultTemplatePage';

// register views for rendering page templates
viewRegistry.add('page', 'homepage', HomepageTemplatePage);
viewRegistry.add('page', 'default', DefaultTemplatePage);

// register views for rendering article templates
// viewRegistry.add('article', 'headless-template', HeadlessTemplateArticle);

// start react application in specific DOM element
startApp(document.getElementById('sulu-headless-container'));

const navigationLinks = document.querySelectorAll('nav a');
navigationLinks.forEach((navigationLink) => {
    navigationLink.addEventListener('click', interceptAnchorClick);
});
