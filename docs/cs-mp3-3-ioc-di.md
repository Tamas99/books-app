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
