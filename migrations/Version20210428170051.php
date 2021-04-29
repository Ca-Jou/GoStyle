<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210428170051 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_coupon (user_id INT NOT NULL, coupon_id INT NOT NULL NOT NULL, INDEX IDX_1ED243FA76ED395 (user_id), INDEX IDX_1ED243F66C5951B (coupon_id), PRIMARY KEY(user_id, coupon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_coupon ADD CONSTRAINT FK_1ED243FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_coupon ADD CONSTRAINT FK_1ED243F66C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_coupon');
    }
}
