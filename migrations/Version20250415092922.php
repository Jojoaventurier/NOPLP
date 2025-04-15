<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415092922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(150) DEFAULT NULL, lyrics LONGTEXT DEFAULT NULL, is_duo TINYINT(1) DEFAULT NULL, is_downloaded TINYINT(1) DEFAULT NULL, has_lyrics TINYINT(1) DEFAULT NULL, is_listened TINYINT(1) DEFAULT NULL, noplp_count INT DEFAULT NULL, normal_play_count INT DEFAULT NULL, user_song_knowledge VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_person (song_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_32A7C0FCA0BDB2F3 (song_id), INDEX IDX_32A7C0FC217BBB47 (person_id), PRIMARY KEY(song_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE song_review (id INT AUTO_INCREMENT NOT NULL, song_id INT DEFAULT NULL, reviewed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7F38904CA0BDB2F3 (song_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tv_show (id INT AUTO_INCREMENT NOT NULL, air_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_person ADD CONSTRAINT FK_32A7C0FCA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_person ADD CONSTRAINT FK_32A7C0FC217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_review ADD CONSTRAINT FK_7F38904CA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_person DROP FOREIGN KEY FK_32A7C0FCA0BDB2F3');
        $this->addSql('ALTER TABLE song_person DROP FOREIGN KEY FK_32A7C0FC217BBB47');
        $this->addSql('ALTER TABLE song_review DROP FOREIGN KEY FK_7F38904CA0BDB2F3');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE song');
        $this->addSql('DROP TABLE song_person');
        $this->addSql('DROP TABLE song_review');
        $this->addSql('DROP TABLE tv_show');
    }
}
