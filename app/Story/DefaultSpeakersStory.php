<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\SpeakerFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Zenstruck\Foundry\Story;

final class DefaultSpeakersStory extends Story
{
    public function __construct(
        #[Autowire(value: '%kernel.project_dir%/tests/Resources/avatars/speakers')]
        private string $avatarBaseDir,
    ) {
    }

    public function build(): void
    {
        SpeakerFactory::new()
            ->withFirstName('Adriana')
            ->withLastName('VIZINHO')
            ->withCompanyName('Deezer')
            ->withAvatar($this->avatarBaseDir . '/3308.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Albane')
            ->withLastName('VEYRON')
            ->withCompanyName('IDEME')
            ->withAvatar($this->avatarBaseDir . '/3253.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Alexandre')
            ->withLastName('DAUBOIS')
            ->withCompanyName('norsys')
            ->withAvatar($this->avatarBaseDir . '/3197.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Alexandre')
            ->withLastName('MORGAUT')
            ->withCompanyName('Capgemini')
            ->withAvatar($this->avatarBaseDir . '/3276.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Antoine')
            ->withLastName('BLUCHET')
            ->withCompanyName('Les Tilleuls')
            ->withAvatar($this->avatarBaseDir . '/3327.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Audrey')
            ->withLastName('BROUARD')
            ->withCompanyName('Harmonie Mutuelle')
            ->withAvatar($this->avatarBaseDir . '/3273.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Benoit')
            ->withLastName('VIGUIER')
            ->withCompanyName('Lendable')
            ->withAvatar($this->avatarBaseDir . '/3221.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Charles')
            ->withLastName('FOL')
            ->withCompanyName('Lendable')
            ->withAvatar($this->avatarBaseDir . '/3241.png')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Cyrille')
            ->withLastName('COQUARD')
            ->withCompanyName('WP Média')
            ->withAvatar($this->avatarBaseDir . '/3315.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('David')
            ->withLastName('BUROS')
            ->withCompanyName('Monsieur Biz')
            ->withAvatar($this->avatarBaseDir . '/3246.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Derick')
            ->withLastName('RETHANS')
            ->withAvatar($this->avatarBaseDir . '/3351.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Fabien')
            ->withLastName('PAITRY')
            ->withCompanyName('Yousign')
            ->withAvatar($this->avatarBaseDir . '/3196.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Felix')
            ->withLastName('EYMONOT')
            ->withAvatar($this->avatarBaseDir . '/3275.png')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Gina')
            ->withLastName('BANYARD')
            ->withCompanyName('The PHP Foundation')
            ->withAvatar($this->avatarBaseDir . '/3214.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Grégoire')
            ->withLastName('PINEAU')
            ->withCompanyName('JoliCode')
            ->withAvatar($this->avatarBaseDir . '/3198.jpeg')
            ->create()
        ;


        SpeakerFactory::new()
            ->withFirstName('Iana')
            ->withLastName('IATSUN')
            ->withCompanyName('IA Parle')
            ->withAvatar($this->avatarBaseDir . '/3225.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Joël')
            ->withLastName('WURTZ')
            ->withCompanyName('JoliCode')
            ->withAvatar($this->avatarBaseDir . '/3295.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Julien')
            ->withLastName('JOYE')
            ->withCompanyName('ekino')
            ->withAvatar($this->avatarBaseDir . '/3312.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Julien')
            ->withLastName('MERCIER-ROJAS')
            ->withCompanyName('Jeckel-Lab ')
            ->withAvatar($this->avatarBaseDir . '/3291.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Juliette')
            ->withLastName('REINDERS FOLMER')
            ->withCompanyName('Advies en zo')
            ->withAvatar($this->avatarBaseDir . '/3346.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Kévin')
            ->withLastName('DUNGLAS')
            ->withCompanyName('Les-Tilleuls.coop')
            ->withAvatar($this->avatarBaseDir . '/3243.png')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Loïc')
            ->withLastName('FRÉMONT')
            ->withCompanyName('Akawaka')
            ->withAvatar($this->avatarBaseDir . '/3329.png')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Marion')
            ->withLastName('AGÉ')
            ->withCompanyName('Les-Tilleuls.coop')
            ->withAvatar($this->avatarBaseDir . '/3239.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Martin')
            ->withLastName('SUPIOT')
            ->withCompanyName('Alma')
            ->withAvatar($this->avatarBaseDir . '/3211.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Mathieu')
            ->withLastName('NOËL')
            ->withAvatar($this->avatarBaseDir . '/3320.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Matthias')
            ->withLastName('NOBACK')
            ->withAvatar($this->avatarBaseDir . '/3352.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Nerea')
            ->withLastName('ENRIQUE')
            ->withCompanyName('ekino')
            ->withAvatar($this->avatarBaseDir . '/3251.png')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Nicolas')
            ->withLastName('GREKAS')
            ->withCompanyName('Symfony SAS')
            ->withAvatar($this->avatarBaseDir . '/3356.jpeg')
            ->create()
        ;


        SpeakerFactory::new()
            ->withFirstName('Olivier')
            ->withLastName('DOLBEAU')
            ->withAvatar($this->avatarBaseDir . '/3301.png')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Pascal')
            ->withLastName('MARTIN')
            ->withCompanyName('Bedrock')
            ->withAvatar($this->avatarBaseDir . '/3354.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Pauline')
            ->withLastName('VOS')
            ->withAvatar($this->avatarBaseDir . '/3358.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Smaïne')
            ->withLastName('MILIANNI')
            ->withCompanyName('Yousign')
            ->withAvatar($this->avatarBaseDir . '/3192.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Thierry')
            ->withLastName('KAUFFMANN')
            ->withCompanyName('Arawa')
            ->withAvatar($this->avatarBaseDir . '/3203.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Timothée')
            ->withLastName('MARTIN')
            ->withCompanyName('Widop')
            ->withAvatar($this->avatarBaseDir . '/3217.jpeg')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Vincent')
            ->withLastName('BONTEMS')
            ->withAvatar($this->avatarBaseDir . '/3357.png')
            ->create()
        ;

        SpeakerFactory::new()
            ->withFirstName('Vincent')
            ->withLastName('LEPOT')
            ->withAvatar($this->avatarBaseDir . '/3234.jpeg')
            ->create()
        ;
    }
}
