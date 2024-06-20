<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620172915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, player_id_id, bet, finished, american_score, british_score, winnings, deck, placement, saved_date FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, player_id_id INTEGER NOT NULL, bet INTEGER NOT NULL, finished DATE DEFAULT NULL, american_score INTEGER NOT NULL, british_score INTEGER NOT NULL, winnings INTEGER DEFAULT NULL, deck VARCHAR(255) NOT NULL, placement VARCHAR(255) NOT NULL, saved_date DATE NOT NULL, CONSTRAINT FK_232B318CC036E511 FOREIGN KEY (player_id_id) REFERENCES player (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game (id, player_id_id, bet, finished, american_score, british_score, winnings, deck, placement, saved_date) SELECT id, player_id_id, bet, finished, american_score, british_score, winnings, deck, placement, saved_date FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318CC036E511 ON game (player_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD COLUMN type VARCHAR(255) DEFAULT NULL');
    }
}
