<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522085909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, player_id_id INTEGER NOT NULL, bet INTEGER NOT NULL, type VARCHAR(255) DEFAULT NULL, finished DATE NOT NULL, american_score INTEGER NOT NULL, british_score INTEGER NOT NULL, winnings INTEGER NOT NULL, deck VARCHAR(255) NOT NULL, placement VARCHAR(255) NOT NULL, saved_date DATE NOT NULL, CONSTRAINT FK_232B318CC036E511 FOREIGN KEY (player_id_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_232B318CC036E511 ON game (player_id_id)');
        $this->addSql('DROP TABLE finished_games');
        $this->addSql('DROP TABLE saved_games');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE finished_games (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, player_id_id INTEGER NOT NULL, bet INTEGER NOT NULL, type VARCHAR(255) DEFAULT NULL COLLATE "BINARY", finished DATE NOT NULL, american_score INTEGER NOT NULL, british_score INTEGER NOT NULL, winnings INTEGER NOT NULL, CONSTRAINT FK_1FD6C745C036E511 FOREIGN KEY (player_id_id) REFERENCES player (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1FD6C745C036E511 ON finished_games (player_id_id)');
        $this->addSql('CREATE TABLE saved_games (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, player_id_id INTEGER NOT NULL, deck VARCHAR(255) NOT NULL COLLATE "BINARY", placement VARCHAR(255) NOT NULL COLLATE "BINARY", finished BOOLEAN NOT NULL, bet INTEGER NOT NULL, type VARCHAR(255) DEFAULT NULL COLLATE "BINARY", saved_date DATE NOT NULL, CONSTRAINT FK_92F0F028C036E511 FOREIGN KEY (player_id_id) REFERENCES player (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_92F0F028C036E511 ON saved_games (player_id_id)');
        $this->addSql('DROP TABLE game');
    }
}
