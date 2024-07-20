<?php

namespace App\Entity;

use App\Repository\SpeakerAvatarRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: SpeakerAvatarRepository::class)]
class SpeakerAvatar extends File
{
    #[Vich\UploadableField(mapping: 'speaker_avatar', fileNameProperty: 'path')]
    #[\Symfony\Component\Validator\Constraints\File(maxSize: '6000000', mimeTypes: ['image/*'])]
    protected ?\SplFileInfo $file = null;
}
