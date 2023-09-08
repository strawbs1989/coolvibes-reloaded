<?php
/**
 * Handles DB Multi-table query
 */

declare(strict_types=1);

namespace PhpMyAdmin\Database;

use PhpMyAdmin\ConfigStorage\Relation;
use PhpMyAdmin\ConfigStorage\RelationCleanup;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Operations;
use PhpMyAdmin\ParseAnalyze;
use PhpMyAdmin\Sql;
use PhpMyAdmin\Template;
use PhpMyAdmin\Transformations;
use PhpMyAdmin\Url;

use function md5;

/**
 * Class to handle database Multi-table querying
 */
class MultiTableQuery
{
    /**
     * Table names
     *
     * @var array<int, string>
     */
    private array $tables;

    public function __construct(
        private DatabaseInterface $dbi,
        public Template $template,
        private string $db,
        private int $defaultNoOfColumns = 3,
    ) {
        $this->tables = $this->dbi->getTables($this->db);
    }

    /**
     * Get Multi-Table query page HTML
     *
     * @return string Multi-Table query page HTML
     */
    public function getFormHtml(): string
    {
        $tables = [];
        foreach ($this->tables as $table) {
            $tables[$table]['hash'] = md5($table);
            $tables[$table]['columns'] = $this->dbi->getColumnNames($this->db, $table);
        }

        return $this->template->render('database/multi_table_query/form', [
            'db' => $this->db,
            'tables' => $tables,
            'default_no_of_columns' => $this->defaultNoOfColumns,
        ]);
    }

    /**
     * Displays multi-table query results
     *
     * @param string $sqlQuery The query to parse
     * @param string $db       The current database
     */
    public static function displayResults(string $sqlQuery, string $db): string
    {
        [, $db] = ParseAnalyze::sqlQuery($sqlQuery, $db);

        $goto = Url::getFromRoute('/database/multi-table-query');

        $dbi = DatabaseInterface::getInstance();
        $relation = new Relation($dbi);
        $sql = new Sql(
            $dbi,
            $relation,
            new RelationCleanup($dbi, $relation),
            new Operations($dbi, $relation),
            new Transformations(),
            new Template(),
        );

        return $sql->executeQueryAndSendQueryResponse(
            null,
            false, // is_gotofile
            $db, // db
            null, // table
            null, // find_real_end
            null, // sql_query_for_bookmark - see below
            null, // message_to_show
            null, // sql_data
            $goto, // goto
            null, // disp_query
            null, // disp_message
            $sqlQuery, // sql_query
            null, // complete_query
        );
    }
}
