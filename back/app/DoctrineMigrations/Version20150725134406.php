<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150725134406 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE GracenoteResponse');
        $this->addSql('DROP TABLE ImdbInfosResponse');
        $this->addSql('DROP TABLE ImdbSearchResponse');
        $this->addSql('DROP TABLE Log');
        $this->addSql('DROP TABLE StatsDaily');
        $this->addSql('DROP TABLE StatsGlobal');
        $this->addSql('DROP TABLE ponthubfile_user');
        $this->addSql('DROP TABLE user_course');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE GracenoteResponse (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, artist VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, year VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ImdbInfosResponse (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, year VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, duration INT NOT NULL, genres VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, director VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, actors VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, rating VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ImdbSearchResponse (ids INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, year VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(ids)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Log (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, date INT NOT NULL, method VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, url VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, params VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, code INT NOT NULL, ip VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, browser VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, system VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, agent VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE StatsDaily (id INT AUTO_INCREMENT NOT NULL, promo VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, httpVerbs LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', httpCodes LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', systems LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', browsers LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', connections INT NOT NULL, connectionsUnique INT NOT NULL, year INT NOT NULL, week INT NOT NULL, day INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE StatsGlobal (id INT AUTO_INCREMENT NOT NULL, promo VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, httpVerbs LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', httpCodes LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', systems LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', browsers LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', hours LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', connections INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ponthubfile_user (ponthubfile_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8E3F280A79A29844 (ponthubfile_id), INDEX IDX_8E3F280AA76ED395 (user_id), PRIMARY KEY(ponthubfile_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_course (user_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_73CC7484A76ED395 (user_id), INDEX IDX_73CC7484591CC992 (course_id), PRIMARY KEY(user_id, course_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
