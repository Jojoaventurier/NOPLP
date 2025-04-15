<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415092529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA145DB008A');
        $this->addSql('CREATE TABLE song_review (id INT AUTO_INCREMENT NOT NULL, song_id INT DEFAULT NULL, reviewed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7F38904CA0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_review ADD CONSTRAINT FK_7F38904CA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
        $this->addSql('DROP TABLE user_song_knowledge');
        $this->addSql('DROP INDEX IDX_33EDEEA145DB008A ON song');
        $this->addSql('ALTER TABLE song ADD is_listened TINYINT(1) DEFAULT NULL, ADD normal_play_count INT DEFAULT NULL, ADD user_song_knowledge VARCHAR(255) DEFAULT NULL, CHANGE user_song_knowledge_id noplp_count INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_song_knowledge (id INT AUTO_INCREMENT NOT NULL, knowledge_level INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE song_review DROP FOREIGN KEY FK_7F38904CA0BDB2F3');
        $this->addSql('DROP TABLE song_review');
        $this->addSql('ALTER TABLE song ADD user_song_knowledge_id INT DEFAULT NULL, DROP is_listened, DROP noplp_count, DROP normal_play_count, DROP user_song_knowledge');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA145DB008A FOREIGN KEY (user_song_knowledge_id) REFERENCES user_song_knowledge (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_33EDEEA145DB008A ON song (user_song_knowledge_id)');
    }
}
