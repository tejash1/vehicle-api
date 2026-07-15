<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260715000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema: brands, vehicle_models, vehicles, vehicle_images';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE brands (
                id           INT AUTO_INCREMENT NOT NULL,
                name         VARCHAR(100)       NOT NULL,
                country      VARCHAR(100)       DEFAULT NULL,
                logo_filename VARCHAR(255)      DEFAULT NULL,
                created_at   DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at   DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                UNIQUE INDEX uq_brands_name (name),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE vehicle_models (
                id         INT AUTO_INCREMENT NOT NULL,
                brand_id   INT                NOT NULL,
                name       VARCHAR(100)       NOT NULL,
                created_at DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_vehicle_models_brand_id (brand_id),
                UNIQUE INDEX uq_model_brand_name (brand_id, name),
                PRIMARY KEY(id),
                CONSTRAINT fk_vehicle_models_brand FOREIGN KEY (brand_id)
                    REFERENCES brands (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE vehicles (
                id               INT AUTO_INCREMENT NOT NULL,
                vehicle_model_id INT                NOT NULL,
                year             SMALLINT           NOT NULL,
                color            VARCHAR(50)        DEFAULT NULL,
                price            NUMERIC(10, 2)     DEFAULT NULL,
                mileage          INT                DEFAULT NULL,
                status           VARCHAR(20)        NOT NULL DEFAULT 'available',
                vin              VARCHAR(17)        DEFAULT NULL,
                description      LONGTEXT           DEFAULT NULL,
                created_at       DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                updated_at       DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_vehicle_status (status),
                INDEX idx_vehicle_year (year),
                INDEX idx_vehicles_vehicle_model_id (vehicle_model_id),
                UNIQUE INDEX uq_vehicles_vin (vin),
                PRIMARY KEY(id),
                CONSTRAINT fk_vehicles_model FOREIGN KEY (vehicle_model_id)
                    REFERENCES vehicle_models (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE vehicle_images (
                id            INT AUTO_INCREMENT NOT NULL,
                vehicle_id    INT                NOT NULL,
                filename      VARCHAR(255)       NOT NULL,
                original_name VARCHAR(255)       DEFAULT NULL,
                mime_type     VARCHAR(100)       DEFAULT NULL,
                size          INT                DEFAULT NULL,
                is_primary    TINYINT(1)         NOT NULL DEFAULT 0,
                sort_order    INT                NOT NULL DEFAULT 0,
                created_at    DATETIME           NOT NULL COMMENT '(DC2Type:datetime_immutable)',
                INDEX idx_vehicle_images_vehicle_id (vehicle_id),
                INDEX idx_vehicle_image_primary (vehicle_id, is_primary),
                PRIMARY KEY(id),
                CONSTRAINT fk_vehicle_images_vehicle FOREIGN KEY (vehicle_id)
                    REFERENCES vehicles (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vehicle_images DROP FOREIGN KEY fk_vehicle_images_vehicle');
        $this->addSql('ALTER TABLE vehicles DROP FOREIGN KEY fk_vehicles_model');
        $this->addSql('ALTER TABLE vehicle_models DROP FOREIGN KEY fk_vehicle_models_brand');
        $this->addSql('DROP TABLE vehicle_images');
        $this->addSql('DROP TABLE vehicles');
        $this->addSql('DROP TABLE vehicle_models');
        $this->addSql('DROP TABLE brands');
    }
}
