<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Preferences;

use PhpMyAdmin\Config;
use PhpMyAdmin\Config\ConfigFile;
use PhpMyAdmin\Config\Forms\User\FeaturesForm;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Message;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Theme\ThemeManager;
use PhpMyAdmin\TwoFactor;
use PhpMyAdmin\Url;
use PhpMyAdmin\UserPreferences;

use function define;
use function ltrim;

class FeaturesController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private UserPreferences $userPreferences,
        private Relation $relation,
        private Config $config,
        private ThemeManager $themeManager,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        $GLOBALS['cf'] ??= null;
        $GLOBALS['error'] ??= null;
        $GLOBALS['tabHash'] ??= null;
        $GLOBALS['hash'] ??= null;
        $GLOBALS['server'] ??= null;

        $GLOBALS['cf'] = new ConfigFile($this->config->baseSettings);
        $this->userPreferences->pageInit($GLOBALS['cf']);

        $formDisplay = new FeaturesForm($GLOBALS['cf'], 1);

        if ($request->hasBodyParam('revert')) {
            // revert erroneous fields to their default values
            $formDisplay->fixErrors();
            $this->redirect('/preferences/features');

            return;
        }

        $GLOBALS['error'] = null;
        if ($formDisplay->process(false) && ! $formDisplay->hasErrors()) {
            // Load 2FA settings
            $twoFactor = new TwoFactor(Config::getInstance()->selectedServer['user']);
            // save settings
            $result = $this->userPreferences->save($GLOBALS['cf']->getConfigArray());
            // save back the 2FA setting only
            $twoFactor->save();
            if ($result === true) {
                // reload config
                $this->config->loadUserPreferences($this->themeManager);
                $GLOBALS['tabHash'] = $request->getParsedBodyParam('tab_hash');
                $GLOBALS['hash'] = ltrim($GLOBALS['tabHash'], '#');
                $this->userPreferences->redirect('index.php?route=/preferences/features', null, $GLOBALS['hash']);

                return;
            }

            $GLOBALS['error'] = $result;
        }

        $relationParameters = $this->relation->getRelationParameters();

        $this->render('preferences/header', [
            'route' => $request->getRoute(),
            'is_saved' => $request->hasQueryParam('saved'),
            'has_config_storage' => $relationParameters->userPreferencesFeature !== null,
        ]);

        $formErrors = $formDisplay->displayErrors();

        $this->render('preferences/forms/main', [
            'error' => $GLOBALS['error'] instanceof Message ? $GLOBALS['error']->getDisplay() : '',
            'has_errors' => $formDisplay->hasErrors(),
            'errors' => $formErrors,
            'form' => $formDisplay->getDisplay(
                true,
                Url::getFromRoute('/preferences/features'),
                ['server' => $GLOBALS['server']],
            ),
        ]);

        if ($request->isAjax()) {
            $this->response->addJSON('disableNaviSettings', true);
        } else {
            define('PMA_DISABLE_NAVI_SETTINGS', true);
        }
    }
}
