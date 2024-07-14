# Persistence

Symfony ORM keretrendszerek:

-   **Doctrine ORM** - legjobban elterjedt
-   **Eloquent ORM** - inkább Laravelben használjál
-   **Propel ORM**

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
        url: "%env(resolve:DATABASE_URL)%"

        # adatbázis szerver verziója itt, vagy a .env file-ban
        server_version: "8.0.32"

        profiling_collect_backtrace: "%kernel.debug%"
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
                dir: "%kernel.project_dir%/src/Entity"
                prefix: 'App\Entity'
                alias: App
        controller_resolver:
            auto_mapping: true

# felülírja az alapértelmezett konfigurációt, ha
# test környezet van beállítva
when@test:
    doctrine:
        dbal:
            dbname_suffix: "_test%env(default::TEST_TOKEN)%"
```

A Doctrine úgynevezett DataFixture-eket használ az adatbázis feltöltésére dummy adattal. Ezeket tesztelésre lehet használni vagy kezdetleges adatokként, amíg a projekt elindul és valós adatok is kerülnek az adatbázisba.

Ezt egy csomag telepítésével használhatjuk.

-   ha a **Symfony Flex** jelen van a projektben, akkor: **composer require --dev orm-fixtures**
-   különben: **composer require --dev doctrine/doctrine-fixtures-bundle**

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

Commandline-ból a **symfony console make:migration** paranccsal új verziójú migrációt hozhatunk létre, ha változtattunk az Entitások mezőin, újat hoztunk létre vagy esetleg ha töröltünk egyet.

Ez a **migrations/** könyvtárba menti a migrációs file-okat .php kiterjesztéssel, hozzáfűzve a file nevéhez a migráció létrejöttének idejét:

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Automatikusan generált Migráció
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

A **Doctrine/ORM** csomag is tartalmaz attribútumokat, amivel az entitást jobban testreszabhatjuk:

-   #[Table]:
    -   name
    -   schema
-   #[Column]:
    -   name
    -   unique
    -   type
    -   length
    -   precision
    -   scale

## Automatikus ID generálás

```php
#[ORM\Id]
#[ORM\GeneratedValue(strategy: 'AUTO')]
#[ORM\Column]
private ?int $id = null;
```

A GeneratedValue különböző stratégiákat alkalmazhat az inkrementáláshoz:

-   AUTO (alapértelmezett) - az éppen használt adatbázis platform stratégiáját fogja alkalmazni a Doctrine
-   SEQUENCE - PostgreSQL és Oracle esetén. Teljes hordozhatóság
-   IDENTITY - speciális identity oszlopokat használ a Doctrine az érték kigeneráláshoz, amikor egy sor bekerül az adatbázisba. Nincs teljes hordozhatóság
-   SEQUENCE - adatbázis szekvenciát használ a Doctrine az ID kigenerálásához. Nincs teljes hordozhatóság
-   NONE - Nem generál automatikusan. Az értékek hozzá vannak rendelve és ki vannak generálva már a kódban
-   CUSTOM - Saját osztályt adhatunk meg az azonosító kigeneráláshoz

## Sequence Generator

```php
<?php

class Message
{
    #[Id]
    #[GeneratedValue(strategy: 'SEQUENCE')]
    #[SequenceGenerator(sequenceName: 'message_seq', initialValue: 1, allocationSize: 100)]
    protected int|null $id = null;
    // ...
}
```

## DQL - Doctrine Query Language

Hasonlóan a JPQL-hez, a DQL is egy lekérdező nyelv, amit a Doctrine ORM használ PHP-ban. Lehetővé teszi az adatbázis lekérdezést objektum-orientált formában, használva az entitás objektumokat.

-   Hasonló az SQL-hez, csak itt a PHP objektumokkal és azok tulajdonságaival dolgozunk
-   A lekérdezések az entitások és azok kapcsolataik alapján vannak megírva, nem az adatbázisban levő táblák alapján
-   A DQL elfedi az alatta levő adatbázist, így adatbázis független lesz a kód

## A Query osztály

-   getResult() - egy objektumokkal teli halmazt térít vissza
-   getArrayResult() - egy read-only tömb gráfot térít vissza
-   getScalarResult() - egy skalár értékekből álló halmazt ad vissza, ami tartalmazhat duplikátumokat is
-   getSingleScalarResult() - egyetlen skalár értéket térít vissza. Ha a visszatérített adat nem csak egy értéket tartalmaz, **NonUniqueResultException** kivételt dob
-   getSingleResult() - egyetlen objektumot térít vissza. Ha több mint egy objektum kerül vissza az eredményben, **NonUniqueResultException** kivételt dob. Ha nem tartalmaz egyetlen objektumot sem, **NoResultException** kivételt dob
-   setParameter($param, $value) - a lekérdezés paraméterezhető, így megadhatunk egy számot, hogy hányadik paraméternek az értéke vagy a paraméter nevét is String-ként

```php
<?php

$query = $em->createQuery('SELECT b FROM Book b WHERE b.title = :title');
$query->setParameter('title', 'Title1');
$books = $query->getResult(); // tömb Book típusú objektumokkal
```

DQL lekérdezésben létrehozhatunk DTO-kat direkt módon a **NEW** operátorral.

Létrehozzuk az osztályt:

```php
<?php

class CustomerDTO
{
    public function __construct($name, $email, $city, $value = null)
    {
        //...
    }
}
```

**NEW** operátor:

```php
<?php

$query = $em->createQuery('SELECT NEW CustomerDTO(c.name, e.email, a.city) FROM Customer c JOIN c.email e JOIN c.address a');
$users = $query->getResult(); // CustomerDTO-kat tartalmazó tömb
```

A Doctrine lehetőséget ad lekérdezések cache-elésére is. Így ennek eredménye el lesz mentve az első végrehajtáskor és az azutáni hívásokkor az eredmény már az cache-elt helyről térül vissza, ami produkciós környezetben ajánlott stratégia.

Result cache - Elmentjük a lekérdezés eredményét:

```php
<?php

$cache = new \Symfony\Component\Cache\Adapter\PhpFilesAdapter(
    'doctrine_results',
    0,
    '/path/to/writable/directory'
);
$config = new \Doctrine\ORM\Configuration();
$config->setResultCache($cache);
```

Ezután konfigurálhatjuk, hogy a result cache-t használja:

```php
<?php

$query = $em->createQuery('select u from \Entities\User u');
$query->enableResultCache();
```

## Öröklődés

A Doctrine is támogatja az entitások közötti származtatási kapcsolatot. Hasonló módon a JPA-hoz, itt is tudunk egy osztályt **#[MappedSuperclass]** kulcsszóval annotálni, hogy információt tartalmazzon a származtatott al-osztályoknak, ha önmaga nem egy valódi entitás.

```php
<?php

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\Entity;

#[MappedSuperclass]
class Person
{
    #[Column(type: 'integer')]
    protected int $mapped1;
    #[Column(type: 'string')]
    protected string $mapped2;
    #[OneToOne(targetEntity: Toothbrush::class)]
    #[JoinColumn(name: 'toothbrush_id', referencedColumnName: 'id')]
    protected Toothbrush|null $toothbrush = null;

    // ...
}

#[Entity]
class Employee extends Person
{
    #[Id, Column(type: 'integer')]
    private int|null $id = null;
    #[Column(type: 'string')]
    private string $name;

    // ...
}

#[Entity]
class Toothbrush
{
    #[Id, Column(type: 'integer')]
    private int|null $id = null;

    // ...
}
```

A következő DDL-re fordul le az fentebb ábrázolt adatbázis schema (SQLite-ban):

```sql
CREATE TABLE Employee (mapped1 INTEGER NOT NULL, mapped2 TEXT NOT NULL, id INTEGER NOT NULL, name TEXT NOT NULL, toothbrush_id INTEGER DEFAULT NULL, PRIMARY KEY(id))
```

A gyökér entity osztálynak tartalmaznia kell a következő annotációkat:

-   **#[InheritanceType]** - két származtatási stratégiából lehet választani
-   **#[DiscriminatorColumn]** - egy extra oszlop, ami információt tartalmaz a hierarchia típúsáról
-   **#[DiscriminatorMap]** - lehetséges értékek a megkülönböztető oszlopnak

## Single Table Inheritance

Egy származtatási stratégia, ahol minden osztálynak a hierarchiában egyetlen adatbázis tábla lesz készítve.

-   Egyszerű implementálni, bár nagyobb kitelepítéseknél nagy impaktja lehet az indexelésre
-   Lekérdezéseknél nem szükséges a **JOIN**, csak megadjuk a feltételeket a **WHERE** ágban

```php
<?php
namespace MyProject\Model;

#[Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap(['person' => Person::class, 'employee' => Employee::class])]
class Person
{
    // ...
}

#[Entity]
class Employee extends Person
{
    // ...
}
```

## Class Table Inheritance

Egy másik származtatási stratégia, ahol minden osztálynak a hierarchiában egy-egy tábla van létrehozva. Egy tábla úgy kapcsolódik a szülő táblájához, hogy **Foreign Key** segítségével mutat rá.

-   Tervezésnél a legnagyobb flexibilitást nyújtja ez a stratégia, mivel a változásokat egy típus esetén, csak a neki megfelelő táblában kell elvégezni
-   Több JOIN műveletre van szükség a lekérdezéseknél, ami nagyban lassíthatja a teljesítményt, ha nagy táblákról van szó

```php
<?php
namespace MyProject\Model;

#[Entity]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap(['person' => Person::class, 'employee' => Employee::class])]
class Person
{
    // ...
}

#[Entity]
class Employee extends Person
{
    // ...
}
```

## Entitások közötti kapcsolatok

Az entitások közötti kapcsolatok jelölésére a Doctrine is ad lehetőséget a megszokott annotációkkal.

Ont-To-One

```php
<?php
#[Entity]
class Product
{
    // ...

    /** Egy termék tartalmaz egy szállítást. */
    #[OneToOne(targetEntity: Shipment::class)]
    #[JoinColumn(name: 'shipment_id', referencedColumnName: 'id')]
    private Shipment|null $shipment = null;

}

#[Entity]
class Shipment
{
    // ...
}
```

One-To-Many

```php
<?php
#[Entity]
class Product
{
    // ...

    /**
     * Egy terméknek sok funkciója van. Ez az inverse side.
     * @var Collection<int, Feature>
     */
    #[OneToMany(targetEntity: Feature::class, mappedBy: 'product')]
    private Collection $features;
    // ...

    public function __construct() {
        $this->features = new ArrayCollection();
    }
}
```

Many-To-One

```php
<?php
#[Entity]
class Feature
{
    // ...
    /** Sok funkciónak van egy terméke. Ez az owning side. */
    #[ManyToOne(targetEntity: Product::class, inversedBy: 'features')]
    #[JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    private Product|null $product = null;
    // ...
}
```

Many-To-Many

```php
<?php
#[Entity]
class User
{
    // ...

    /**
     * Sok Usernek van sok Groupja.
     * @var Collection<int, Group>
     */
    #[ManyToMany(targetEntity: Group::class, inversedBy: 'users')]
    #[JoinTable(name: 'users_groups')]
    private Collection $groups;

    public function __construct() {
        $this->groups = new ArrayCollection();
    }

    // ...
}

#[Entity]
class Group
{
    // ...
    /**
     * Sok Groupnak van sok Usere.
     * @var Collection<int, User>
     */
    #[ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    private Collection $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }

    // ...
}
```
