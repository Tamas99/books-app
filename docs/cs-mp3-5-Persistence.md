# Persistence

Symfony ORM keretrendszerek:
 - **Doctrine ORM** - legjobban elterjedt
 - **Eloquent ORM** - inkább Laravelben használjál
 - **Propel ORM**

A **Doctrine** egy hasonló ORM rendszer, mint a **Hibernate** Javában és ezt használják leggyakrabban Symfonyval.

Megfelelő csomagok telepítése: **doctrine/dbal**, **doctrine/doctrine-bundle**, **doctrine/doctrine-migrations-bundle**, **doctrine/orm**.

Itt is annotációs mechanizmussal alakul a PHP objektum át és képezi le egy adatbázisba. Egyszerű mentés-, lekérés-, módosítás- és törléshez nem kell a fejlesztő saját kódot írjon. A műveletekhez a **Doctrine\ORM\EntityManagerInterface** implementációja szolgál végrehajtást.

A PHP objektum a **Doctrine\ORM\Mapping\Entity** osztály **#[ORM\Entity(repositoryClass: BookRepository::class)]** annotációjával kerül menedzselhető állapotba, illetve tartalmazhat egy adattagja egy **#[ORM\Id]** annotációt, amivel egyedivé válik.

Az adatbázis kapcsolat kialakításához a **.env** file-ban létrehozva egy környezeti változót egy értékkel tehetjük meg, amit aztán a **config/packages/doctrine.yaml** konfigurációs fileban fel kell használnunk a **doctrine.dbal.url** kulcs értékeként.

Adatbázis kapcsolat connection string:

```ini
# .env
APP_ENV=dev

DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```

Egy kezdeti projekt doctrine.yaml konfigurációs fájlja:

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'

        # adatbázis szerver verziója itt, vagy a .env file-ban
        server_version: '8.0.32'

        profiling_collect_backtrace: '%kernel.debug%'
        use_savepoints: true
    orm:
        auto_generate_proxy_classes: true
        enable_lazy_ghost_objects: true
        report_fields_where_declared: true
        validate_xml_mapping: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App
        controller_resolver:
            auto_mapping: true

# felülírja az alapértelmezett konfigurációt, ha
# test környezet van beállítva
when@test:
    doctrine:
        dbal:
            dbname_suffix: '_test%env(default::TEST_TOKEN)%'
```

A Doctrine úgynevezett DataFixture-eket használ az adatbázis feltöltésére dummy adattal. Ezeket tesztelésre lehet használni vagy kezdetleges adatokként, amíg a projekt elindul és valós adatok is kerülnek az adatbázisba.

Ezt egy csomag telepítésével használhatjuk.
 - ha a **Symfony Flex** jelen van a projektben, akkor: **composer require --dev orm-fixtures**
 - különben: **composer require --dev doctrine/doctrine-fixtures-bundle**

```php
// src/DataFixtures/BookFixtures.php
namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // létrehoz 10 könyvet
        for ($i = 0; $i < 20; $i++) {
            $book = new Book();
            $book->setTitle('Book '.$i);
            $book->setAuthor('Author '.$i);
            $book->setCreatedDate(new Date());
            $manager->persist($book);
        }

        $manager->flush();
    }
}
```

A Symfony biztosít egy **Symfony CLI** commandline eszközt is, egy **symfony** binaryt, ami opcionális, viszont megkönnyíti a lokális fejlesztést. Így betölthetjük a feljebb definiált fixture-eket az adatbázisba:

**symfony console doctrine:fixtures:load**

A symfony binary használható még kódgenerálásra is, Controllerek, Repositoryk, Entityk generálásához. Ehhez fel kell telepítenünk a csomagot: **composer require --dev symfony/maker-bundle**

Például egy Entity létrehozása:

```bash
$ php bin/console make:entity

Class name of the entity to create or update:
> Book

New property name (press <return> to stop adding fields):
> title

Field type (enter ? to see all types) [string]:
> string

Field length [255]:
> 255

Can this field be null in the database (nullable) (yes/no) [no]:
> no

New property name (press <return> to stop adding fields):
> createdDate

Field type (enter ? to see all types) [string]:
> DateTime

Can this field be null in the database (nullable) (yes/no) [no]:
> no

New property name (press <return> to stop adding fields):
>
(press enter again to finish)
```

**EntityManager** használata:

```php
# src/Repository/BookRepository.php

class BookRepository extends ServiceEntityRepository
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
        $this->entityManager = $registry->getManager();
    }

    public function create(Book $book): ?int {
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book->getId();
    }

    public function delete(Book $book): void
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }
}
```

Entitások lekérést biztosítja már a Repository osztály is:

```php
# src/Service/BookService.php

public function findOneById(int $id): BookDetailsDto
{
    $book = $this->bookRepository->findOneBy(['id' => $id]);
    if(!$book) {
        throw new NotFoundHttpException("Book not found with ID: $id");
    }

    $bookDetailsDto = $this->bookMapper->mapBookToBookDetailsDto($book);
    return $bookDetailsDto;
}
```

Book entitás:

```php
# src/Entity/Book.php

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(length: 16)]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdDate = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    # getterek/setterek ...
}
```

Commandline-ból a **symfony console make:migration** paranccsal új veriójú migrációt hozhatunk létre, ha változtattunk az Entitások mezőin, újat hoztunk létre vagy esetleg ha töröltünk egyet.

Ez a **migrations/** könyvtárba menti a migrációs file-okat .php kiterjesztéssel, hozzáfűzve a file nevéhez a migráció létrejöttének idejét:

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generált Migráció
 */
final class Version20240529183312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE book (
          id INT AUTO_INCREMENT NOT NULL,
          title VARCHAR(255) NOT NULL,
          author VARCHAR(255) NOT NULL,
          isbn VARCHAR(16) NOT NULL,
          created_date DATETIME NOT NULL,
          description VARCHAR(255) NOT NULL,
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE book');
    }
}
```

A legújabb migrációt a következő paranccsal alkalmazhatjuk az adatbázisra:

**symfony console doctrine:migrations:migrate**

Ennek megadhatunk verziót is, ha nem a legrégebbit szeretnénk alkalmazni.


