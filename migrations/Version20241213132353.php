<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213132353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE class_room_student (class_room_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_9483E8859162176F (class_room_id), INDEX IDX_9483E885CB944F1A (student_id), PRIMARY KEY(class_room_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE class_room_student ADD CONSTRAINT FK_9483E8859162176F FOREIGN KEY (class_room_id) REFERENCES `classroom` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE class_room_student ADD CONSTRAINT FK_9483E885CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF339162176F');
        $this->addSql('DROP INDEX IDX_B723AF339162176F ON student');
        $this->addSql('ALTER TABLE student DROP class_room_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE class_room_student DROP FOREIGN KEY FK_9483E8859162176F');
        $this->addSql('ALTER TABLE class_room_student DROP FOREIGN KEY FK_9483E885CB944F1A');
        $this->addSql('DROP TABLE class_room_student');
        $this->addSql('ALTER TABLE student ADD class_room_id INT NOT NULL');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF339162176F FOREIGN KEY (class_room_id) REFERENCES classroom (id)');
        $this->addSql('CREATE INDEX IDX_B723AF339162176F ON student (class_room_id)');
    }
}
