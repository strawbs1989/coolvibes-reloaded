<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database\Structure;

use PhpMyAdmin\Config;
use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\Controllers\AbstractController;
use PhpMyAdmin\DbTableExists;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\Identifiers\DatabaseName;
use PhpMyAdmin\Message;
use PhpMyAdmin\RecentFavoriteTable;
use PhpMyAdmin\ResponseRenderer;
use PhpMyAdmin\Template;
use PhpMyAdmin\Url;
use PhpMyAdmin\Util;

use function __;
use function count;
use function json_decode;
use function json_encode;
use function md5;
use function sha1;

final class FavoriteTableController extends AbstractController
{
    public function __construct(
        ResponseRenderer $response,
        Template $template,
        private Relation $relation,
        private readonly DbTableExists $dbTableExists,
    ) {
        parent::__construct($response, $template);
    }

    public function __invoke(ServerRequest $request): void
    {
        $GLOBALS['errorUrl'] ??= null;

        if ($GLOBALS['db'] === '') {
            return;
        }

        $config = Config::getInstance();
        $GLOBALS['errorUrl'] = Util::getScriptNameForOption($config->settings['DefaultTabDatabase'], 'database');
        $GLOBALS['errorUrl'] .= Url::getCommon(['db' => $GLOBALS['db']], '&');

        if (! $request->isAjax()) {
            return;
        }

        $favoriteInstance = RecentFavoriteTable::getInstance('favorite');

        $favoriteTables = $request->getParam('favoriteTables');
        if ($favoriteTables !== null) {
            $favoriteTables = json_decode($favoriteTables, true);
        } else {
            $favoriteTables = [];
        }

        // Required to keep each user's preferences separate.
        $user = sha1($config->selectedServer['user']);

        // Request for Synchronization of favorite tables.
        if ($request->getParam('sync_favorite_tables') !== null) {
            $relationParameters = $this->relation->getRelationParameters();
            if ($relationParameters->favoriteTablesFeature !== null) {
                $this->response->addJSON($this->synchronizeFavoriteTables(
                    $favoriteInstance,
                    $user,
                    $favoriteTables,
                ));
            }

            return;
        }

        $databaseName = DatabaseName::tryFrom($request->getParam('db'));
        if ($databaseName === null || ! $this->dbTableExists->hasDatabase($databaseName)) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', Message::error(__('No databases selected.')));

            return;
        }

        $changes = true;
        $favoriteTable = $request->getParam('favorite_table', '');
        $alreadyFavorite = $this->checkFavoriteTable($favoriteTable);

        if (isset($_REQUEST['remove_favorite'])) {
            if ($alreadyFavorite) {
                // If already in favorite list, remove it.
                $favoriteInstance->remove($GLOBALS['db'], $favoriteTable);
                $alreadyFavorite = false; // for favorite_anchor template
            }
        } elseif (isset($_REQUEST['add_favorite'])) {
            if (! $alreadyFavorite) {
                $numTables = count($favoriteInstance->getTables());
                if ($numTables == $config->settings['NumFavoriteTables']) {
                    $changes = false;
                } else {
                    // Otherwise add to favorite list.
                    $favoriteInstance->add($GLOBALS['db'], $favoriteTable);
                    $alreadyFavorite = true; // for favorite_anchor template
                }
            }
        }

        $favoriteTables[$user] = $favoriteInstance->getTables();

        $json = [];
        $json['changes'] = $changes;
        if (! $changes) {
            $json['message'] = $this->template->render('components/error_message', [
                'msg' => __('Favorite List is full!'),
            ]);
            $this->response->addJSON($json);

            return;
        }

        // Check if current table is already in favorite list.
        $favoriteParams = [
            'db' => $GLOBALS['db'],
            'ajax_request' => true,
            'favorite_table' => $favoriteTable,
            ($alreadyFavorite ? 'remove' : 'add') . '_favorite' => true,
        ];

        $json['user'] = $user;
        $json['favoriteTables'] = json_encode($favoriteTables);
        $json['list'] = $favoriteInstance->getHtmlList();
        $json['anchor'] = $this->template->render('database/structure/favorite_anchor', [
            'table_name_hash' => md5($favoriteTable),
            'db_table_name_hash' => md5($GLOBALS['db'] . '.' . $favoriteTable),
            'fav_params' => $favoriteParams,
            'already_favorite' => $alreadyFavorite,
        ]);

        $this->response->addJSON($json);
    }

    /**
     * Synchronize favorite tables
     *
     * @param RecentFavoriteTable $favoriteInstance Instance of this class
     * @param string              $user             The user hash
     * @param mixed[]             $favoriteTables   Existing favorites
     *
     * @return mixed[]
     */
    private function synchronizeFavoriteTables(
        RecentFavoriteTable $favoriteInstance,
        string $user,
        array $favoriteTables,
    ): array {
        $favoriteInstanceTables = $favoriteInstance->getTables();

        if ($favoriteInstanceTables === [] && isset($favoriteTables[$user])) {
            foreach ($favoriteTables[$user] as $value) {
                $favoriteInstance->add($value['db'], $value['table']);
            }
        }

        $favoriteTables[$user] = $favoriteInstance->getTables();

        // Set flag when localStorage and pmadb(if present) are in sync.
        $_SESSION['tmpval']['favorites_synced'][$GLOBALS['server']] = true;

        return ['favoriteTables' => json_encode($favoriteTables), 'list' => $favoriteInstance->getHtmlList()];
    }

    /**
     * Function to check if a table is already in favorite list.
     *
     * @param string $currentTable current table
     */
    private function checkFavoriteTable(string $currentTable): bool
    {
        $recentFavoriteTables = RecentFavoriteTable::getInstance('favorite');
        foreach ($recentFavoriteTables->getTables() as $value) {
            if ($value['db'] == $GLOBALS['db'] && $value['table'] == $currentTable) {
                return true;
            }
        }

        return false;
    }
}
