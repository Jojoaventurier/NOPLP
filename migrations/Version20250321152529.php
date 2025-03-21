<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321152529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song ADD user_song_knowledge_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA145DB008A FOREIGN KEY (user_song_knowledge_id) REFERENCES user_song_knowledge (id)');
        $this->addSql('CREATE INDEX IDX_33EDEEA145DB008A ON song (user_song_knowledge_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA145DB008A');
        $this->addSql('DROP INDEX IDX_33EDEEA145DB008A ON song');
        $this->addSql('ALTER TABLE song DROP user_song_knowledge_id');
    }
}
