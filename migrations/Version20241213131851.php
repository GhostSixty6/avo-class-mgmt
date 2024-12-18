<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213131851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE class_room_user (class_room_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8548A3129162176F (class_room_id), INDEX IDX_8548A312A76ED395 (user_id), PRIMARY KEY(class_room_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE class_room_user ADD CONSTRAINT FK_8548A3129162176F FOREIGN KEY (class_room_id) REFERENCES `classroom` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE class_room_user ADD CONSTRAINT FK_8548A312A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE class_room_user DROP FOREIGN KEY FK_8548A3129162176F');
        $this->addSql('ALTER TABLE class_room_user DROP FOREIGN KEY FK_8548A312A76ED395');
        $this->addSql('DROP TABLE class_room_user');
    }
}
