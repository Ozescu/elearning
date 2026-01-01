<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260101112023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, created_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, is_global TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4DB9D91CFE54D947 (group_id), INDEX IDX_4DB9D91CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, pdf_file VARCHAR(255) DEFAULT NULL, deadline DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_F87474F3591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, deadline DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_A412FA92591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_question (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, question LONGTEXT NOT NULL, question_type VARCHAR(50) NOT NULL, options JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', correct_answer LONGTEXT DEFAULT NULL, INDEX IDX_6033B00B853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_lesson (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, lesson_id INT NOT NULL, completed TINYINT(1) NOT NULL, completed_at DATETIME DEFAULT NULL, INDEX IDX_7642AC73CB944F1A (student_id), INDEX IDX_7642AC73CDF80196 (lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_quiz (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, quiz_id INT NOT NULL, score DOUBLE PRECISION DEFAULT NULL, submitted_at DATETIME DEFAULT NULL, answers JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_9B31814ACB944F1A (student_id), INDEX IDX_9B31814A853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00B853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE student_lesson ADD CONSTRAINT FK_7642AC73CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student_lesson ADD CONSTRAINT FK_7642AC73CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE student_quiz ADD CONSTRAINT FK_9B31814ACB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student_quiz ADD CONSTRAINT FK_9B31814A853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91CFE54D947');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91CB03A8386');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3591CC992');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92591CC992');
        $this->addSql('ALTER TABLE quiz_question DROP FOREIGN KEY FK_6033B00B853CD175');
        $this->addSql('ALTER TABLE student_lesson DROP FOREIGN KEY FK_7642AC73CB944F1A');
        $this->addSql('ALTER TABLE student_lesson DROP FOREIGN KEY FK_7642AC73CDF80196');
        $this->addSql('ALTER TABLE student_quiz DROP FOREIGN KEY FK_9B31814ACB944F1A');
        $this->addSql('ALTER TABLE student_quiz DROP FOREIGN KEY FK_9B31814A853CD175');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_question');
        $this->addSql('DROP TABLE student_lesson');
        $this->addSql('DROP TABLE student_quiz');
    }
}
