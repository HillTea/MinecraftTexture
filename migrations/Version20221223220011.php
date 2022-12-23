<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221223220011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags_products (tags_id INT NOT NULL, products_id INT NOT NULL, INDEX IDX_D92CC6438D7B4FB4 (tags_id), INDEX IDX_D92CC6436C8A81A9 (products_id), PRIMARY KEY(tags_id, products_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tags_products ADD CONSTRAINT FK_D92CC6438D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags_products ADD CONSTRAINT FK_D92CC6436C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tags_products DROP FOREIGN KEY FK_D92CC6438D7B4FB4');
        $this->addSql('ALTER TABLE tags_products DROP FOREIGN KEY FK_D92CC6436C8A81A9');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tags_products');
    }
}
