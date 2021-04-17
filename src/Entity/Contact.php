<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class Contact
{

  /**
   * @var string
   *@Assert\Length(min=2, max=255, minMessage="Titre trop court", maxMessage="Titre trop long")
   */
  public $customer;

  /**
   * @var string
   * @Assert\Email()
   */
  public $email;

  /**
   * @var string
   *@Assert\Length(min=2, max=255, minMessage="Titre trop court", maxMessage="Titre trop long")
   */
  public $subject;

  /**
   * @var integer
   */
  public $budget;

  /**
   * @var string
   *@Assert\Length(min=10, minMessage="Contenu trop court")
   */
  public $challenge;


}
