<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321152052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) DEFAULT NULL, lyrics LONGTEXT DEFAULT NULL, is_duo TINYINT(1) DEFAULT NULL, is_downloaded TINYINT(1) DEFAULT NULL, has_lyrics TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_person (song_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_32A7C0FCA0BDB2F3 (song_id), INDEX IDX_32A7C0FC217BBB47 (person_id), PRIMARY KEY(song_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tv_show (id INT AUTO_INCREMENT NOT NULL, air_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_song_knowledge (id INT AUTO_INCREMENT NOT NULL, knowledge_level INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_person ADD CONSTRAINT FK_32A7C0FCA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_person ADD CONSTRAINT FK_32A7C0FC217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_person DROP FOREIGN KEY FK_32A7C0FCA0BDB2F3');
        $this->addSql('ALTER TABLE song_person DROP FOREIGN KEY FK_32A7C0FC217BBB47');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE song');
        $this->addSql('DROP TABLE song_person');
        $this->addSql('DROP TABLE tv_show');
        $this->addSql('DROP TABLE user_song_knowledge');
    }
}
