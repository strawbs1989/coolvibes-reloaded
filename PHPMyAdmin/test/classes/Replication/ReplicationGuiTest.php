<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Replication;

use PhpMyAdmin\Config;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Replication\Replication;
use PhpMyAdmin\Replication\ReplicationGui;
use PhpMyAdmin\Replication\ReplicationInfo;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

#[CoversClass(ReplicationGui::class)]
class ReplicationGuiTest extends AbstractTestCase
{
    /**
     * ReplicationGui instance
     */
    private ReplicationGui $replicationGui;

    /**
     * Prepares environment for the test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseInterface::$instance = $this->createDatabaseInterface();
        //$_POST
        $_POST['primary_add_user'] = 'primary_add_user';

        //$GLOBALS
        $config = Config::getInstance();
        $config->settings['MaxRows'] = 10;
        $config->settings['ServerDefault'] = 'server';
        $config->settings['RememberSorting'] = true;
        $config->settings['SQP'] = [];
        $config->settings['MaxCharactersInDisplayedSQL'] = 1000;
        $config->settings['ShowSQL'] = true;
        $config->settings['TableNavigationLinksMode'] = 'icons';
        $config->settings['LimitChars'] = 100;
        $config->settings['DBG']['sql'] = false;
        $config->settings['ShowHint'] = true;

        $GLOBALS['table'] = 'table';
        $GLOBALS['server'] = 0;
        $GLOBALS['urlParams'] = [];

        $this->replicationGui = new ReplicationGui(new Replication(DatabaseInterface::getInstance()), new Template());
    }

    #[Group('medium')]
    public function testGetHtmlForPrimaryReplication(): void
    {
        $html = $this->replicationGui->getHtmlForPrimaryReplication(null, false, 'primary_add_user', null, null);

        //validate 1: Primary replication
        $this->assertStringContainsString('<div class="card-header">Primary replication</div>', $html);
        $this->assertStringContainsString('This server is configured as primary in a replication process.', $html);

        //validate 2: getHtmlForReplicationStatusTable
        $this->assertStringContainsString('<div id="replication_primary_section"', $html);

        $this->assertStringContainsString('Binlog_Do_DB', $html);
        $this->assertStringContainsString('Binlog_Ignore_DB', $html);

        $this->assertStringContainsString('primary-bin.000030', $html);

        //validate 3: getHtmlForReplicationReplicasTable
        $this->assertStringContainsString('replication_replicas_section', $html);
        $this->assertStringContainsString('<th>Server ID</th>', $html);
        $this->assertStringContainsString('<th>Host</th>', $html);
        //replica host
        $this->assertStringContainsString('<td class="text-end font-monospace">Server_id1</td>', $html);
        $this->assertStringContainsString('<td class="text-end font-monospace">Server_id2</td>', $html);
        $this->assertStringContainsString('<td class="text-end font-monospace">Host1</td>', $html);
        $this->assertStringContainsString('<td class="text-end font-monospace">Host2</td>', $html);
        //Notice
        $this->assertStringContainsString('Only replicas started with the', $html);

        //validate 4: navigation URL
        $this->assertStringContainsString('<a href="index.php?route=/server/replication', $html);
        $this->assertStringContainsString('Add replica replication user', $html);

        //validate 5: 'Add replication replica user' form
        $this->assertStringContainsString('<div id="primary_addreplicauser_gui">', $html);
    }

    public function testGetHtmlForReplicaConfiguration(): void
    {
        $replicationInfo = new ReplicationInfo(DatabaseInterface::getInstance());
        $replicationInfo->load();

        //Call the test function
        $html = $this->replicationGui->getHtmlForReplicaConfiguration(
            null,
            true,
            $replicationInfo->getReplicaStatus(),
            isset($_POST['replica_configure']),
        );

        //legend
        $this->assertStringContainsString('<div class="card-header">Replica replication</div>', $html);
        $this->assertStringContainsString('<div id="replica_configuration_gui">', $html);
        //notice
        $this->assertStringContainsString('Server is configured as replica in a replication process.', $html);
        //replica session
        $this->assertStringContainsString('<div id="replication_replica_section"', $html);
        //variable
        $this->assertStringContainsString('Master_SSL_CA_Path', $html);
        $this->assertStringContainsString('Master_SSL_Cert', $html);
        $this->assertStringContainsString('Master_SSL_Cipher', $html);
        $this->assertStringContainsString('Seconds_Behind_Master', $html);
    }

    public function testGetHtmlForReplicationChangePrimary(): void
    {
        //Call the test function
        $html = $this->replicationGui->getHtmlForReplicationChangePrimary('replica_changeprimary');

        $this->assertStringContainsString('<form method="post" action="index.php?route=/server/replication', $html);
        $this->assertStringContainsString('Replica configuration', $html);
        $this->assertStringContainsString('Change or reconfigure primary server', $html);
        $notice = 'Make sure you have a unique server-id in your configuration file (my.cnf)';
        $this->assertStringContainsString($notice, $html);
    }
}
