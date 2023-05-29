<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230529081451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE albums_artists (album_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_8BB2B6C11137ABCF (album_id), INDEX IDX_8BB2B6C1B7970CF8 (artist_id), PRIMARY KEY(album_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE albums_artists ADD CONSTRAINT FK_8BB2B6C11137ABCF FOREIGN KEY (album_id) REFERENCES albums (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE albums_artists ADD CONSTRAINT FK_8BB2B6C1B7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE albums_artists DROP FOREIGN KEY FK_8BB2B6C11137ABCF');
        $this->addSql('ALTER TABLE albums_artists DROP FOREIGN KEY FK_8BB2B6C1B7970CF8');
        $this->addSql('DROP TABLE albums_artists');
    }
}
