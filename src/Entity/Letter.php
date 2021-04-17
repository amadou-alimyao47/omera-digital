<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class Letter
{

  /**
   * @var string
   * @Assert\Length(min=1, max=255, minMessage="Titre trop court", maxMessage="Titre trop long")
   */
  public $subject;

  /**
   * @var string
   *@Assert\Length(min=10, minMessage="Contenu trop court")
   */
  public $content;

}
