<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\SpeakerFactory;
use App\Factory\TalkFactory;
use Zenstruck\Foundry\Story;

final class DefaultTalksStory extends Story
{
    public function build(): void
    {
        TalkFactory::new()
            ->withTitle('Le Zéro Downtime Deployment en pratique')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Smaïne', 'lastName' => 'MILIANNI']))
            ->withDescription(<<<'TEXT'
                Le Zéro Downtime Deployment appelé communémant ZDD est une pratique permettant de faire des mises en production sans interruption du système ni de maintenance programmée, finis les MEP à 7h00 du matin ou durant la pause déjeuner !

                Chez Yousign nous appliquons le ZDD, la mise en place du ZDD nous permet de déployer tous les jours sereinement sans interruption et à n'importe quel moment de la journée.
                
                Dans ce talk je vous propose un retour d'expérience sur cette pratique, les cas pratiques et les pièges à éviter afin de pouvoir mettre en production sereinement.
                
                Que vous changiez votre schéma de base de données, modifier vos consumer asynchronoes ou vos contrats d'APIs, je vous propose de voir comment nous faisons concrètement côté code applicatif pour déployer à toute heure sans casser la prod.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Webhooks at scale')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Fabien', 'lastName' => 'PAITRY']))
            ->withDescription(<<<'TEXT'
                L'API Yousign utilise les webhooks pour notifier les app clientes des changement d'état sur les traitements asynchrones. Avec la forte croissance du trafic sur l'API nous avons été confrontés à un certain nombre de difficultés qui nous ont conduits à revoir en profondeur notre système de notifications. Au cours de ce rapide retour d'experience nous évoquerons les problématiques techniques liées aux webhooks ainsi que les solutions que nous avons mises en place pour garantir leur délivrabilité.

                Spoiler alert, on parlera, entre autre, de RabbitMQ et d´implémentation du pattern circuit breaker en PHP
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Créer sa Malware Sandbox en PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Alexandre', 'lastName' => 'DAUBOIS']))
            ->withDescription(<<<'TEXT'
                La sécurité est, plus que jamais, au centre de toutes les problématiques en informatique. Peu importe si on parle de web, d’app mobiles ou de logiciels pour ordinateur, la sécurité revient sans cesse au centre de la table.

                Mais allons droit au but : qui effectue des vérifications poussées et avancées sur les fichiers reçues dans son application PHP ? On ne parle pas d’une « simple » vérification de MIME Type bien sûr. Êtes-vous protégés d’un code malicieux, d’injections de commandes ou encore des terrible « tarbombs » qui feront les heures sombres de vos systèmes de fichier ? Êtes-vous familier avec les magic bytes ?

                Sans aller jusqu’à créer un véritable antivirus, beaucoup de choses peuvent être faites PHP (mais pas que !) pour isoler les potentiels malware, les analyser et traiter les fichiers en toute sécurité en créant sa propre Malware Sandbox. Bienvenue dans la cybersécurité !
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Covariance, Contravariance et Diamant')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Alexandre', 'lastName' => 'DAUBOIS']))
            ->withDescription(<<<'TEXT'
                Derrière ce titre et ces mots avec beaucoup de syllabes se trouvent de la théorie de la programmation objet. On est d’accord, ça peut paraître barbant à première vue. Hors, quand vous comprenez ces outils, vous comprenez les comportements de PHP. Et de tous les langages de programmation orientés objet !

                Pourquoi est-il possible d’étendre le type d’argument d’une méthode d’une classe fille, mais qu’il est interdit d’étendre son type de retour ? Est-ce juste pour le plaisir ou la théorie objet rend-elle ceci impossible ? Petit indice : Barbara Liskov, informaticienne émérite, y est pour quelque chose.
                
                On se donne rendez-vous pour vous rendre imbattable lundi à la machine à café sur les histoires d’héritage, de problème du diamant, de covariance et de contravariance !
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('PHP/Parallel : Accélérer sensiblement ses temps d\'exécution')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Thierry', 'lastName' => 'KAUFFMANN']))
            ->withDescription(<<<'TEXT'
                Sponsorisé par le Ministère de l'Éducation Nationale depuis 2020, le développement de l'application DiViM-S a représenté un défi technique : contrôler un pool de 150 serveurs de visio-conférence BigBlueButton pour adapter automatiquement le nombre de serveurs actifs au nombre de participants et de salles. Afin d'atteindre une efficacité maximale de l'application et de rendre possibles des temps de réactivité de quelques secondes le programme fait appel massivement au parallélisme disponible nativement dans PHP depuis la version 7.2 avec l'extension PHP/Parallel.

                Sont ainsi parallélisés aussi bien les appels à l'API de l'hébergeur (Scaleway) que les sondages des serveurs BigBlueButton.
                
                La conférence présentera tout d'abord les attentes fonctionnelles et les réalisations pratiques du projet, puis l'architecture de l'application utilisant le moteur PHP sous Docker, ensuite définira ce que l'on entend par "parallélisme" et décrira enfin, en détail, comment ont été implémentées les fonctionnalités de parallélisme natives de PHP.
                
                    Lien de l'application sur GitHub : https://github.com/arawa/divims
                    Documentation de PHP/Parallel : https://www.php.net/manual/fr/book.parallel.php
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Utiliser un framework pour écrire de la documentation ?')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Martin', 'lastName' => 'SUPIOT']))
            ->withDescription(<<<'TEXT'
                Et si il suffisait d'un peu de méthode et d'organisation pour que la doc devienne agréable à utiliser mais aussi à écrire ?

                Au travers d'exemples culinaires simples à appréhender, nous verrons comment organiser sa documentation, comment mettre les auteurs en confiance, et comment écrire une documentation qui sera lue !
                
                Le framework Diátaxis est encore peu connu mais il a déjà fait ses preuves chez de grands éditeurs de logiciels et j'ai eu l'occasion de le mettre en place dans une équipe technique de 60 personnes. Nous nous appuierons sur cette expérience pour montrer que la doc, ce n'est pas qu'une suite de mots et qu'on peut lui donner du sens.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('PHP: Particularly Horrible Programs')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Gina', 'lastName' => 'BANYARD']))
            ->withDescription(<<<'TEXT'
                PHP est votre outil de prédilection, mais est-ce-que vous connaissez vraiment PHP ?

                Dans ce talk ludique et bon enfant on explorera les bizarreries du langage, des comportements incompréhensibles, et des choses techniquement faisables mais peu recommandables qui auront le don de vous surprendre.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Une Monade, simplement, c\'est quoi ?')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Gina', 'lastName' => 'BANYARD']))
            ->withDescription(<<<'TEXT'
                Monade est un terme qui nous vient de la programmation fonctionnelle. Certaines personnes trouvent les monades comme la meilleure solution pour la gestion d'erreur. Mais qu'est ce que c'est une monade exactement ? Et si je vous disais que vous vous en êtes déjà servi sans le savoir ?

                Dans cette conférence on verra quelques principes de la programmation fonctionnelle qui expliquent l'origine des monades, et une multitudes d'exemples pour démystifier ce terme.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Écrire du Php avec seulement 6 caractères')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Benoit', 'lastName' => 'VIGUIER']))
            ->withDescription(<<<'TEXT'
                Mais pourquoi ? Pourquoi donc voudrions nous écrire un programme avec une contrainte aussi absurde ?? Parce que l’important n’est pas la destination, mais le voyage… De même qu’une solution de Code Golf ou de Quine n’a pas pour vocation d’être mise en production, ce challenge est un prétexte pour explorer quelques subtilités de PHP. Idée géniale pour l’obfuscation? Hérésie à ne jamais montrer aux juniors? Accrochez vos ceintures, et venez vous faire une opinion avec ce talk (très) technique et (presque) philosophique !
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Démystifier l\'IA : Un guide pratique pour les devs PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Iana', 'lastName' => 'IATSUN']))
            ->withDescription(<<<'TEXT'
                L'intelligence artificielle est en train de transformer en profondeur le paysage professionnel. Si son impact est déjà considérable dans le domaine du traitement des données, les progrès fulgurants réalisés ces dernières années laissent entrevoir une transformation de tous les secteurs d'activité.

                Dans cette présentation, j'ai pour objectif de démystifier l'IA. Je vous guiderai à travers les concepts fondamentaux de cette technologie, en explorant ses capacités et ses limites dans le contexte du développement informatique. Mon ambition est de fournir aux devs PHP les connaissances et les outils nécessaires pour se familiariser avec l'IA et l'intégrer efficacement dans leurs projets.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('De "0" à "tracing distribué" en quelques lignes de code, c\'est possible !')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Vincent', 'lastName' => 'LEPOT']))
            ->withDescription(<<<'TEXT'
                La télémétrie n'est pas quelque chose de nouveau, et souvent on connaît très bien le log et souvent assez bien la métrique. Mais il y a un troisième larron que l'on utilise peu et qui permet d'obtenir des informations précieuses sur le bon fonctionnement (ou pas) de ses applications : la trace.

                L'objectif de ce talk est de vous montrer comment mettre en place rapidement des traces dans vos applications PHP avec OpenTelemetry afin de pouvoir plus facilement débugguer vos applications et comprendre ce qui les rend parfois un peu lentes. Le tout en production !

                À partir d'un exemple simple d'architecture distribuée qui comporte des problèmes, nous allons instrumenter ensemble notre code en quelques minutes et trouver facilement les soucis et les régler.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Comment déboguer Xdebug... ou n\'importe quel autre bug bizarre en PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Kévin', 'lastName' => 'DUNGLAS']))
            ->withDescription(<<<'TEXT'
                Que faire quand c'est le debugger qui bug ? C'est la question à laquelle j'ai dû répondre lorsque mes notifications GitHub se sont affolées car une nouvelle version de Xdebug faisait planter FrankenPHP et donc tous les projets qui l'utilisent.

                Ensemble, nous retracerons l'histoire épique de ce déboggage de debugger et découvrirons une méthode qui permet de débogguer à peu près n'importe quel bug :
                
                    isoler le problème
                    coder un "reproducteur minimal"
                    installer un environnement de développement permettant de débogguer PHP et ses extensions
                    utiliser les bons outils pour comprendre ce qui se passe
                    faire un rapport de bug détaillé
                    établir une théorie
                    écrire un correctif
                    contribuer le correctif au projet "upstream"
                
                Cette méthode est applicable pour déboguer n'importe quel problème, dans n'importe quel langage ! Nous profiterons de cette aventure pour découvrir le fonctionnement interne du moteur d’exécution PHP, et des extensions ainsi que quelques outils de déboggage, en particulier GDB.
                
                Pour information, avoir des notions de programmation C est un plus mais n'est pas indispensable.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Utiliser une faille de la glibc pour attaquer le moteur PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Charles', 'lastName' => 'FOL']))
            ->withDescription(<<<'TEXT'
                Cette conférence décrit la découverte, l'impact et l'exploitation de la vulnérabilité CVE-2024-2961, publiée par LEXFO il y a quelques mois. Ici, pas d'injection SQL, de Cross-Site Scripting ou d'injection de commande : on attaque directement l'interpréteur du langage ! En effet, cette faille de dépassement de tampon (buffer overflow), située dans la GLIBC, la librairie standard utilisée par tous les programmes Linux, offre la possibilité de corrompre l'allocateur PHP, et ainsi prendre le contrôle d'un serveur à distance.

                À travers l'exploitation de cette vulnérabilité, de deux façons différentes (via les failles de lecture de fichier, et les appels directs à la fonction vulnérable), cette conférence vous donnera un aperçu du fonctionnement interne de PHP, son allocateur, et ses structures principales (zval, zend_string, etc.), et comment un hacker peut compromettre l'interpréteur du langage, à même son coeur.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Dis Siri, mets des éléPHPants dans ma domotique')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'David', 'lastName' => 'BUROS']))
            ->withDescription(<<<'TEXT'
                En 2022, après quelques années à tâtonner et à bricoler des trucs jamais finis, je décide de me lancer un peu plus sérieusement dans la domotisation de ma maison. Malgré des débuts prometteurs, je me rends compte que des interfaces manquent pour réaliser mes rêves les plus fous (ou les plus inutiles suivant les points de vue). Me vient alors l'idée saugrenue, mais "pragmatique", de coder en PHP les outils manquants. C'est le début d'un parcours aussi enrichissant que semé d'embûches.

                Ce talk, sous forme d'un retour d'expérience, parlera entre autres d'assistants vocaux, de banque en ligne, de domotique Open Source, de librairies PHP custom et de bétonnière. Alors, si l'un ou l'autre de ces sujets vous intrigue ou vous intéresse, je vous invite à me suivre pour du craftsmanship vachement craft.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Une gestion dynamique des permissions en PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Marion', 'lastName' => 'AGÉ']))
            ->withDescription(<<<'TEXT'
                Si vous avez une application accueillant différents types d'utilisateurs, vous vous êtes déjà probablement questionné sur la meilleure façon de gérer des permissions dynamiques et administrables, en fonction de différents critères du métier, pour lesquels les rôles ne sont pas suffisants.

                Ce talk explore la mise en place d'un système de gestion des permissions dynamique en PHP, garantissant à la fois sécurité, robustesse et performances. Nous aborderons les méthodes pour modéliser, définir et stocker les permissions, ainsi que les techniques pour assurer la validité de ces permissions à chaque requête sans alourdir votre système.

                Nous verrons également comment transmettre ces informations au frontend pour qu'il puisse adapter l'affichage sans rechargement (menu, boutons, ou informations à afficher) en fonction des droits de l'utilisateur connecté, même lorsque ces droits évoluent.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('GitHub Copilot : une intégration sans turbulences dans notre DSI')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Audrey', 'lastName' => 'BROUARD']))
            ->withDescription(<<<'TEXT'
                Vous vous demandez comment intégrer GitHub Copilot dans votre DSI ? Dans un monde où l'IA redéfinit la programmation, comment anticiper son accueil ? Allez-vous rencontrer des réticences ? Si vous vous posez des questions sur les possibles freins, les craintes, l'intérêt des devs, le ROI financier et non financier (Developer Happiness !), voici notre approche "sans prise de tête". Après un an d'exploration chez Harmonie Mutuelle, découvrez notre démarche pour une intégration fluide. Prêt•e à décoller ?
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('L\'Orienté Objet au Cœur du Templating Symfony : Découvrez TwigComponent et LiveComponent')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Felix', 'lastName' => 'EYMONOT']))
            ->withDescription(<<<'TEXT'
                Plongez dans l'univers de TwigComponent et LiveComponent pour découvrir comment ces outils peuvent simplifier et dynamiser les interfaces de vos projets Symfony. En adoptant une approche orientée objet, ces packages offrent une structure claire et réutilisable pour vos templates, tout en intégrant des fonctionnalités interactives et réactives. Que vous débutiez ou que vous cherchiez à approfondir vos connaissances, venez explorer des solutions innovantes pour des interfaces utilisateur plus performantes et agréables à utiliser.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('L’inévitable chemin vers le DevGreenOPs')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Alexandre', 'lastName' => 'MORGAUT']))
            ->withDescription(<<<'TEXT'
                Des bonnes pratiques, de nombreux outils, et des incitations légales grandissantes. Aussi évoqué sous le nom de DevSusOPs, pour « Sustainability IT » ou Numérique Responsable en français, ce domaine était initialement principalement pris en compte sous l’angle de l’aspect financier ou des gains de performances, mais comme pour le RGPD et le RGAA, le RGESN avec la loi REEN, ainsi que le CSRD européen et les recommandation W3C nous incitent à prendre les choses en main. Des Linters aux outils de mesures d’impact carbone, découvrez comment être efficaces de votre IDE à votre production.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Optimiser les performances de votre API avec AutoMapper')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Joël', 'lastName' => 'WURTZ']))
            ->withDescription(<<<'TEXT'
                AutoMapper est un outil permettant de mapper des données entre 2 représentations différentes, que ce soit depuis des objets vers d'autres objets, ou des objets vers des structures génériques comme un tableau PHP.

                Cet outil a toujours été pensé dans le but d'offrir des performances inégalées.

                Au sein d'une application développée avec API Platform, nous avons remplacé une partie de la sérialisation avec cet outil, ce qui nous a permis de considérablement augmenter les performances, même sur de simple appels, et ainsi réduire la charge sur nos serveurs.

                Au cours de cette conférence nous reviendrons sur cette librairie et comment la mettre en place sur un projet existant via notre retour d'expérience.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Gérer des gros volumes de données avec PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Joël', 'lastName' => 'WURTZ']))
            ->withDescription(<<<'TEXT'
                Importer ou exporter un fichier CSV, synchroniser des gros volumes de données, ... Toutes ces tâches sont apparues au moins une fois au cours de nos différents projets.
                
                Alors bien sûr, on pense tout de suite à faire des workers, des traitements asynchrones, mais dans certains cas ce n'est pas souhaitable ou envisageable.
                
                Comment faire dans ces cas-là ? Nous verrons ensemble plusieurs bonnes pratiques et astuces pour réaliser ce genre de tâches sans trop casser nos serveurs.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Plongée au coeur des Enums')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Julien', 'lastName' => 'MERCIER-ROJAS']))
            ->withDescription(<<<'TEXT'
                Arrivé avec PHP 8.1, les Enums sont un outil puissant mais souvent sous ou mal utilisé.

                Découvrez comment utiliser efficacement ce concept pour améliorer la structure et la lisibilité de votre code.

                Petit tour sur leur fonctionnement, les bonnes et mauvaises pratiques, et leur prise en charge dans Symfony et Doctrine.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('StandAAArdisez vos tests !')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Nerea', 'lastName' => 'ENRIQUE']))
            ->withDescription(<<<'TEXT'
                Une bonne partie de notre travail consiste à lire et écrire des tests. Mais comment les unifier tout en gardant la maintenabilité et la robustesse ? Une partie de la réponse pourrait bien se trouver dans le pattern AAA !

                Le pattern AAA (Arrange, Act, Assert) est une approche qui permet de structurer l’écriture de tests unitaires. Cela peut améliorer la qualité de notre code en nous forçant à écrire des tests concis et focalisés, ce qui rend le code plus facile à comprendre et à déboguer. Cependant, comme toute approche, le pattern AAA a aussi ses défis. Par exemple, il peut être difficile d’appliquer ce pattern dans des scénarios complexes.

                Voyons comment ce pattern peut simplifier notre écriture de tests !
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('throw new Exception(); Oui mais laquelle ?!')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Olivier', 'lastName' => 'DOLBEAU']))
            ->withDescription(<<<'TEXT'
                Des exceptions, on en voit tous les jours dans notre vie de dev !

                Y'en a des bien : celles qu'on a créées nous-même avec un p'tit nom trop mignon, celles qu'on envoient aux camarades quand ils font n'importe quoi avec notre code, celle qu'on a anticipée et qu'on gère à la perfection. Et y'en des beaucoup moins bien : celles qu'on n'attendaient pas, celles qu'on ne comprends pas, celles qu'on reçoient des camarades alors qu'on a rien demandé et les pires d'entre elles, celles qui pointent le bout de leur nez en prod à 17h un vendredi soir.
                
                A moins... que ce ne soit l'inverse ?!
                
                Dans cette conférence, je me propose de vous faire un petit tour d'horizon de ce que j'estime être les bonnes pratiques à respecter quand on manipule des exceptions. :)
                
                    "On a les exceptions qu'on mérite." Tsao Leu
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Découverte de Castor : Le Task Runner PHP qui Simplifie votre Workflow')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Grégoire', 'lastName' => 'PINEAU']))
            ->withDescription(<<<'TEXT'
                Vous êtes développeur PHP et cherchez un moyen d'automatiser vos tâches répétitives ? Découvrez Castor, le task runner conçu spécifiquement pour une super DX. Dans cette session de 20 minutes, nous vous présenterons les fonctionnalités clés de Castor et comment il peut transformer votre workflow de développement.

                Nous commencerons par une introduction sur les principes de base des task runners et l'importance de l'automatisation dans les projets modernes. Ensuite, nous explorerons Castor en détail, en vous montrant comment définir, organiser et exécuter des tâches avec une syntaxe simple et intuitive. Vous verrez des exemples concrets d'utilisation de Castor pour des tâches courantes telles que le déploiement, les tests, et la génération de documentation.
                
                Que vous soyez un développeur PHP débutant ou expérimenté, cette conférence vous fournira les connaissances nécessaires pour intégrer Castor dans vos projets et gagner en efficacité. Rejoignez-nous pour découvrir comment Castor peut simplifier et optimiser vos processus de développement PHP.
                Objectifs de la session :
                
                    Introduire les concepts de base des task runners et de l'automatisation.
                    Présenter les fonctionnalités et avantages de Castor pour les projets PHP.
                    Illustrer par des exemples concrets comment utiliser Castor dans divers scénarios de développement.
                    Inspirer les développeurs à adopter Castor pour améliorer leur productivité et leurs workflows.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('La goutte d’eau qui fait déborder le Cloud')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Julien', 'lastName' => 'JOYE']))
            ->withDescription(<<<'TEXT'
                Vous déployez votre application Symfony bien architecturée en micro-services sur une infra kubernetes, trop bien !

                Mais avez-vous déjà essayé de faire tourner Drupal sur le même type d’infra ?
                
                Ça peut paraître trivial ; pourtant deux mentalités bien différentes viennent se confronter l’une à l’autre. Quand Drupal peut envoyer 800 requêtes SQL sur un cold start, ou faire transiter des téraoctets de données redis sur le réseau par mois, est-ce que k8s est toujours la solution ?
                
                Dans cette conférence, je vous fais un petit rappel de ce qu’est k8s, vous montre pourquoi Drupal (ou l’infra) peut devenir votre pire ennemi dans ce contexte, et comment éviter certains pièges.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('WordPress: Une cause perdue?')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Cyrille', 'lastName' => 'COQUARD']))
            ->withDescription(<<<'TEXT'
                WordPress est souvent vu comme une solution de seconde zone par les devs PHP qui en ont une vision erronée car datée.
                
                Dans cette conférence, je vous propose un tour d'horizon des technologies WordPress qui modernisent pour proposer aux devs un environnement moderne et confortable.
                
                Ensuite nous aborderons plus en détails le processus mis en place par le framework wp-launchpad qui permet de moderniser le développement de plugin sur WordPress. Ainsi nous verrons les differentes notions mises en place pour adapter le développemement moderne aux contraintes du développement WordPress, tout en gardant le développement le plus accessible possible à la communauté.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Un parser de PHP en PHP : le cœur des outils d\'analyse statique')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Mathieu', 'lastName' => 'NOËL']))
            ->withDescription(<<<'TEXT'
                Au cours de ses 29 ans, la communauté PHP s'est construit de nombreux outils de travail collaboratif, de qualité de code et d'automatisation.

                Lintage, analyse statique, refactoring, documentation, couverture de code...
                
                Durant ce talk, faisons ensemble le tour des grands principes des lexer, tokens, parsers et AST. Découvrez comment certains de ces outils (PHPStan, Psaml, Rector, PhpMetrics, phpDocumentor...) tirent parti du paquet nikic/PHP-Parser.
                
                Puis, au travers d'un exemple concret, nous étudierons comment employer ce paquet pour construire vos propres outils.
                
                En conclusion, nous verrons ensemble à quel point les outils utilisant ce parser orientent les choix de la core team de PHP, que le facteur bus peut être plus important que nous le croyons et quelles sont les alternatives et nouveaux projets qui visent à analyser/comprendre le code PHP rapidement.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('PMU: un plugin composer pour la gestion de Monorepository en PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Antoine', 'lastName' => 'BLUCHET']))
            ->withDescription(<<<'TEXT'
                Si vous pratiquez du Javascript vous êtes familier avec les monorepository et les outils du type Lerna, npm workspaces ou Nx. En PHP il est plus difficile et moins courant d'utiliser ces pratiques, or nous en avons souvent le besoin. Après avoir rappelé les fonctionnalités qui nous seraient bénéfiques, nous allons analyser les différents plugins disponibles aujourd'hui qui les implémente. En s'inspirant de ceux-ci nous allons construire ensemble PMU (PHP Monorepository Utility), un plugin composer qui implémente les fonctionnalités principales autour de la gestion de mono repository comme la gestion de versions, l'exécution de scripts, le lien entre chaque composant etc.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Créer des interfaces d’administration rapidement avec Symfony UX et Sylius')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Loïc', 'lastName' => 'FRÉMONT']))
            ->withDescription(<<<'TEXT'
                Dès le début, l’architecture de Sylius a toujours été composée d’un maximum de composants réutilisables. En 2013, l’interface d’administration était conçue avec Bootstrap et le composant Sylius Resource pour éviter de, toujours, créer les mêmes controllers avec énormément de codes dupliqués.

                En 2016, Pawel Jedrzejewski (Foundateur de Sylius) a introduit le composant Sylius Grid qui permet de créer des tables pour lister les données dans vos applications. Filtrage, champs Twig custom etc. ont été introduits pour obtenir un maximum de fonctionnalités.
                
                Un mois plus tard, Pawel a choisi d’utiliser Semantic UI (sic) et a eu la bonne idée de créer des interfaces de CRUD génériques.
                
                Depuis 2023, la nouvelle équipe de Sylius travaille sur une nouvelle interface d’administration en utilisant à nouveau Bootstrap. Jakub Tobiasz a sorti un nouveau composant nommé Twig Hooks et a également contribué à Symfony UX pour créer un layout moderne pour les besoins d’aujourd’hui.
                
                Cette stack Sylius, démarrée il y a 10 ans, a maintenant d’excellentes fonctionnalités et utilise, notamment, les nouveaux Twig components. Nous sommes à l'aube de pouvoir installer et utiliser cette stack dans une application Symfony sans inclure toute la partie E-commerce de Sylius.
                
                Ce talk introduira de nouveaux packages qui permettront d’utiliser cette nouvelle Sylius stack dans n’importe lequel de vos projets Symfony. Ainsi, vous pourrez créer des interfaces d'administration rapidement grâce aux templates génériques. De plus, vous gagnerez en rapidité pour configurer vos entités et opérations en utilisant la dernière version du Sylius Resource. La customisation ne sera pas pour autant sacrifiée, bien au contraire, et vous ne serez pas, non plus, limité à une architecture RAD rigide.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('CTE : la puissance inexploitée de votre base de données SQL')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Timothée', 'lastName' => 'MARTIN']))
            ->withDescription(<<<'TEXT'
                Avez-vous déjà vu une requête SQL contenant l’instruction “WITH” ? Si oui, vous avez eu la chance de croiser un spécimen rare : une CTE.

                Les Common Table Expressions sont une fonctionnalité méconnue des bases de données SQL. Elles permettent pourtant de réaliser des requêtes complexes de manière performante. Trop souvent nous alourdissons nos applications PHP de travail sur les données qui serait réalisé de manière bien plus efficace par le SGBDR. Les développeurs sont pourtant réticents à utiliser les fonctionnalités avancées des bases de données. De surcroît, les ORMs tels que Doctrine ne les supportent pas nativement, ce qui freine leur adoption.
                
                N’ayez crainte, les CTE ne sont ni complexes ni magiques. Reprenons les bases ensemble et vous pourrez alors exploiter ce pouvoir insoupçonné de votre base de données !
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Le grand cycle de la vie (d’une variable)')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Benoit', 'lastName' => 'VIGUIER']))
            ->withDescription(<<<'TEXT'
                En PGP, pas besoin de se préoccuper de l’allocation mémoire, le ZendEngine gère tout ça pour vous ! Malgré tout, lorsque vous exécutez des processus longs (cron, workers…), il est parfois (trop) facile de consommer beaucoup (trop) de mémoire sans s’en rendre compte… Garbage collection, références cycliques, copy on write, WeakReference… voici un rapide tour d’horizon des pièges et outils à connaître pour optimiser la gestion mémoire de vos programmes.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Resurrecting the Dead')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Juliette', 'lastName' => 'REINDERS FOLMER']))
            ->withDescription(<<<'TEXT'
                Have you ever considered what would happen when a popular open source package gets abandoned ?
                Now, what about when that package is one of your essential dependencies ?
                
                This scenario is not some abstract scenario for a far away future. Projects get abandoned every day and your dependency might be next... But you can help!
                Come & listen to some tales of an accidental maintainer and learn about what you can do to help try and prevent these situations from getting out of hand.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('L\'équipe technique vue comme un orchestre : l\'individu au service du groupe')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Adriana', 'lastName' => 'VIZINHO']))
            ->withDescription(<<<'TEXT'
                Je manage des équipes techniques depuis une quinzaine d’année mais je suis aussi, à mes heures perdues, flûtiste au sein de plusieurs orchestres depuis bien plus longtemps.

                Et j’ai remarqué au fil de toutes ces années que ces deux mondes qui peuvent sembler très éloignés à première vue, ont d’incroyables points communs.
                
                Un orchestre est souvent composé de musiciens jeunes ou plus âgées, débutants ou expérimentés, amateurs ou professionnels et d’une multitude de familles d’instrument bien différentes Une équipe technique regroupe quant à elle, un patchwork de collègues juniors ou seniors, novices ou experts ainsi que différents métiers techniques avec leurs spécificités (QA, développement, OPS, intra, network, ...)
                
                Alors, qu’est-ce qui permet à un orchestre ou une équipe technique, pourtant composés d’une multitude de profils et de caractères différents de pouvoir au final d’un côté, jouer à l’unisson ou de l’autre, livrer un projet de qualité en temps et en heure ? Quel est la part du manager ou du chef d’orchestre dans cet accomplissement ? Comment sont gérées les spécificités individuelles de chacun ? 
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('What\'s new in PHP 8.4')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Derick', 'lastName' => 'RETHANS']))
            ->withDescription(<<<'TEXT'
                During this presentation, we are going to look at the new features that were introduced in PHP 8.4, and some in earlier versions. Join me to have a look at how the type system is strengthened with Typed Class Constants, Readonly Classes, Arbitrary Static Variable Initialisers, Overloaded Method Markers, and other new smaller features and clean-ups.

                At the end you will have a good understanding about all the new and exciting features that are part of the PHP 8.4 release.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Welcome to the Age of Static Analysis and Automated Refactoring')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Matthias', 'lastName' => 'NOBACK']))
            ->withDescription(<<<'TEXT'
                Programmers live a dangerous life. It's so easy to make mistakes and mess things up, so we like to limit the scope of our changes. There may be large parts of a code base that we don't dare to touch at all. It makes us somewhat conservative when it comes to large upgrades or refactorings.

                If only we could have a tool that has our back when we make changes that influence remote parts of a code base, or that can even make those changes for us. One that tells us about mistakes immediately after we make them. And maybe even one that fixes our problems, without the risk of making a mistake.
                
                Actually, since a few years such tools exist. In this talk, I'll explain why you should use them, what its use cases are, and what amazing things are to be expected from them in the near future.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('How to Eliminate Waste in your Development Process')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Matthias', 'lastName' => 'NOBACK']))
            ->withDescription(<<<'TEXT'
                It looks like we're productive when we sit behind the keyboard and type code all day, joining the occasional meeting to shuffle some post-its. But somewhere in the process we are wasting a lot of time and energy, because it still feels like we're slow to deliver. We drag unfinished tasks from sprint to sprint, we keep increasing the number of "story points", we hide stakeholder value in branches that are "almost ready to be merged", we are fixing merge conflicts on a daily basis, and have a pipeline that tends to fail after 30 minutes.

                If you feel like giving up already, hang on! There's a set of practices that will bring back some light in the lives of developers. I'm thinking Mob Programming, Mikado Method, Continuous Delivery, TDD. Let's see how they fit together and how they can prevent a lot of waste in all of our software development processes.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('L’aventure d’une requête HTTP — ou le chemin de la vie des devs')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Pascal', 'lastName' => 'MARTIN']))
            ->withDescription(<<<'TEXT'
                À la fin de mon stage de Master et avant le début de mon premier CDI, le chef de mon chef m’a demandé : « On a un gros projet PHP qui arrive… Tu connais PHP ? ». Mon site perso était en PHP, je croyais que je connaissais bien… Quelques mois plus tard, j’avais utilisé des frameworks et un ORM. J’avais découvert le SQL, les DTO, le MVC et SOAP. Et codé en Javascript et écrit du CSS, subi des tests de charge et découvert le profiling. Vingt ans et des centaines de découvertes plus tard, je suis convaincu que notre métier est d’apprendre — et de choisir où concentrer nos efforts. Aujourd’hui, nous allons suivre le périple d’une requête HTTP émise vers votre application. En chemin, nous rencontrerons de belles technologies, étudierons certains de leurs cas d’usage, éviterons des pièges parfois mortels, et vivrons de belles aventures ! J’espère vous aider à saisir à quel point notre métier est un assemblage de multiples compétences et à choisir les prochaines technologies avec lesquelles vous voudrez expérimenter. Vous en tirerez aussi peut-être des voies d’améliorations pour vos projets… Et des idée de prochaines étapes pour votre carrière.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Les objets paresseux en PHP')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Nicolas', 'lastName' => 'GREKAS']))
            ->withDescription(<<<'TEXT'
                Depuis des lustres, vous utilisez certainement des objets paresseux dans vos projets à travers Doctrine ou Symfony sans le savoir. Le sujet est ardu techniquement, et pourtant fondamental dès qu'on développe des applications non triviales. Lors de cette conférence, je vous propose de plonger dans les mécanismes bas niveaux qui permettent d'obtenir ce comportement, lequel permet de ne pas initialiser un objet tant que ce n'est pas indispensable. Ne vous y trompez pas, le but n'est pas de vous en servir au quotidien, mais plutôt d'approfondir les aspects aussi bien pratiques que théoriques du sujet pour le plaisir, et pour le jour où vous en aurez besoin en direct s'il se présente.
                TEXT
            )
            ->create()
        ;

        TalkFactory::new()
            ->withTitle('Les nouveautés dans Symfony 7.x')
            ->withSpeaker(SpeakerFactory::findOrCreate(['firstName' => 'Nicolas', 'lastName' => 'GREKAS']))
            ->withDescription(<<<'TEXT'
                Avec la récente publication de Symfony 7.0, un nouveau cycle de développement vient de s'ouvrir pour votre framework PHP favori. Avec PHP 8.2 comme version minimale, voici au passage l'occasion pour la communauté d'explorer les nouvelles possibilités offertes par le langage. Mais que s'est-il passé en un an ? Lors de cette conférence, je vous propose une rétrospective des améliorations apportées à Symfony. Nouveaux composants, améliorations des composants existants, mais aussi nouvelles pratiques et dépréciations associées, etc. De belles sources d'inspiration pour vos projets en perspective.
                TEXT
            )
            ->create()
        ;
    }
}
