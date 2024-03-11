<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311155724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joueur ADD equipe_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C5254808DD FOREIGN KEY (equipe_id_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_FD71A9C5254808DD ON joueur (equipe_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joueur DROP FOREIGN KEY FK_FD71A9C5254808DD');
        $this->addSql('DROP INDEX IDX_FD71A9C5254808DD ON joueur');
        $this->addSql('ALTER TABLE joueur DROP equipe_id_id');
    }
}
