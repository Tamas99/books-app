# Inversion of Control

## Symfony

A Symfony szintén egy népszerű, nyílt forráskódú PHP keretrendszer, amely enterprise webalkalmazások fejlesztésére alkalmas.
 - Teljes értékű keretrendszer, amely mindent biztosít, amire szüksége van egy modern webalkalmazás felépítéséhez legyen az routing, validálás és template-ek
 - Modulárisan felépített, így csak azokat a modulokat kell használnia, amelyekre az alkalmazásának szüksége van
 - A Symfony parancssori felületet (CLI) is kínál, amellyel feladatokat automatizálhat és a keretrendszert konfigurálhatja
 - A Symfony is segítségünkre szolgál a reflection/annotáció-feldolgozásával
 - Támogatja a többrétegű architektúrát
 - Adatelérési réteg generálása (Doctrine segítéségvel)

A Symfony lokális Web Server segítségével könnyen fejleszthetünk lokálisan webalkalmazást anélkül, hogy valamilyen más, külső web servert használjunk, mint az Apache vagy Nginx. Azonban ez a web server nem alkalmas produkciós környezetre és csak lokális fejlesztésre ajánlott.

Kezdetleges Symfony Bundle-ok:
 - **Symfony Flex** - egy alap csomag, megkönnyíti más bundle-ok telepítését, alapértelmezett beállításokat ad ezeknek
 - **Symfony Security Bundle** - user authentication és authorization beállítás, több autentikációs mechanizmus, RBAC autorizációs stratégia stb.
 - **Doctrine Bundle** - Doctrine adatbázis ORM, migráció generálás, Entityk létrehozása, relációk kialakítása

# Dependency Injection

A PHP esetében a legelterjedtebb DI keretrendszerek a **Symfony** és a **Laravel**.

Symfony-ban a **Dependency Injection** a **Service Container** segítségével van megvalósítva. Ez kezeli a service-ek és ezek függőségeinek a példányosítását. Symfonyban, ha használni akarunk egy osztályt más osztályokban, service-ként kell definiálnunk.

## Service definiálása

A service-ek Symfonyban konfigurációs fájlokban vannak definiálva (**services.yaml**). Megadhatjuk, hogy milyen osztályok legyenek service-ként beköthetőek:

```yaml
# config/services.yaml
services:
    # alapértelmezett konfigurációk
    _defaults:
        autowire: true      # Automatikus függőség injektálás
        autoconfigure: true # Automatikus service regisztrálás, mint parancsok, esemény feliratkozók stb.

    # az src/ könyvtáron belüli osztályokat lehetővé teszi, hogy service-ként használjuk
    # ez osztályonként létrehoz egy service-t, aminek az ID-ja a fully-qualified osztálynév lesz
    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'

    # fontos a sorrend, mert egy service definiálás felülír egy korábban definiált service-t
```

A YAML-n kívül megadhatunk még XML vagy PHP konfigurációs fájlokat is.

Ha nem akarunk automatikus függőség injektálást használni, kézzel is beköthetjük a függőségeket egy-egy osztálynak [explicit konfigurációt használva](https://symfony.com/doc/current/service_container.html#services-explicitly-configure-wire-services).

## Példa

```php
// src/Service/DependencyService.php
namespace App\Service;

use Psr\Log\LoggerInterface;

class DependencyService
{
    // type-hint segítségével injektáljuk a függőséget
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function dependencyMethod()
    {
        $this->logger->info('This is the DependencyService.');
    }
}
```

```php
// src/Service/MainService.php
namespace App\Service;

use Psr\Log\LoggerInterface;

class MainService
{
    public function __construct(
        private LoggerInterface $logger,
        private DependencyService $dependencyService,
    ) {
    }

    public function mainMethod()
    {
        $this->logger->info('Calling the DependencyService.');
        $this->dependencyService->dependencyMethod();
    }
}
```

Az **#[Autowire()]** paraméter attribútummal beállíthatjuk az alapértelmezett implementációját egy adott függőségnek, ha az többel is rendelkezik.

```php
public function __construct(
    #[Autowire(service: 'monolog.logger.request')]
    private LoggerInterface $logger,
) {
    // ...
}
```

A **#[Required]** attribútummal megadhatjuk, hogy milyen metódusokat hívjon meg az autowiring a függőségek bekötése alatt.

## Application Kernel

Az **Application Kernel** Symfonyban az alkalmazás központi eleme. Ez a belépési pontja az alkalmazásnak, kezeli a service container-t, a kérések élettartamát.

 - Elindítja az alkalmazást a megfelelő környezettel
 - Betölti a konfigurációs file-okat
 - Felépíti a service container-t, ami a service-eket kezeli az alkalmazásban
 - Kezeli a függőségek bekötését
 - Kezeli a bejövő HTTP kéréseket és továbbítja a megfelelő controllernek

Symfonyban alapértelmezetten minden service **Singleton**. Ezt felülírhatjuk, hogy új példány jöjjön létre minden injekciós ponton, ha a service **Prototype Service** lesz. Ezt a **services.yaml** konfigurációs file-ban a **shared** attribútummal állíthatjuk be.

```yaml
# config/services.yaml
services:
    App\Service\DependencyService:
        shared: false
```

## Symfony környezet

A Symfony alapértelmezetten 3 környezettel kezdődik:

 - **dev**
 - **prod**
 - **test**

Az applikáció működését módosíthatjuk egyetlen konfigurációs file-ból is megadva, hogy melyik környezetben, hogyan viselkedjen a **when** kulcsszóval:

```yaml
# config/packages/webpack_encore.yaml
webpack_encore:
    # ...
    output_path: '%kernel.project_dir%/public/build'
    strict_mode: true
    cache: false

# cache csak prod környezetben aktív
when@prod:
    webpack_encore:
        cache: true

# strict mode kikapcsolása test környezetben
when@test:
    webpack_encore:
        strict_mode: false
```

A Symfony a projekt gyökér könyvtárában keresi a **.env** file-t, amiben a változók definiálva vannak. Aktív környezetet úgy adhatunk meg, ha ebben a file-ban az **APP_ENV** változót beállítjuk.

```ini
# .env (vagy .env.local)
APP_ENV=prod
```

A .env file minden bejövő kérésnél be lesz olvasva és feldolgozza a Symfony, tehát nem kell cache-t üríteni vagy a PHP containert újraindítani akkor sem, ha Dockert használunk.

A .env file veriókövetve van és a projekt repositoryban a helye. Ezt a file-t lokálisan felülírhatjuk egy **.env.local** file-lal, ami viszont csak a saját gépünkön van jelen, nem töltjük fel a kóddal együtt és szenzitív információkat tartalmazhat a projekt működésétől függően.

**.env.test** szintén felülírja a .env file-t, de csak a test környezetre és ez is verziókövetve kell legyen a git által. Saját gépen ezt is felülírhatjuk **.env.test.local** file-lal. Hasonló módon más környezetre is érvényesek ezek a szabályok követve a **.env.\<environment>**, illetve **.env.\<environment>.local** mintát.
